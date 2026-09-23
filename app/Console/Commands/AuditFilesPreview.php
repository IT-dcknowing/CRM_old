<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Etape C du nettoyage des anciens modules (audit nettoyage-modules) :
 * previsualisation, sans suppression. Complement de AuditFilesDelete.
 */
class AuditFilesPreview extends Command
{
    protected $signature = 'audit:files-preview';

    protected $description = 'Liste les fichiers candidats au nettoyage (anciens modules isoles) avec preuve de non-reference. Ne supprime rien.';

    /**
     * Chemins (relatifs a la racine du projet) des familles jugees isolees
     * par la cartographie du 2026-09-16 : aucune reference trouvee depuis
     * les modules conserves, aucune route active (web.php, web-settings.php,
     * api.php verifies via route:list). KnowledgeBase est deliberement
     * absente de cette liste : routes/api.php sert encore /api/knowledge-base
     * via AuthController::getKnowledgeBase().
     */
    public const WHITELIST = [
        // Notices
        'app/Http/Controllers/NoticeController.php',
        'app/Http/Controllers/NoticeFileController.php',
        'app/Observers/NoticeObserver.php',
        'app/Observers/NoticeFileObserver.php',
        'app/Observers/NoticeViewObserver.php',
        'app/Events/NewNoticeEvent.php',
        'app/Listeners/NewNoticeListener.php',
        'app/Models/Notice.php',
        'app/Models/NoticeFile.php',
        'app/Models/NoticeBoardUser.php',
        'app/Models/NoticeView.php',
        'app/DataTables/NoticeBoardDataTable.php',
        'resources/views/notices',

        // Awards / Appreciation — CORRECTION 2026-09-16 : app/Models/Award.php,
        // AwardIcon.php et Appreciation.php ont ete retires de cette liste.
        // EmployeeController.php:682-887 (onglet "Appreciations" de la fiche
        // employe, module Employes conserve) interroge ces 3 modeles
        // directement (Appreciation::with(['award','award.awardIcon',...])).
        // Les controleurs/observers/DataTables restent candidats : ils n'ont
        // aucune route live et ne sont pas utilises par cet onglet (qui
        // interroge les modeles sans passer par eux).
        // AwardObserver.php conserve (Award::class y reste rattache dans
        // EventServiceProvider, aucun event declenche par cet observer).
        'app/Http/Controllers/AwardController.php',
        'app/DataTables/AwardDataTable.php',
        'resources/views/awards',
        // AppreciationEvent/AppreciationListener conserves : AppreciationObserver
        // (garde active) les declenche sur creation (event(new AppreciationEvent(...))).
        'app/Http/Controllers/AppreciationController.php',
        'app/DataTables/AppreciationsDataTable.php',
        'app/DataTables/EmployeeAppreciationDataTable.php',
        'resources/views/appreciations',

        // Modules nwidart autonomes, aucune route live detectee (route:list verifie)
        'Modules/Recruit',
        'Modules/Sms',

        // --- Deuxieme vague (2026-09-16) : familles couplees, tentative elargie ---
        //
        // IMPORTANT : une premiere version de cette whitelist incluait aussi
        // Leads/Deals-secondaires, Invoices/Estimates/CreditNotes, Tickets,
        // Leave/Attendance et ShiftRotation. Elle a ete entierement retiree
        // apres decouverte que plusieurs commandes planifiees dans
        // app/Console/Kernel.php (executees par cron, invisibles depuis
        // route:list ou un grep de controleurs) utilisent activement ces
        // memes classes : CarryForwardLeaves, SendAttendanceReminder,
        // SendMonthlyAttendanceReport, LeavesQuotaRenew,
        // CreateEmployeeLeaveQuotaHistory, AutoCreateRecurringInvoices,
        // SendInvoiceReminder, FetchTicketEmails, AssignShiftRotation,
        // AssignEmployeeShiftRotation, SendAutoFollowUpReminder (celle-ci
        // utilise DealFollowUp, un modele qui avait ete inclus par erreur).
        // Tant qu'un audit dedie n'a pas croise CHAQUE commande de
        // app/Console/Kernel.php avec CHAQUE famille candidate, ces familles
        // restent hors whitelist. Voir docs/audit/fichiers-inutiles.md.
        //
        // Seule famille confirmee sans aucune dependance (route, controleur
        // conserve, ni commande planifiee) : Orders / Products commerciaux.

        // Orders / Products (commerciaux)
        'app/Http/Controllers/OrderController.php',
        'app/Http/Controllers/ProductCategoryController.php',
        'app/Http/Controllers/ProductController.php',
        'app/Http/Controllers/ProductFileController.php',
        'app/Http/Controllers/ProductSubCategoryController.php',
        'app/Observers/OrderObserver.php',
        'app/Observers/ProductObserver.php',
        'app/Observers/ProductCategoryObserver.php',
        'app/Observers/ProductFileObserver.php',
        'app/Observers/ProductSubCategoryObserver.php',
        'app/Events/OrderUpdatedEvent.php',
        'app/Listeners/OrderUpdatedListener.php',
        'app/Models/Order.php',
        'app/Models/OrderCart.php',
        'app/Models/OrderItemImage.php',
        'app/Models/OrderItems.php',
        'app/Models/Product.php',
        'app/Models/ProductCategory.php',
        'app/Models/ProductFiles.php',
        'app/Models/ProductSubCategory.php',
        'app/DataTables/OrdersDataTable.php',
        'app/DataTables/ProductsDataTable.php',
        'resources/views/orders',
        'resources/views/products',

    ];

