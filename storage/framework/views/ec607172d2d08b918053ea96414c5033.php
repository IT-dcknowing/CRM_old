
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport Hebdomadaire des Tâches</title>
</head>
<body style="margin:0;padding:0;background:#f3f4f6;">
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f3f4f6;padding:32px 12px;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" border="0" style="width:600px;max-width:600px;background:#ffffff;border:1px solid #e5e7eb;border-radius:12px;">

    
    <tr><td style="padding:22px 28px;border-bottom:1px solid #e5e7eb;">
        <table width="100%" cellpadding="0" cellspacing="0" border="0"><tr>
            <td valign="middle">
                <?php if(isset($message) && !empty($logoPath) && file_exists($logoPath)): ?>
                    <img src="<?php echo e($message->embed($logoPath)); ?>" alt="<?php echo e($company->company_name); ?>" width="120" height="32" style="display:block;border:0;width:120px;height:32px;object-fit:contain;">
                <?php elseif(!empty($logoUrlDark)): ?>
                    <img src="<?php echo e($logoUrlDark); ?>" alt="<?php echo e($company->company_name); ?>" width="120" height="32" style="display:block;border:0;width:120px;height:32px;object-fit:contain;">
                <?php else: ?>
                    <span style="font-size:16px;font-weight:bold;color:#111827;"><?php echo e($company->company_name); ?></span>
                <?php endif; ?>
            </td>
            <td align="right" valign="middle">
                <table cellpadding="0" cellspacing="0" border="0" style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:999px;"><tr>
                    <td style="padding:4px 12px;font-size:11.5px;font-weight:600;color:#374151;">Rapport Hebdomadaire</td>
                </tr></table>
            </td>
        </tr></table>
    </td></tr>

    
    <tr><td style="padding:28px 28px 0 28px;">
        <h1 style="margin:0;font-size:20px;line-height:1.3;color:#111827;font-weight:bold;letter-spacing:-.5px;">
            Activité des tâches du cabinet
        </h1>
        <p style="margin:6px 0 0;font-size:13px;color:#6b7280;">
            Semaine du <?php echo e($rapport->debut->format('d/m/Y')); ?> au <?php echo e($rapport->fin->format('d/m/Y')); ?>

        </p>

        <p style="margin:16px 0 0;font-size:14.5px;line-height:1.6;color:#374151;">
            Bonjour <strong><?php echo e($admin->name); ?></strong>,<br>
            Le rapport hebdomadaire d'activité des tâches a été généré avec succès. Vous trouverez le document PDF complet en pièce jointe de ce courriel.
        </p>
    </td></tr>

    
    <tr><td style="padding:20px 28px 0 28px;">
        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:10px;">
            <tr>
                <td style="padding:16px;text-align:center;border-right:1px solid #e5e7eb;width:25%;">
                    <div style="font-size:22px;font-weight:bold;color:#111827;"><?php echo e($rapport->synthese->total); ?></div>
                    <div style="font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;margin-top:2px;">Tâches actives</div>
                </td>
                <td style="padding:16px;text-align:center;border-right:1px solid #e5e7eb;width:25%;">
                    <div style="font-size:22px;font-weight:bold;color:#16a34a;"><?php echo e($rapport->synthese->termines); ?></div>
                    <div style="font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;margin-top:2px;">Terminées</div>
                </td>
                <td style="padding:16px;text-align:center;border-right:1px solid #e5e7eb;width:25%;">
                    <div style="font-size:22px;font-weight:bold;color:#2563eb;"><?php echo e($rapport->synthese->enCours); ?></div>
                    <div style="font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;margin-top:2px;">En cours</div>
                </td>
                <td style="padding:16px;text-align:center;width:25%;">
                    <div style="font-size:22px;font-weight:bold;color:<?php echo e($rapport->synthese->retards > 0 ? '#dc2626' : '#6b7280'); ?>;"><?php echo e($rapport->synthese->retards); ?></div>
                    <div style="font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;margin-top:2px;">En retard</div>
                </td>
            </tr>
        </table>
    </td></tr>

    
    <tr><td style="padding:16px 28px 0 28px;">
        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border:1px solid #e5e7eb;border-radius:8px;">
            <tr>
                <td style="padding:10px 14px;border-bottom:1px solid #e5e7eb;font-size:13px;color:#6b7280;">Tâches à faire</td>
                <td style="padding:10px 14px;border-bottom:1px solid #e5e7eb;font-size:13px;font-weight:600;color:#111827;text-align:right;"><?php echo e($rapport->synthese->aFaire); ?></td>
            </tr>
            <tr>
                <td style="padding:10px 14px;border-bottom:1px solid #e5e7eb;font-size:13px;color:#6b7280;">Tâches incomplètes</td>
                <td style="padding:10px 14px;border-bottom:1px solid #e5e7eb;font-size:13px;font-weight:600;color:#111827;text-align:right;"><?php echo e($rapport->synthese->incompletes); ?></td>
            </tr>
            <tr>
                <td style="padding:10px 14px;border-bottom:1px solid #e5e7eb;font-size:13px;color:#6b7280;">Collaborateurs actifs cette semaine</td>
                <td style="padding:10px 14px;border-bottom:1px solid #e5e7eb;font-size:13px;font-weight:600;color:#111827;text-align:right;"><?php echo e($rapport->synthese->gens); ?></td>
            </tr>
            <tr>
                <td style="padding:10px 14px;font-size:13px;color:#6b7280;">Délai moyen de traitement</td>
                <td style="padding:10px 14px;font-size:13px;font-weight:600;color:#111827;text-align:right;">
                    <?php echo e($rapport->synthese->delaiMoyen !== null ? $rapport->synthese->delaiMoyen . ' j' : '—'); ?>

                </td>
            </tr>
        </table>
    </td></tr>

    
    <tr><td style="padding:20px 28px 0 28px;">
        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;">
            <tr>
                <td style="padding:12px 16px;font-size:13px;line-height:1.5;color:#1e40af;">
                    📎 <strong>Pièce jointe :</strong> Le document <code>rapport-hebdomadaire-<?php echo e($rapport->debut->format('Y-m-d')); ?>.pdf</code> est joint à ce mail avec le détail de toutes les tâches et la répartition par collaborateur.
                </td>
            </tr>
        </table>
    </td></tr>

    
    <tr><td align="center" style="padding:28px 28px 24px 28px;">
        <table cellpadding="0" cellspacing="0" border="0"><tr>
            <td align="center" style="border-radius:8px;background:#111827;">
                <a href="<?php echo e($urlRapport); ?>" style="display:inline-block;padding:12px 24px;font-size:14px;font-weight:600;color:#ffffff;text-decoration:none;border-radius:8px;">
                    Consulter le rapport en ligne &rarr;
                </a>
            </td>
        </tr></table>
    </td></tr>

    
    <tr><td style="padding:18px 28px;background:#f9fafb;border-top:1px solid #e5e7eb;border-bottom-left-radius:12px;border-bottom-right-radius:12px;">
        <table width="100%" cellpadding="0" cellspacing="0" border="0"><tr>
            <td style="font-size:11.5px;line-height:1.5;color:#9ca3af;">
                Ce rapport automatisé est envoyé chaque lundi matin (après clôture de la semaine) aux administrateurs de <?php echo e($company->company_name); ?>.
            </td>
        </tr></table>
    </td></tr>

</table>
</td></tr>
</table>
</body>
</html>
<?php /**PATH C:\laragon\www\CRM\resources\views/emails/rapport_hebdomadaire.blade.php ENDPATH**/ ?>