Bonjour <?php echo e($recipientName); ?>,

Cette tache vous est confiee : <?php echo e($taskHeading); ?>

<?php if(!empty($dueLabel)): ?>
<?php echo e($dueLabel); ?>

<?php endif; ?>

<?php if(!empty($taskReference)): ?>
Reference    : <?php echo e($taskReference); ?>

<?php endif; ?>
Client       : <?php echo e($clientName); ?>

<?php if(!empty($projectName)): ?>
Dossier      : <?php echo e($projectName); ?>

<?php endif; ?>
<?php if(!empty($responsibleName)): ?>
Responsable  : <?php echo e($responsibleName); ?>

<?php endif; ?>
Priorite     : <?php echo e($priority); ?>

Echeance     : <?php echo e($dueDate); ?>

Statut       : <?php echo e($taskStatus); ?>

<?php if(!empty($description)): ?>

INSTRUCTIONS
<?php echo e(strip_tags($description)); ?>

<?php endif; ?>
<?php if(!empty($attachments)): ?>

PIECES DU DOSSIER
<?php $__currentLoopData = $attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attachment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
- <?php echo e($attachment['name']); ?> : <?php echo e($attachment['url']); ?>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>

Ouvrir la tache : <?php echo e($url); ?>


--
Notification interne <?php echo e($companyName); ?>. Repondez a ce message pour joindre le responsable du dossier.
<?php /**PATH C:\laragon\www\CRM\resources\views/emails/txt/collab_task_init.blade.php ENDPATH**/ ?>