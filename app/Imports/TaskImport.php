<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

/**
 * Lecture du fichier d'import des taches.
 *
 * A la difference des autres imports du projet, les colonnes ne sont
 * pas mises en correspondance par l'utilisateur : le modele officiel
 * fixe leur ordre et leur intitule. Cela permet une validation ligne a
 * ligne precise, que l'assistant de correspondance ne permettait pas.
 */
class TaskImport implements ToArray, WithCustomCsvSettings
{

    /* Le fichier depose, quand on le connait : les reglages CSV se
       deduisent de son contenu, pas d'une configuration figee. */
    private ?string $chemin;

    public function __construct(?string $chemin = null)
    {
        $this->chemin = $chemin;
    }

    /* Plafond par fichier : l'import est synchrone, on ne veut
       pas bloquer la requete indefiniment. La constante vit ici
       et non dans le trait : PHP interdit d'acceder a une
       constante de trait depuis l'exterieur. */
    public const MAXIMUM_LIGNES = 500;

    /**
     * Colonnes du modele, dans l'ordre exact du fichier.
     *
     * « cle » sert au code, « nom » figure dans l'en-tete du modele.
     *
     * « anciens » recense les intitules d'autrefois, encore acceptes a
     * la lecture. Renommer une colonne du modele ne doit pas rejeter
     * les fichiers deja remplis par les equipes : ils resteraient
     * valides des semaines apres le changement.
     */
    public static function colonnes(): array
    {
        return [
            [
                'cle'     => 'heading',
                'nom'     => 'Tâche',
                'requis'  => true,
                'anciens' => [
                    'Titre', 'Titre de la tâche', 'Titre de la tache', 'Nom de la tâche',
                    'Nom de la tache', 'Nom', 'Libellé', 'Libelle', 'Heading', 'Task',
                    'Task name', 'Title', 'Taches', 'Tâches', 'Intitulé', 'Intitule',
                    'Objet', 'Sujet', 'Nom tâche', 'Nom tache'
                ],
            ],
            [
                'cle'     => 'project',
                'nom'     => 'Projet',
                'requis'  => false,
                'anciens' => [
                    'Project', 'Nom du projet', 'Nom projet', 'Projets', 'Projects', 'Code projet'
                ],
            ],
            [
                'cle'     => 'start_date',
                'nom'     => 'Date de début',
                'requis'  => true,
                'anciens' => [
                    'Date de debut', 'Date début', 'Date debut', 'Début', 'Debut',
                    'Start date', 'Start_date', 'Start Date', 'StartDate', 'Date',
                    'Date de début de la tâche', 'Date debut tache', 'Date de départ'
                ],
            ],
            [
                'cle'     => 'due_date',
                'nom'     => 'Date de fin',
                'requis'  => false,
                'anciens' => [
                    'Date de fin', 'Date fin', 'Date Fin', 'Fin', 'Due date', 'Due_date',
                    'Due Date', 'DueDate', 'Date d\'échéance', 'Date d\'echeance', 'Échéance',
                    'Echeance', 'Date limite', 'Date de fin prévue', 'Date fin prévue'
                ],
            ],
            [
                'cle'     => 'status',
                'nom'     => 'Statut',
                'requis'  => false,
                'anciens' => [
                    'Status', 'État', 'Etat', 'Colonne', 'Statut de la tâche', 'Statut tâche'
                ],
            ],
            [
                'cle'     => 'assigned_to',
                'nom'     => 'Assigné à',
                'requis'  => false,
                'anciens' => [
                    'Assigne a', 'Assigné', 'Assigne', 'Assignee', 'Assignees', 'Collaborateur',
                    'Collaborateurs', 'Responsable', 'Assign to', 'Assigned to', 'Assigned To',
                    'Membre', 'Membres', 'User', 'Users', 'Employé', 'Employe'
                ],
            ],
            [
                'cle'     => 'category',
                'nom'     => 'Domaines',
                'requis'  => false,
                'anciens' => [
                    'Domaine', 'Catégorie', 'Categorie', 'Catégorie de tâche', 'Categorie de tache',
                    'Category', 'Task Category', 'Domaines', 'Catégories', 'Categories'
                ],
            ],
            [
                'cle'     => 'description',
                'nom'     => 'Description',
                'requis'  => false,
                'anciens' => [
                    'Détails', 'Details', 'Desc', 'Commentaire', 'Commentaires',
                    'Notes', 'Remarques', 'Contenu', 'Corps'
                ],
            ],
        ];
    }

    /** Intitules attendus dans la premiere ligne du fichier. */
    public static function entetes(): array
    {
        return array_column(self::colonnes(), 'nom');
    }

    /** Tous les intitules qu'une colonne accepte : l'actuel et les anciens. */
    public static function intitulesAcceptes(array $colonne): array
    {
        return array_merge([$colonne['nom']], $colonne['anciens'] ?? []);
    }

    public function array(array $rows): array
    {
        return $rows;
    }

    /**
     * Reglages CSV deduits du fichier lui-meme.
     *
     * « Enregistrer sous > CSV » d'Excel en configuration francaise
     * produit un fichier separe par des points-virgules et encode en
     * Windows-1252. Lu avec la virgule et l'UTF-8 de la configuration,
     * il n'a plus qu'une seule colonne, et l'import annonce que toutes
     * les colonnes manquent — un message incomprehensible pour qui
     * vient d'exporter le modele officiel.
     *
     * Ces reglages ne servent qu'aux CSV : un xlsx les ignore.
     */
    public function getCsvSettings(): array
    {
        return [
            'delimiter'      => $this->delimiteur(),
            'input_encoding' => $this->encodage(),
        ];
    }

    /**
     * Le separateur le plus present dans la ligne d'en-tete.
     *
     * On compte sur l'en-tete plutot que sur tout le fichier : une
     * description peut contenir des virgules ou des points-virgules a
     * foison, l'en-tete non.
     */
    private function delimiteur(): string
    {
        $entete = $this->premiereLigne();

        if ($entete === '') {
            return ',';
        }

        $comptes = [
            ';'  => substr_count($entete, ';'),
            ','  => substr_count($entete, ','),
            "	" => substr_count($entete, "	"),
        ];

        arsort($comptes);
        $meilleur = array_key_first($comptes);

        return $comptes[$meilleur] > 0 ? $meilleur : ',';
    }

    /** UTF-8 s'il est valide, Windows-1252 sinon — le defaut d'Excel. */
    private function encodage(): string
    {
        $entete = $this->premiereLigne();

        return ($entete === '' || mb_check_encoding($entete, 'UTF-8'))
            ? 'UTF-8' : 'Windows-1252';
    }

    private function premiereLigne(): string
    {
        if (!$this->chemin || !is_readable($this->chemin)) {
            return '';
        }

        $poignee = @fopen($this->chemin, 'r');

        if (!$poignee) {
            return '';
        }

        $ligne = (string) fgets($poignee, 8192);
        fclose($poignee);

        return $ligne;
    }

}
