<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f5f7fa; margin: 0; padding: 0; -webkit-font-smoothing: antialiased; }
        .wrapper { width: 100%; table-layout: fixed; background-color: #f5f7fa; padding: 48px 0; }
        .container { max-width: 520px; margin: 0 auto; background-color: #ffffff; border-radius: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); overflow: hidden; }
        .header { padding: 32px 32px 0 32px; text-align: left; }
        .logo { font-size: 20px; font-weight: 700; text-decoration: none; }
        .logo-flow { color: #1e293b; }
        .logo-task { color: #3b82f6; }
        .content { padding: 24px 32px 32px 32px; }
        .greeting { font-size: 16px; font-weight: 600; color: #1e293b; margin-bottom: 12px; }
        .body-text { font-size: 15px; font-weight: 400; color: #475569; line-height: 1.6; margin-bottom: 24px; }
        .update-note { background-color: #f8fafc; border-radius: 16px; padding: 16px 20px; border: 1px solid #edf2f7; margin-bottom: 32px; color: #475569; font-size: 14px; line-height: 1.6; }
        .info-section { margin-bottom: 32px; }
        .info-row { display: flex; padding: 12px 0; border-bottom: 1px solid #edf2f7; }
        .info-row:last-child { border-bottom: none; }
        .info-label { width: 100px; font-size: 13px; font-weight: 500; color: #64748b; flex-shrink: 0; }
        .info-value { font-size: 14px; font-weight: 600; color: #1e293b; }
        .btn-container { text-align: center; margin: 32px 0; }
        .btn { background-color: #1e293b; color: #ffffff !important; padding: 12px 24px; border-radius: 40px; font-size: 14px; font-weight: 600; text-decoration: none; display: inline-block; }
        .signature { font-size: 13px; font-weight: 400; color: #475569; margin-top: 32px; }
        .signature-team { font-weight: 600; color: #1e293b; }
        .footer { background-color: #fafbfc; padding: 20px 32px; text-align: center; border-top: 1px solid #edf2f7; }
        .footer-text { font-size: 11px; font-weight: 400; color: #94a3b8; line-height: 1.6; }
        @media screen and (max-width: 480px) {
            .container { margin: 0 12px; }
            .content { padding: 20px 24px 32px 24px; }
            .info-label { width: 85px; }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <div class="logo"><span class="logo-flow">Flow</span><span class="logo-task">Task</span></div>
            </div>
            <div class="content">
                <div class="greeting">Bonjour {{ $recipientName }},</div>
                <div class="body-text">
                    La tâche <strong>"{{ $taskHeading }}"</strong> a été mise à jour. Voici les nouveaux détails concernant l'avancement :
                </div>

                @if(isset($explanation) && $explanation != '' && $explanation != 'Aucune note particulière.')
                <div class="update-note">
                    <strong>Note de suivi :</strong><br>
                    {!! nl2br($explanation) !!}
                </div>
                @endif

                <div class="info-section">
                    @if(isset($projectName) && $projectName != '')
                    <div class="info-row">
                        <div class="info-label">Projet</div>
                        <div class="info-value">{{ $projectName }}</div>
                    </div>
                    @endif
                    <div class="info-row">
                        <div class="info-label">Statut</div>
                        <div class="info-value">{{ $taskStatus }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Échéance</div>
                        <div class="info-value">{{ $dueDate }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Modifié par</div>
                        <div class="info-value">{{ $modifiedBy ?? 'N/A' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Intervenant</div>
                        <div class="info-value">{{ $specialistName ?? 'N/A' }}</div>
                    </div>
                </div>

                <div class="btn-container">
                    <a href="{{ $url }}" class="btn">Voir mon dossier</a>
                </div>

                <div class="signature">
                    Cordialement,<br>
                    L'équipe <span class="signature-team">{{ $companyName }}</span>
                </div>
            </div>
            <div class="footer">
                <div class="footer-text">&copy; {{ date('Y') }} {{ $companyName }}. Suivi de dossier automatisé.</div>
            </div>
        </div>
    </div>
</body>
</html>
