<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mise à jour Tâche</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f5f7fa; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f5f7fa; padding: 40px 0;">
        <tr>
            <td align="center">
                <table border="0" cellpadding="0" cellspacing="0" width="520" style="background-color: #ffffff; border-radius: 20px; overflow: hidden; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);">
                    <!-- Header Logo -->
                    <tr>
                        <td style="padding: 32px 40px 10px 40px; text-align: left;">
                            <div style="font-size: 22px; font-weight: 700; color: #1e293b;">Flow<span style="color: #3b82f6;">Task</span></div>
                        </td>
                    </tr>
                    
                    <!-- Content Area -->
                    <tr>
                        <td style="padding: 20px 40px 40px 40px;">
                            <div style="display: inline-block; background-color: #f1f5f9; color: #64748b; font-size: 11px; font-weight: 700; padding: 4px 12px; border-radius: 40px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 20px;">Mise à jour Tâche</div>
                            
                            <div style="font-size: 18px; font-weight: 700; color: #1e293b; margin-bottom: 12px;">Bonjour {{ $recipientName }},</div>
                            <div style="font-size: 15px; line-height: 1.6; color: #475569; margin-bottom: 24px;">
                                Une modification a été apportée à la tâche : <strong style="color: #1e293b;">« {{ $taskHeading }} »</strong>.
                            </div>

                            <!-- Comparison Block (Tables for side-by-side) -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border: 1px solid #edf2f7; border-radius: 12px; overflow: hidden; margin-bottom: 30px;">
                                <tr>
                                    <td width="50%" style="padding: 16px; background-color: #fff1f2; border-right: 1px solid #edf2f7;">
                                        <div style="font-size: 10px; font-weight: 700; text-transform: uppercase; color: #e11d48; margin-bottom: 4px;">Ancien Statut</div>
                                        <div style="font-size: 13px; font-weight: 600; color: #1e293b;">{{ $previousStatus ?? 'N/A' }}</div>
                                    </td>
                                    <td width="50%" style="padding: 16px; background-color: #f0fdf4;">
                                        <div style="font-size: 10px; font-weight: 700; text-transform: uppercase; color: #16a34a; margin-bottom: 4px;">Nouveau Statut</div>
                                        <div style="font-size: 13px; font-weight: 600; color: #1e293b;">{{ $taskStatus }}</div>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Info Table -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f8fafc; border-radius: 16px; border: 1px solid #edf2f7; padding: 10px 20px;">
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #edf2f7;">
                                        <span style="font-size: 13px; color: #64748b; font-weight: 500; display: inline-block; width: 100px;">Référence</span>
                                        <span style="font-size: 14px; color: #1e293b; font-weight: 600;">#{{ $taskReference }}</span>
                                    </td>
                                </tr>
                                @if(isset($projectName) && $projectName != '')
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #edf2f7;">
                                        <span style="font-size: 13px; color: #64748b; font-weight: 500; display: inline-block; width: 100px;">Projet</span>
                                        <span style="font-size: 14px; color: #1e293b; font-weight: 600;">{{ $projectName }}</span>
                                    </td>
                                </tr>
                                @endif
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #edf2f7;">
                                        <span style="font-size: 13px; color: #64748b; font-weight: 500; display: inline-block; width: 100px;">Client</span>
                                        <span style="font-size: 14px; color: #1e293b; font-weight: 600;">{{ $clientName }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0;">
                                        <span style="font-size: 13px; color: #64748b; font-weight: 500; display: inline-block; width: 100px;">Priorité</span>
                                        <span style="font-size: 14px; color: #1e293b; font-weight: 600;">{{ $priority }}</span>
                                    </td>
                                </tr>
                            </table>

                            @if($noteContent)
                            <div style="font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; margin: 24px 0 8px 0;">Commentaire</div>
                            <div style="background-color: #f8fafc; border: 1px solid #edf2f7; border-radius: 12px; padding: 20px; font-size: 14px; color: #475569; line-height: 1.6;">
                                {!! $noteContent !!}
                            </div>
                            @endif

                            <!-- Button -->
                            <div style="padding: 40px 0 20px 0; text-align: center;">
                                <a href="{{ $url }}" style="background-color: #1e293b; color: #ffffff; padding: 14px 32px; border-radius: 40px; text-decoration: none; font-weight: 600; font-size: 15px; display: inline-block;">Accéder au CRM</a>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="padding: 32px 40px; background-color: #fafbfc; text-align: center; border-top: 1px solid #edf2f7;">
                            <div style="font-size: 12px; color: #94a3b8; line-height: 1.5;">
                                &copy; {{ date('Y') }} {{ $companyName }}. Notification Interne.<br>
                                Ceci est une notification automatique de votre CRM.
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
