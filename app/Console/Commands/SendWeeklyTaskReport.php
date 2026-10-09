<?php

namespace App\Console\Commands;

use App\Mail\RapportHebdomadaireMail;
use App\Models\Company;
use App\Models\User;
use App\Traits\RapportHebdomadaire;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendWeeklyTaskReport extends Command
{
    use RapportHebdomadaire;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send-weekly-task-report {--week= : Semaine ISO au format YYYY-Www (ex: 2026-W36). Par défaut la semaine courante} {--email= : Adresse e-mail de test}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envoie le rapport hebdomadaire des tâches au format PDF par e-mail.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Démarrage de l\'envoi des rapports hebdomadaires...');

        $companies = Company::active()->get();

        if ($companies->isEmpty()) {
            $this->warn('Aucune société active trouvée.');
            return Command::SUCCESS;
        }

        $semaine = $this->option('week') ?: null;

        foreach ($companies as $company) {
            $this->line("--- Société : {$company->company_name} (ID: {$company->id}) ---");

            // Définir la société dans la session pour les helpers company()
            session(['company' => $company]);

            try {
                // 1. Calcul du rapport hebdomadaire global pour le cabinet (limiteA = null)
                $rapport = $this->rapportHebdomadaire($semaine, null);

                // 2. Préparation du logo pour dompdf
                $logo = null;
                $url = companyOrGlobalSetting()->logo_url ?? $company->logo_url;

                if ($url && !str_contains($url, 'worksuite-logo')) {
                    $chemin = public_path(parse_url($url, PHP_URL_PATH) ?? '');
                    $logo = is_file($chemin) ? $chemin : $url;
                }

                // 3. Génération du PDF via dompdf
                $pdf = app('dompdf.wrapper');
                $pdf->loadView('reports.tasks.pdf.hebdomadaire', [
                    'rapport' => $rapport,
                    'societe' => $company,
                    'pourQui' => null,
                    'logo'    => $logo,
                ]);
                $pdf->setPaper('a4', 'portrait');
                $pdfContent = $pdf->output();

                // =========================================================================
                // CONFIGURATION DE TEST TEMPORAIRE : Envoi UNIQUEMENT à bleibaulouh@gmail.com
                // =========================================================================
                $testEmail = $this->option('email') ?: 'bleibaulouh@gmail.com';
                $virtualUser = new User();
                $virtualUser->name = 'Destinataire Test (' . $testEmail . ')';
                $virtualUser->email = $testEmail;
                $virtualUser->company_id = $company->id;
                $recipients = collect([$virtualUser]);

                /* -------------------------------------------------------------------------
                 * CONFIGURATION DE PRODUCTION (À RÉACTIVER APRÈS LE TEST) :
                 * -------------------------------------------------------------------------
                $recipients = User::allAdmins($company->id);

                $specificEmails = [
                    'constant.keyman@dcknowing.com',
                    'donalde.keyman@dcknowing.com',
                ];

                $specificUsers = User::whereIn('email', $specificEmails)->get();
                $recipients = $recipients->concat($specificUsers);

                // Ajout sous forme de compte virtuel si non trouvé en base (garantie de réception)
                foreach ($specificEmails as $email) {
                    if (!$recipients->contains('email', $email)) {
                        $virtualUser = new User();
                        $virtualUser->name = explode('@', $email)[0];
                        $virtualUser->email = $email;
                        $virtualUser->company_id = $company->id;
                        $recipients->push($virtualUser);
                    }
                }

                // Dédoublonnage par adresse e-mail
                $recipients = $recipients->unique('email')->filter(function ($u) {
                    return !empty($u->email);
                });
                ------------------------------------------------------------------------- */

                if ($recipients->isEmpty()) {
                    $this->warn("Aucun destinataire trouvé pour {$company->company_name}.");
                    continue;
                }

                // 5. Envoi individuel par e-mail avec pièce jointe PDF
                $sentCount = 0;
                foreach ($recipients as $recipient) {
                    if (empty($recipient->email)) {
                        continue;
                    }

                    try {
                        Mail::to($recipient->email)->send(
                            new RapportHebdomadaireMail($rapport, $recipient, $company, $pdfContent)
                        );
                        $sentCount++;
                        $this->info("✓ Rapport envoyé avec succès à {$recipient->name} <{$recipient->email}>");
                    } catch (\Throwable $e) {
                        Log::error("Erreur lors de l'envoi du rapport hebdomadaire à {$recipient->email} : " . $e->getMessage(), [
                            'recipient_id' => $recipient->id ?? null,
                            'company_id'   => $company->id,
                            'exception'    => $e,
                        ]);
                        $this->error("✗ Échec de l'envoi à {$recipient->email} : {$e->getMessage()}");
                    }
                }

                $this->info("Total {$sentCount}/{$recipients->count()} e-mail(s) envoyé(s) pour {$company->company_name}.");

            } catch (\Throwable $e) {
                Log::error("Erreur générale lors de la génération du rapport pour {$company->company_name} : " . $e->getMessage(), [
                    'company_id' => $company->id,
                    'exception'  => $e,
                ]);
                $this->error("Erreur pour {$company->company_name} : " . $e->getMessage());
            }
        }

        $this->info('Traitement des rapports hebdomadaires terminé.');
        return Command::SUCCESS;
    }
}
