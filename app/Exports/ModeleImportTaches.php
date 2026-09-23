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
 * Le classeur porte un second onglet, « Listes », d'ou les colonnes
 * Projet, Statut, Assigne a et Domaine tirent des menus deroulants.
 * Sans lui, remplir le fichier supposait de connaitre par coeur
 * quatre-vingt-dix projets et quatre cents collaborateurs, orthographe
 * comprise — ce que personne ne peut faire.
 */
class ModeleImportTaches implements FromArray, WithHeadings, WithStyles, WithEvents
{

    /* Les lignes du modele sur lesquelles les menus sont poses. Au-dela,
       l'import refuse le fichier de toute facon. */
    private const DERNIERE_LIGNE = TaskImport::MAXIMUM_LIGNES + 1;

    private array $projets;
    private array $gens;
    private array $categories;
    private array $statuts;

    public function __construct()
    {
        $societe = company()->id;

        $this->projets = DB::table('projects')->whereNull('deleted_at')
            ->where('company_id', $societe)
            ->orderBy('project_name')->pluck('project_name')->all();

        /* Tous les comptes actifs, et non les seuls employes : l'import
           accepte deja n'importe quel utilisateur, la liste doit dire
           la meme chose que le controle. */
        $this->gens = DB::table('users')
            ->where('company_id', $societe)->where('status', 'active')
            ->orderBy('name')->pluck('name')->unique()->values()->all();

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
                $this->gens[0] ?? '',
                $this->categories[0] ?? '',
                'Ligne d’exemple : supprimez-la avant d’importer.',
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

                foreach (range('A', 'H') as $colonne) {
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
     * Il reste visible : c'est aussi la reponse a « quels projets
     * existent ? », consultable sans revenir dans l'application.
     */
    private function ecrireListes($classeur): void
    {
        $listes = $classeur->createSheet();
        $listes->setTitle('Listes');

        $colonnes = [
            'A' => ['Projets', $this->projets],
            'B' => ['Collaborateurs', $this->gens],
            'C' => ['Domaines', $this->categories],
            'D' => ['Statuts', $this->statuts],
        ];

        foreach ($colonnes as $lettre => [$titre, $valeurs]) {
            $listes->setCellValue($lettre . '1', $titre);

            foreach (array_values($valeurs) as $n => $valeur) {
                $listes->setCellValue($lettre . ($n + 2), $valeur);
            }

            $listes->getColumnDimension($lettre)->setAutoSize(true);
        }

        $listes->getStyle('A1:D1')->getFont()->setBold(true);
    }

    /**
     * Menus deroulants sur les quatre colonnes a referentiel.
     *
     * Assigne a fait exception : sa liste n'est qu'une aide, la saisie
     * reste libre, sans quoi l'on ne pourrait plus ecrire deux noms
     * separes par une virgule pour confier une tache a plusieurs
     * personnes.
     */
    private function poserMenus(Worksheet $feuille): void
    {
        $menus = [
            'B' => ['liste' => $this->projets,    'colonne' => 'A', 'strict' => true,
                    'aide' => 'Choisissez un projet dans la liste, ou laissez vide.'],
            'E' => ['liste' => $this->statuts,    'colonne' => 'D', 'strict' => true,
                    'aide' => 'Choisissez un statut. Vide, la tâche sera « Incomplète ».'],
            'F' => ['liste' => $this->gens,       'colonne' => 'B', 'strict' => false,
                    'aide' => 'Choisissez un collaborateur. Pour en confier à plusieurs, séparez les noms par une virgule.'],
            'G' => ['liste' => $this->categories, 'colonne' => 'C', 'strict' => true,
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
