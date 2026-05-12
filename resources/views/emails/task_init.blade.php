<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* BASE STYLES */
        body { 
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; 
            background-color: #f5f7fa; 
            margin: 0; 
            padding: 0; 
            -webkit-font-smoothing: antialiased;
        }
        
        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #f5f7fa;
            padding: 48px 0;
        }

        .container {
            max-width: 520px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            overflow: hidden;
        }

        /* HEADER / LOGO */
        .header {
            padding: 32px 32px 0 32px;
            text-align: left;
        }
        
        .logo {
            font-size: 20px;
            font-weight: 700;
            text-decoration: none;
        }
        .logo-flow { color: #1e293b; }
        .logo-task { color: #3b82f6; }

        /* CONTENT */
        .content {
            padding: 24px 32px 32px 32px;
        }

        .greeting {
            font-size: 16px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 12px;
        }

        .body-text {
            font-size: 15px;
            font-weight: 400;
            color: #475569;
            line-height: 1.6;
            margin-bottom: 32px;
        }

        /* INFO LINES */
        .info-section {
            margin-bottom: 32px;
        }

        .info-row {
            display: flex;
            padding: 12px 0;
            border-bottom: 1px solid #edf2f7;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            width: 100px;
            font-size: 13px;
            font-weight: 500;
            color: #64748b;
            flex-shrink: 0;
        }

        .info-value {
            font-size: 14px;
            font-weight: 600;
            color: #1e293b;
        }

        /* ATTACHMENTS */
        .attachments-section {
            margin-bottom: 32px;
        }

        .attachments-title {
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 12px;
        }

        .pill-container {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .pill {
            background-color: #f1f5f9;
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 13px;
            color: #3b82f6;
            text-decoration: none;
            font-weight: 500;
            display: inline-block;
        }
        
        .pill:hover {
            background-color: #e2e8f0;
            text-decoration: underline;
        }

        /* BUTTON */
        .btn-container {
            text-align: center;
            margin-top: 32px;
            margin-bottom: 32px;
        }

        .btn {
            background-color: #1e293b;
            color: #ffffff !important;
            padding: 12px 24px;
            border-radius: 40px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.2s ease;
        }

        .btn:hover {
            background-color: #0f172a;
        }

        /* SIGNATURE */
        .signature {
            font-size: 13px;
            font-weight: 400;
            color: #475569;
            margin-top: 32px;
            line-height: 1.5;
        }
        
        .signature-team {
            font-weight: 600;
            color: #1e293b;
        }

        /* FOOTER */
        .footer {
            background-color: #fafbfc;
            padding: 20px 32px;
            text-align: center;
            border-top: 1px solid #edf2f7;
        }

        .footer-text {
            font-size: 11px;
            font-weight: 400;
            color: #94a3b8;
            line-height: 1.6;
        }

        /* RESPONSIVE */
        @media screen and (max-width: 480px) {
            .container {
                margin: 0 12px;
                border-radius: 16px;
            }
            .header {
                padding: 28px 28px 0 28px;
            }
            .content {
                padding: 20px 24px 32px 24px;
            }
            .info-label {
                width: 85px;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <!-- Ligne 1 : Logo Textuel -->
            <div class="header">
                <div class="logo">
                    <span class="logo-flow">Flow</span><span class="logo-task">Task</span>
                </div>
            </div>

            <div class="content">
                <!-- Ligne 2 : Greeting -->
                <div class="greeting">Bonjour {{ $recipientName }},</div>

                <!-- Ligne 3 : Message (Texte personnalisé par l'utilisateur) -->
                <div class="body-text">
                    Une nouvelle tâche, <strong>« {{ $taskHeading }} »</strong>, vient d’être créée. Consultez-la dès maintenant pour suivre les prochaines actions.
                </div>

                <!-- Lignes d'informations détaillées -->
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
                        <div class="info-label">Priorité</div>
                        <div class="info-value">{{ $priority ?? 'Moyenne' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Intervenant</div>
                        <div class="info-value">{{ $specialistName ?? 'N/A' }}</div>
                    </div>
                </div>

                <!-- Description / Note d'origine -->
                @if(isset($explanation) && $explanation != '' && $explanation != 'Aucune note particulière.')
                <div class="attachments-title" style="margin-top: 24px;">Description de la tâche</div>
                <div class="body-text" style="background-color: #f8fafc; padding: 16px; border-radius: 12px; border: 1px solid #edf2f7; font-size: 14px;">
                    {!! $explanation !!}
                </div>
                @endif

                <!-- Ligne 7 : Pièces jointes -->
                @if(isset($attachments) && count($attachments) > 0)
                <div class="attachments-section">
                    <div class="attachments-title">Pièces jointes</div>
                    <div class="pill-container">
                        @foreach($attachments as $attachment)
                        <a href="{{ $attachment['url'] }}" class="pill">{{ $attachment['name'] }}</a>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Ligne 8 : Bouton CTA -->
                <div class="btn-container">
                    <a href="{{ $url }}" class="btn">Voir mon dossier</a>
                </div>

                <!-- Ligne 9 : Signature -->
                <div class="signature">
                    Cordialement,<br>
                    L'équipe <span class="signature-team">{{ $companyName }}</span>
                </div>
            </div>

            <!-- Ligne 10 : Footer -->
            <div class="footer">
                <div class="footer-text">
                    &copy; {{ date('Y') }} {{ $companyName }}. Tous droits réservés.<br>
                    Ceci est une notification automatique, merci de ne pas y répondre.
                </div>
            </div>
        </div>
    </div>
</body>
</html>
