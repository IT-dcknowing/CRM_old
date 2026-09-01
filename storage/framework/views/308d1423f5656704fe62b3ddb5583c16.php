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
        .pill-container { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 12px; }
        .pill { background-color: #f1f5f9; padding: 6px 14px; border-radius: 30px; font-size: 13px; color: #3b82f6; text-decoration: none; font-weight: 500; display: inline-block; }
        .btn-container { text-align: center; margin: 32px 0; }
        .btn { background-color: #1e293b; color: #ffffff !important; padding: 12px 24px; border-radius: 40px; font-size: 14px; font-weight: 600; text-decoration: none; display: inline-block; }
        .signature { font-size: 13px; font-weight: 400; color: #475569; margin-top: 32px; }
        .signature-team { font-weight: 600; color: #1e293b; }
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
                <div class="greeting">Bonjour <?php echo e($recipientName); ?>,</div>
                <div class="body-text">Nous avons le plaisir de vous confirmer l'ouverture officielle de votre dossier de diligence au sein de notre établissement.</div>

                <div class="info-section">
                    <div class="info-row"><div class="info-label">Diligence</div><div class="info-value"><?php echo e($projectName); ?></div></div>
                    <div class="info-row"><div class="info-label">Statut</div><div class="info-value">Dossier ouvert</div></div>
                    <div class="info-row"><div class="info-label">Date</div><div class="info-value"><?php echo e($date); ?></div></div>
                </div>

                <?php if(isset($attachments) && count($attachments) > 0): ?>
                <div style="margin-bottom: 32px;">
                    <div style="font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase;">Documents joints</div>
                    <div class="pill-container">
                        <?php $__currentLoopData = $attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attachment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e($attachment['url']); ?>" class="pill"><?php echo e($attachment['name']); ?></a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
                <?php endif; ?>

                <div class="btn-container">
                    <a href="<?php echo e($url); ?>" class="btn">Voir mon dossier</a>
                </div>

                <div class="signature">
                    Cordialement,<br>
                    L'équipe <span class="signature-team"><?php echo e($companyName); ?></span>
                </div>
            </div>
            <div class="footer">
                <div class="footer-text">&copy; <?php echo e(date('Y')); ?> <?php echo e($companyName); ?>. Ouverture de diligence officielle.</div>
            </div>
        </div>
    </div>
</body>
</html>
<?php /**PATH /home/cp2255957p00/public_html/tacheflux/resources/views/emails/project_init.blade.php ENDPATH**/ ?>