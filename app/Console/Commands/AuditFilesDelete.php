<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

/**
 * Etape C du nettoyage des anciens modules (audit nettoyage-modules) :
 * suppression reelle, whitelist figee dans AuditFilesPreview::WHITELIST.
 *
 * Ne fait rien sans --confirm. Refuse tout chemin hors du projet ou hors
 * whitelist. Journalise chaque suppression.
 */
class AuditFilesDelete extends Command
{
    protected $signature = 'audit:files-delete {--confirm : Execute reellement les suppressions (sinon dry-run)}';

    protected $description = 'Supprime les fichiers de la whitelist AuditFilesPreview::WHITELIST. Dry-run par defaut.';

    public function handle(): int
    {
        $confirmed = (bool) $this->option('confirm');
        $base = realpath(base_path());

        if (!$confirmed) {
            $this->warn('Dry-run (pas de --confirm) : aucune suppression ne sera effectuee.');
        } else {
            $this->error('Mode --confirm actif : les suppressions ci-dessous seront reelles et definitives.');

            if (!$this->confirm('Une sauvegarde du projet et de la base existe-t-elle, et confirmes-tu la suppression ?', false)) {
                $this->info('Annule.');

                return self::SUCCESS;
            }
        }

        $logPath = storage_path('logs/audit-cleanup-files.log');
        $deleted = 0;
        $skipped = 0;

        foreach (AuditFilesPreview::WHITELIST as $relative) {
            $path = $base . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative);
            $real = realpath($path);

            if ($real === false) {
                $this->warn("Introuvable, ignore : {$relative}");
                $skipped++;
                continue;
            }

            // Le chemin reel doit rester strictement a l'interieur du projet.
            if (!str_starts_with($real, $base . DIRECTORY_SEPARATOR)) {
                $this->error("REFUSE (hors projet) : {$relative}");
                $skipped++;
                continue;
            }

            if (!$confirmed) {
                $this->line("[dry-run] supprimerait : {$relative}");
                continue;
            }

            $isDir = is_dir($real);

            try {
                if ($isDir) {
                    File::deleteDirectory($real);
                } else {
                    File::delete($real);
                }

                $deleted++;
                $line = date('Y-m-d H:i:s') . " SUPPRIME " . ($isDir ? '[dossier] ' : '[fichier] ') . $relative . PHP_EOL;
                File::append($logPath, $line);
                $this->info("Supprime : {$relative}");
            } catch (\Throwable $e) {
                $this->error("ECHEC ({$relative}) : {$e->getMessage()}");
                Log::channel('single')->error('audit:files-delete echec', ['path' => $relative, 'error' => $e->getMessage()]);

                // On s'arrete au premier echec plutot que de continuer a l'aveugle.
                return self::FAILURE;
            }
        }

        $this->newLine();
        $this->info("Termine. Supprimes: {$deleted}, ignores: {$skipped}. Journal : {$logPath}");
        $this->info('Restauration : git checkout -- <chemin> (fichiers versionnes) tant que rien n\'est commite.');

        return self::SUCCESS;
    }
}
