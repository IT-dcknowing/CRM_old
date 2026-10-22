<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Restaure les index et cles primaires perdus par la base de production.
 *
 * La migration d'origine (2018_01_01_000000_create_worksuite_new_table)
 * declarait ces index et cles primaires, mais la base actuelle ne les a plus :
 * tasks, task_users, user_permissions, projects... n'ont que leur cle
 * primaire, et role_user, permission_role et notifications n'en ont aucune.
 * Chaque requete HTTP lit pourtant user_permissions par user_id (65 000
 * lignes parcourues en entier), et le tableau des taches joint tasks,
 * task_users et projects sans index.
 *
 * On ne restaure que les index des chemins critiques (taches, permissions,
 * projets, notifications, sessions). Aucune cle etrangere n'est ajoutee : la
 * base contient peut-etre des lignes orphelines, et une contrainte les ferait
 * echouer.
 *
 * La migration est idempotente :
 *   - un index est ignore si un index existant commence deja par les memes
 *     colonnes (il serait redondant) ;
 *   - une cle primaire n'est ajoutee que si la table n'en a pas et que les
 *     colonnes ne contiennent ni doublon ni NULL. Sinon elle est ignoree et
 *     l'anomalie est journalisee, sans faire echouer le deploiement.
 *
 * down() ne supprime que les index crees ici (prefixe idx_restaure_). Les cles
 * primaires restent : elles faisaient partie du schema d'origine, et down() ne
 * peut pas savoir si elles existaient deja sur une autre base.
 */
