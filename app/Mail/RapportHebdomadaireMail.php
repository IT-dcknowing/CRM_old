<?php

namespace App\Mail;

use App\Models\Company;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RapportHebdomadaireMail extends Mailable
{
    use Queueable, SerializesModels;

    public $rapport;
    public $admin;
    public $company;
    public $pdfContent;
    public $logoUrlDark;
    public $logoPath;

    /**
     * Create a new message instance.
     */
    public function __construct(object $rapport, User $admin, Company $company, string $pdfContent)
    {
        $this->rapport = $rapport;
        $this->admin = $admin;
        $this->company = $company;
        $this->pdfContent = $pdfContent;

        // Préparation du logo pour l'e-mail
        $logoUrl = companyOrGlobalSetting()->logo_url ?? $company->logo_url;
        $this->logoUrlDark = $logoUrl;
        $this->logoPath = null;

        if ($logoUrl && !str_contains($logoUrl, 'worksuite-logo')) {
            $localPath = public_path(parse_url($logoUrl, PHP_URL_PATH) ?? '');
            if (is_file($localPath)) {
                $this->logoPath = $localPath;
            }
        }
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $dateDebut = $this->rapport->debut->format('d/m/Y');
        $dateFin = $this->rapport->fin->format('d/m/Y');
        $subject = '[' . $this->company->company_name . '] Rapport hebdomadaire des tâches — Semaine du ' . $dateDebut . ' au ' . $dateFin;

        $pdfFilename = 'rapport-hebdomadaire-' . $this->rapport->debut->format('Y-m-d') . '.pdf';

        return $this->view('emails.rapport_hebdomadaire', [
            'rapport'     => $this->rapport,
            'admin'       => $this->admin,
            'company'     => $this->company,
            'logoUrlDark' => $this->logoUrlDark,
            'logoPath'    => $this->logoPath,
            'urlRapport'  => route('task-report.hebdomadaire', ['semaine' => $this->rapport->semaineIso ?? null]),
        ])
        ->subject($subject)
        ->attachData($this->pdfContent, $pdfFilename, [
            'mime' => 'application/pdf',
        ]);
    }
}
