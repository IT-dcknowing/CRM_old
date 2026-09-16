<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

/**
 * Etape D du nettoyage des anciens modules (audit nettoyage-modules) :
 * suppression reelle des tables, whitelist figee dans
 * AuditTablesPreview::WHITELIST.
 *
 * La suppression d'une table est destructive et non recuperable par une
 * migration down() seule. Ne fait rien sans --confirm ET --i-have-a-backup.
 */
class AuditTablesDelete extends Command
{
    protected $signature = 'audit:tables-delete
        {--confirm : Execute reellement les suppressions (sinon dry-run)}
        {--i-have-a-backup : Confirmation humaine qu\'une sauvegarde de la base existe - non verifiee techniquement}';

    protected $description = 'Supprime les tables de la whitelist AuditTablesPreview::WHITELIST. Dry-run par defaut.';

    public function handle(): int
    {
        $confirmed = (bool) $this->option('confirm');
        $hasBackupFlag = (bool) $this->option('i-have-a-backup');

        if (!$confirmed) {
            $this->warn('Dry-run (pas de --confirm) : aucune table ne sera supprimee.');
        } elseif (!$hasBackupFlag) {
            $this->error('--confirm sans --i-have-a-backup : refuse. Une sauvegarde de la base doit exister avant toute suppression de table.');

            return self::FAILURE;
        } else {
            $this->error('Mode --confirm actif : les tables ci-dessous seront supprimees definitivement.');

            if (!$this->confirm('Confirmes-tu avoir verifie la sauvegarde et vouloir continuer ?', false)) {
                $this->info('Annule.');

                return self::SUCCESS;
            }
        }

        $logPath = storage_path('logs/audit-cleanup-tables.log');
        $dropped = 0;
        $skipped = 0;

        foreach (AuditTablesPreview::WHITELIST as $table) {
            if (!Schema::hasTable($table)) {
                $this->warn("Absente, ignoree : {$table}");
                $skipped++;
                continue;
            }

            $referencedBy = DB::select(
                'SELECT TABLE_NAME FROM information_schema.KEY_COLUMN_USAGE
                 WHERE REFERENCED_TABLE_SCHEMA = DATABASE() AND REFERENCED_TABLE_NAME = ?',
                [$table]
            );

            if ($referencedBy) {
                $refs = implode(', ', array_map(fn ($r) => $r->TABLE_NAME, $referencedBy));
                $this->error("REFUSE ({$table}) : referencee par une cle etrangere depuis {$refs}. Verifier avant de forcer manuellement.");
                $skipped++;
                continue;
            }

            if (!$confirmed) {
                $this->line("[dry-run] supprimerait la table : {$table}");
                continue;
            }

            try {
                Schema::drop($table);
                $dropped++;
                File::append($logPath, date('Y-m-d H:i:s') . " DROP TABLE {$table}" . PHP_EOL);
                $this->info("Supprimee : {$table}");
            } catch (\Throwable $e) {
                $this->error("ECHEC ({$table}) : {$e->getMessage()}");

                return self::FAILURE;
            }
        }

        $this->newLine();
        $this->info("Termine. Supprimees: {$dropped}, ignorees: {$skipped}. Journal : {$logPath}");
        $this->info('Restauration : uniquement depuis la sauvegarde de base prealable - aucune migration down() ne recree les donnees supprimees.');

        return self::SUCCESS;
    }
}