    public function handle(): int
    {
        $base = base_path();
        $this->info('Previsualisation - aucune suppression. ' . count(self::WHITELIST) . ' entrees.');
        $this->newLine();

        foreach (self::WHITELIST as $relative) {
            $path = $base . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative);

            if (!file_exists($path)) {
                $this->warn("MANQUANT  {$relative}");
                continue;
            }

            $isDir = is_dir($path);
            $size = $isDir ? $this->dirSize($path) : filesize($path);
            $modified = date('Y-m-d H:i', filemtime($path));
            $className = $this->guessClassName($relative);
            $liveReference = $className ? $this->hasLiveReference($className, $path) : null;

            $flag = $liveReference ? ' /!\\ REFERENCE TROUVEE - A VERIFIER' : '';

            $this->line(sprintf(
                '%s  %s  %s  modifie %s%s',
                $isDir ? '[dossier]' : '[fichier]',
                str_pad($relative, 55),
                str_pad($this->humanSize($size), 10),
                $modified,
                $flag
            ));
        }

        $this->newLine();
        $this->info('Pour supprimer (apres relecture) : php artisan audit:files-delete --confirm');

        return self::SUCCESS;
    }

    private function guessClassName(string $relativePath): ?string
    {
        if (!str_ends_with($relativePath, '.php')) {
            return null;
        }

        $withoutExt = substr($relativePath, 0, -4);

        return basename($withoutExt);
    }

    /**
     * Grep de securite : le nom de classe est-il mentionne ailleurs que
     * dans son propre fichier (app/, routes/, resources/views/, Modules/) ?
     * Une correspondance ne prouve pas a elle seule un usage reel (peut
     * etre un commentaire, un autre sens du mot) - a verifier a la main.
     */
    private function hasLiveReference(string $className, string $ownPath): bool
    {
        $roots = ['app', 'routes', 'resources/views', 'Modules'];
        $base = base_path();

        foreach ($roots as $root) {
            $dir = $base . DIRECTORY_SEPARATOR . $root;

            if (!is_dir($dir)) {
                continue;
            }

            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS)
            );

            foreach ($iterator as $file) {
                if (!$file->isFile()) {
                    continue;
                }

                $filePath = $file->getPathname();

                if (str_starts_with($filePath, rtrim($ownPath, DIRECTORY_SEPARATOR))) {
                    continue;
                }

                // Les commandes d'audit elles-memes listent ces noms de classe
                // dans leur whitelist : faux positif systematique a ignorer.
                if (str_contains($filePath, DIRECTORY_SEPARATOR . 'Console' . DIRECTORY_SEPARATOR . 'Commands' . DIRECTORY_SEPARATOR . 'Audit')) {
                    continue;
                }

                if (!in_array($file->getExtension(), ['php', 'blade'])) {
                    continue;
                }

                $contents = file_get_contents($filePath);

                if ($contents === false) {
                    continue;
                }

                foreach (explode("\n", $contents) as $line) {
                    $trimmed = ltrim($line);

                    // Ligne entierement commentee (PHP ou Blade) : on l'ignore,
                    // sinon une route deja desactivee ressort comme "reference".
                    if (str_starts_with($trimmed, '//') || str_starts_with($trimmed, '*') || str_starts_with($trimmed, '{{--')) {
                        continue;
                    }

                    if (str_contains($line, $className)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    private function dirSize(string $dir): int
    {
        $size = 0;
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $size += $file->getSize();
            }
        }

        return $size;
    }

    private function humanSize(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 1) . ' Mo';
        }

        if ($bytes >= 1024) {
            return round($bytes / 1024, 1) . ' Ko';
        }

        return $bytes . ' o';
    }
}
