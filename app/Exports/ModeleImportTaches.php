<?php

namespace App\Exports;

use App\Imports\TaskImport;
use App\Models\TaskboardColumn;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Modele Excel d'import des taches.
 *
 * Genere a la demande plutot que stocke comme fichier : les statuts,
 * les categories et les projets proposes suivent ainsi la
 * configuration reelle du cabinet, sans modele a maintenir a la main.
 *
 * Le classeur porte un second onglet, « Listes », masque, d'ou les
 * colonnes Projet, Statut et Domaine tirent des menus deroulants.
 * L'utilisateur connecte est automatiquement assigne a toutes ses
 * taches importees : la colonne « Assigne a » n'existe plus.
 */
class ModeleImportTaches implements FromArray, WithHeadings, WithStyles, WithEvents
{

    /* Les lignes du modele sur lesquelles les menus sont poses. Au-dela,
       l'import refuse le fichier de toute facon. */
    private const DERNIERE_LIGNE = TaskImport::MAXIMUM_LIGNES + 1;

    private array $projets;
    private array $categories;
    private array $statuts;

    public function __construct()
    {
        $societe = company()->id;

        $this->projets = DB::table('projects')->whereNull('deleted_at')
            ->where('company_id', $societe)
            ->orderBy('project_name')->pluck('project_name')->all();

        /* L'assignation est desormais automatique : l'utilisateur connecte
           est toujours l'assigne. La liste des collaborateurs n'est plus
           necessaire dans le modele. */
        $this->categories = DB::table('task_category')
            ->where('company_id', $societe)
            ->orderBy('category_name')->pluck('category_name')->all();

        $this->statuts = TaskboardColumn::orderBy('priority')
            ->pluck('column_name')->all();
    }

    public function headings(): array
    {
        return TaskImport::entetes();
    }

    public function array(): array
    {
        /* Un exemple bati sur des valeurs qui existent vraiment : une
           ligne inventee serait refusee a l'import et donnerait le
           sentiment d'un outil defaillant. */
        $format = company()->date_format;

        return [
            [
                'Exemple : préparer la déclaration TVA',
                $this->projets[0] ?? '',
                now()->format($format),
                now()->addDays(7)->format($format),
                TaskboardColumn::where('slug', 'to_do')->value('column_name') ?: 'À faire',
                $this->categories[0] ?? '',
                'Ligne d\'exemple : supprimez-la avant d\'importer.',
            ],
        ];
    }

    public function styles(Worksheet $feuille)
    {
        /* La ligne d'exemple est grisee, pour qu'on comprenne qu'elle
           n'a pas vocation a rester. */
        return [
            1 => ['font' => ['bold' => true]],
            2 => ['font' => ['italic' => true, 'color' => ['rgb' => '9CA3AF']]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $evenement) {
                $feuille = $evenement->sheet->getDelegate();
                $feuille->setTitle('Tâches');

                $this->ecrireListes($feuille->getParent());
                $this->poserMenus($feuille);

                /* 7 colonnes : A (Tâche) à G (Description). */
                foreach (range('A', 'G') as $colonne) {
                    $feuille->getColumnDimension($colonne)->setAutoSize(true);
                }

                /* Le lecteur doit ouvrir le fichier sur la saisie, pas
                   sur les listes. */
                $feuille->getParent()->setActiveSheetIndex(0);
            },
        ];
    }

    /**
     * Onglet « Listes » : une colonne par referentiel.
     *
     * Masque a la fin : il n'existe que pour alimenter les menus
     * deroulants, et Excel lit tres bien un onglet masque pour resoudre
     * ses formules de validation. Visible, il passait pour un second
     * fichier a remplir.
     */
    private function ecrireListes($classeur): void
    {
        $listes = $classeur->createSheet();
        $listes->setTitle('Listes');

        /* La colonne « Collaborateurs » a ete retiree : l'assignation est
           desormais automatique. Onglet Listes : A = Projets, B = Domaines,
           C = Statuts. */
        $colonnes = [
            'A' => ['Projets',  $this->projets],
            'B' => ['Domaines', $this->categories],
            'C' => ['Statuts',  $this->statuts],
        ];

        foreach ($colonnes as $lettre => [$titre, $valeurs]) {
            $listes->setCellValue($lettre . '1', $titre);

            foreach (array_values($valeurs) as $n => $valeur) {
                $listes->setCellValue($lettre . ($n + 2), $valeur);
            }

            $listes->getColumnDimension($lettre)->setAutoSize(true);
        }

        $listes->getStyle('A1:C1')->getFont()->setBold(true);

        /* Masque une fois rempli. Un classeur ne peut pas etre enregistre
           avec pour seul onglet visible un onglet masque : celui des
           taches l'est, l'ordre des operations est donc sans risque. */
        $listes->setSheetState(Worksheet::SHEETSTATE_HIDDEN);
    }

    /**
     * Menus deroulants sur les trois colonnes a referentiel.
     *
     * La colonne « Assigne a » a ete supprimee : l'utilisateur connecte
     * est automatiquement assigne a toutes les taches qu'il importe.
     *
     * Le projet n'impose pas sa liste : l'import cree celui qu'il ne
     * trouve pas. Statut et Domaine restent bloquants : l'import refuse
     * une valeur inconnue, autant le dire dans Excel plutot qu'a l'analyse.
     *
     * Colonnes du fichier : A Tâche · B Projet · C Date début · D Date fin
     *                       E Statut · F Domaine · G Description
     * Onglet Listes        : A Projets · B Domaines · C Statuts
     */
    private function poserMenus(Worksheet $feuille): void
    {
        $menus = [
            /* Le projet n'est pas bloquant, a la difference du statut et
               du domaine : l'import cree le projet qu'il ne connait pas.
               Une validation « stop » interdisait d'ecrire ce nom, et la
               creation restait donc hors d'atteinte depuis Excel. */
            'B' => ['liste' => $this->projets,    'colonne' => 'A', 'strict' => false,
                    'aide' => 'Choisissez un projet dans la liste, laissez vide, ou saisissez un nouveau nom : le projet sera cree a l\'import.'],
            'E' => ['liste' => $this->statuts,    'colonne' => 'C', 'strict' => true,
                    'aide' => 'Choisissez un statut. Vide, la tâche sera « Incomplète ».'],
            'F' => ['liste' => $this->categories, 'colonne' => 'B', 'strict' => true,
                    'aide' => 'Choisissez un domaine dans la liste, ou laissez vide.'],
        ];

        foreach ($menus as $cible => $menu) {
            if (!$menu['liste']) {
                continue;
            }

            $derniere = count($menu['liste']) + 1;

            $validation = new DataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            $validation->setErrorStyle(DataValidation::STYLE_STOP);
            $validation->setAllowBlank(true);
            $validation->setShowDropDown(true);
            $validation->setShowInputMessage(true);
            $validation->setPromptTitle(__('Aide à la saisie'));
            $validation->setPrompt($menu['aide']);

            /* Bloquant sur les referentiels stricts, simple aide sur les
               collaborateurs. */
            $validation->setShowErrorMessage($menu['strict']);
            $validation->setErrorTitle(__('Valeur inconnue'));
            $validation->setError(__("Choisissez une valeur de la liste déroulante."));

            $validation->setFormula1(
                sprintf('=Listes!$%s$2:$%s$%d', $menu['colonne'], $menu['colonne'], $derniere)
            );

            $feuille->setDataValidation(
                sprintf('%s2:%s%d', $cible, $cible, self::DERNIERE_LIGNE),
                $validation
            );
        }
    }

}