return new class extends Migration
{

    private const PREFIXE = 'idx_restaure_';

    /** Cles primaires du schema d'origine. */
    private array $clesPrimaires = [
        'role_user' => ['user_id', 'role_id'],
        'permission_role' => ['permission_id', 'role_id'],
        'notifications' => ['id'],
    ];

    /**
     * Index a restaurer : table => [suffixe du nom => colonnes].
     * Les index composes placent en tete la colonne la plus filtrante des
     * requetes reelles (company_id via CompanyScope, user_id pour les droits).
     */
    private array $index = [
        'user_permissions' => [
            'user_permission' => ['user_id', 'permission_id'],
            'permission' => ['permission_id'],
            'permission_type' => ['permission_type_id'],
        ],
        'permission_role' => [
            'role' => ['role_id'],
        ],
        'role_user' => [
            'role' => ['role_id'],
        ],
        'tasks' => [
            'company_board_column' => ['company_id', 'board_column_id'],
            'project' => ['project_id'],
            'task_category' => ['task_category_id'],
            'milestone' => ['milestone_id'],
            'recurring_task' => ['recurring_task_id'],
            'dependent_task' => ['dependent_task_id'],
            'added_by' => ['added_by'],
            'created_by' => ['created_by'],
            'due_date' => ['due_date'],
            'start_date' => ['start_date'],
        ],
        'task_users' => [
            'task_user' => ['task_id', 'user_id'],
            'user' => ['user_id'],
        ],
        'task_history' => [
            'task' => ['task_id'],
            'user' => ['user_id'],
        ],
        'mention_users' => [
            'task' => ['task_id'],
            'user' => ['user_id'],
        ],
        'projects' => [
            'company' => ['company_id'],
            'client' => ['client_id'],
            'project_admin' => ['project_admin'],
            'category' => ['category_id'],
            'deleted_at' => ['deleted_at'],
        ],
        'project_members' => [
            'project_user' => ['project_id', 'user_id'],
            'user' => ['user_id'],
        ],
        'project_activity' => [
            'project' => ['project_id'],
        ],
        'users' => [
            'company' => ['company_id'],
        ],
        'permissions' => [
            'module' => ['module_id'],
        ],
        'module_settings' => [
            'company_type' => ['company_id', 'type'],
        ],
        'notifications' => [
            'notifiable' => ['notifiable_type', 'notifiable_id'],
        ],
        'sessions' => [
            'user' => ['user_id'],
            'last_activity' => ['last_activity'],
        ],
        'universal_search' => [
            'company' => ['company_id'],
        ],
        'user_activities' => [
            'user' => ['user_id'],
        ],
        'pro_notification_logs' => [
            'task' => ['task_id'],
            'project' => ['project_id'],
        ],
    ];

    public function up(): void
    {
        foreach ($this->clesPrimaires as $table => $colonnes) {
            $this->ajouterClePrimaire($table, $colonnes);
        }

        foreach ($this->index as $table => $definitions) {
            foreach ($definitions as $suffixe => $colonnes) {
                $this->ajouterIndex($table, self::PREFIXE . $suffixe, $colonnes);
            }
        }
    }

    public function down(): void
    {
        foreach ($this->index as $table => $definitions) {
            foreach (array_keys($definitions) as $suffixe) {
                $nom = self::PREFIXE . $suffixe;

                if (Schema::hasTable($table) && $this->indexExiste($table, $nom)) {
                    DB::statement("ALTER TABLE `{$table}` DROP INDEX `{$nom}`");
                }
            }
        }
    }

    private function ajouterClePrimaire(string $table, array $colonnes): void
    {
        if (!$this->colonnesPresentes($table, $colonnes) || $this->indexExiste($table, 'PRIMARY')) {
            return;
        }

        $liste = implode(', ', array_map(fn ($c) => "`{$c}`", $colonnes));

        $doublons = DB::selectOne(
            "SELECT COUNT(*) AS n FROM (SELECT 1 FROM `{$table}` GROUP BY {$liste} HAVING COUNT(*) > 1) AS d"
        )->n;

        $nuls = DB::table($table)->where(function ($q) use ($colonnes) {
            foreach ($colonnes as $colonne) {
                $q->orWhereNull($colonne);
            }
        })->count();

        if ($doublons > 0 || $nuls > 0) {
            Log::warning("Migration index : cle primaire de {$table} non ajoutee ({$doublons} doublon(s), {$nuls} NULL).");

            return;
        }

        DB::statement("ALTER TABLE `{$table}` ADD PRIMARY KEY ({$liste})");
    }

    private function ajouterIndex(string $table, string $nom, array $colonnes): void
    {
        if (!$this->colonnesPresentes($table, $colonnes)
            || $this->indexExiste($table, $nom)
            || $this->prefixeDejaIndexe($table, $colonnes)
        ) {
            return;
        }

        $liste = implode(', ', array_map(fn ($c) => "`{$c}`", $colonnes));

        DB::statement("ALTER TABLE `{$table}` ADD INDEX `{$nom}` ({$liste})");
    }

    private function colonnesPresentes(string $table, array $colonnes): bool
    {
        return Schema::hasTable($table) && Schema::hasColumns($table, $colonnes);
    }

    private function indexExiste(string $table, string $nom): bool
    {
        return DB::table('information_schema.statistics')
            ->where('table_schema', DB::getDatabaseName())
            ->where('table_name', $table)
            ->where('index_name', $nom)
            ->exists();
    }

    /** Vrai si un index existant commence deja par ces colonnes, dans cet ordre. */
    private function prefixeDejaIndexe(string $table, array $colonnes): bool
    {
        $index = DB::table('information_schema.statistics')
            ->where('table_schema', DB::getDatabaseName())
            ->where('table_name', $table)
            // Alias explicites : MySQL 8 renvoie ces colonnes en majuscules.
            ->selectRaw('index_name AS nom, column_name AS colonne, seq_in_index AS rang')
            ->orderBy('nom')
            ->orderBy('rang')
            ->get()
            ->groupBy('nom')
            ->map(fn ($lignes) => $lignes->pluck('colonne')->all());

        foreach ($index as $colonnesIndex) {
            if (array_slice($colonnesIndex, 0, count($colonnes)) === $colonnes) {
                return true;
            }
        }

        return false;
    }

};
