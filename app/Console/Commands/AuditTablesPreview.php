<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Etape D du nettoyage des anciens modules (audit nettoyage-modules) :
 * previsualisation des tables candidates, sans suppression.
 */
class AuditTablesPreview extends Command
{
    protected $signature = 'audit:tables-preview';

    protected $description = 'Liste les tables candidates au nettoyage (anciens modules isoles) avec comptage de lignes et cles etrangeres. Ne supprime rien.';

    /**
     * Tables des familles confirmees isolees : Notices, Awards, Appreciation,
     * Orders/Products commerciaux, modules nwidart Recruit/Sms. KnowledgeBase
     * est exclue : encore servie par /api/knowledge-base
     * (AuthController::getKnowledgeBase). Invoice/Lead/Ticket/Leave/Attendance
     * restent hors liste : au-dela du couplage ContractSettingController vers
     * InvoiceSetting, plusieurs commandes planifiees dans app/Console/Kernel.php
     * (CarryForwardLeaves, SendAttendanceReminder, AutoCreateRecurringInvoices,
     * SendInvoiceReminder, FetchTicketEmails, AssignShiftRotation...) utilisent
     * activement ces familles independamment de toute route web (voir rapport
     * docs/audit/tables-inutiles.md, section "bloquees").
     */
    public const WHITELIST = [
        'notices', 'notice_files', 'notice_board_users', 'notice_views',
        // awards/award_icons/appreciations RETIRES 2026-09-16 : utilisees par
        // EmployeeController::appreciation() (fiche employe, module conserve).
        // Orders / Products commerciaux (verifie : aucune commande planifiee,
        // aucune route live, aucune reference depuis un module conserve)
        'orders', 'order_items', 'order_item_images',
        'products', 'product_category', 'product_sub_category', 'product_files',
        // Modules/Recruit
        'application_sources', 'job_interview_stages', 'offer_letter_histories',
        'recruit_applicant_notes', 'recruit_application_files', 'recruit_application_skills',
        'recruit_application_status', 'recruit_application_status_categories',
        'recruit_candidate_database', 'recruit_candidate_follow_ups', 'recruit_custom_questions',
        'recruit_email_notification_settings', 'recruit_footer_links', 'recruit_global_settings',
        'recruit_interview_comments', 'recruit_interview_employees', 'recruit_interview_evaluations',
        'recruit_interview_files', 'recruit_interview_histories', 'recruit_interview_schedules',
        'recruit_interview_stages', 'recruit_job_addresses', 'recruit_job_alerts',
        'recruit_job_applications', 'recruit_job_categories', 'recruit_job_custom_answers',
        'recruit_job_files', 'recruit_job_histories', 'recruit_job_offer_files',
        'recruit_job_offer_letter', 'recruit_job_offer_questions', 'recruit_job_questions',
        'recruit_job_skills', 'recruit_job_sub_categories', 'recruit_job_types',
        'recruit_jobboard_settings', 'recruit_jobs', 'recruit_recommendation_statuses',
        'recruit_salary_structures', 'recruit_selected_salary_components', 'recruit_settings',
        'recruit_skills', 'recruit_work_experiences', 'recruiters',
        // Modules/Sms
        'sms_settings', 'sms_notification_settings',
    ];

    public function handle(): int
    {
        $this->info('Previsualisation - aucune suppression. ' . count(self::WHITELIST) . ' tables.');
        $this->newLine();

        foreach (self::WHITELIST as $table) {
            if (!Schema::hasTable($table)) {
                $this->warn("ABSENTE  {$table}");
                continue;
            }

            $count = DB::table($table)->count();
            $referencedBy = $this->foreignKeysReferencing($table);
            $referencesOut = $this->foreignKeysFrom($table);

            $flag = $referencedBy ? ' /!\\ REFERENCEE PAR : ' . implode(', ', $referencedBy) : '';

            $this->line(sprintf(
                '%s  %s lignes%s',
                str_pad($table, 45),
                str_pad((string) $count, 8),
                $flag
            ));

            if ($referencesOut) {
                $this->line('    -> cles etrangeres sortantes : ' . implode(', ', $referencesOut));
            }
        }

        $this->newLine();
        $this->info('Pour supprimer (apres relecture ET sauvegarde de la base) : php artisan audit:tables-delete --confirm --i-have-a-backup');

        return self::SUCCESS;
    }

    /**
     * Tables (hors whitelist) qui possedent une cle etrangere pointant
     * vers $table - un signal fort de dependance active a verifier avant
     * suppression.
     */
    private function foreignKeysReferencing(string $table): array
    {
        $connection = config('database.default');
        $database = config("database.connections.$connection.database");

        $rows = DB::select(
            'SELECT TABLE_NAME, COLUMN_NAME FROM information_schema.KEY_COLUMN_USAGE
             WHERE REFERENCED_TABLE_SCHEMA = ? AND REFERENCED_TABLE_NAME = ?',
            [$database, $table]
        );

        return array_map(fn ($r) => "{$r->TABLE_NAME}.{$r->COLUMN_NAME}", $rows);
    }

    private function foreignKeysFrom(string $table): array
    {
        $connection = config('database.default');
        $database = config("database.connections.$connection.database");

        $rows = DB::select(
            'SELECT COLUMN_NAME, REFERENCED_TABLE_NAME FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND REFERENCED_TABLE_NAME IS NOT NULL',
            [$database, $table]
        );

        return array_map(fn ($r) => "{$r->COLUMN_NAME} -> {$r->REFERENCED_TABLE_NAME}", $rows);
    }
}
