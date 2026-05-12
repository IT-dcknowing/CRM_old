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
        .body-text { font-size: 15px; font-weight: 400; color: #475569; line-height: 1.6; margin-bottom: 32px; }
        .info-section { margin-bottom: 32px; }
        .info-row { display: flex; padding: 12px 0; border-bottom: 1px solid #edf2f7; }
        .info-row:last-child { border-bottom: none; }
        .info-label { width: 100px; font-size: 13px; font-weight: 500; color: #64748b; flex-shrink: 0; }
        .info-value { font-size: 14px; font-weight: 600; color: #1e293b; }
        .btn-container { text-align: center; margin: 32px 0; }
        .btn { background-color: #1e293b; color: #ffffff !important; padding: 12px 24px; border-radius: 40px; font-size: 14px; font-weight: 600; text-decoration: none; display: inline-block; }
        .footer { background-color: #fafbfc; padding: 20px 32px; text-align: center; border-top: 1px solid #edf2f7; }
        .footer-text { font-size: 11px; font-weight: 400; color: #94a3b8; }
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
                <div class="body-text">Vous avez été assigné en tant que collaborateur sur la diligence suivante.</div>

                <div class="info-section">
                    <div class="info-row"><div class="info-label">Diligence</div><div class="info-value">{{ $projectName }}</div></div>
                    <div class="info-row"><div class="info-label">Statut</div><div class="info-value">Assigné</div></div>
                    <div class="info-row"><div class="info-label">Date</div><div class="info-value">{{ $date }}</div></div>
                </div>

                <div class="btn-container">
                    <a href="{{ $url }}" class="btn">Accéder au dossier</a>
                </div>

                <div class="signature">
                    Cordialement,<br>
                    <span style="font-weight: 600; color: #1e293b;">FlowTask Internal</span>
                </div>
            </div>
            <div class="footer">
                <div class="footer-text">&copy; {{ date('Y') }} {{ $companyName }}. Assignation Interne.</div>
            </div>
        </div>
    </div>
</body>
</html>
