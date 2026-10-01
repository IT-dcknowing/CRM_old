Bonjour <?php echo e($recipientName); ?>,

Le statut de la tache « <?php echo e($taskHeading); ?> » vient de changer.
<?php if(!empty($previousStatus) && $previousStatus !== $taskStatus): ?>

<?php echo e($previousStatus); ?> -> <?php echo e($taskStatus); ?>

<?php else: ?>

Statut : <?php echo e($taskStatus); ?>

<?php endif; ?>
<?php if(!empty($noteContent)): ?>

NOTE DE MISE A JOUR
<?php echo e(strip_tags($noteContent)); ?>

-- <?php echo e($modifiedBy); ?>, aujourd'hui a <?php echo e($heure); ?>

<?php endif; ?>

Client   : <?php echo e($clientName); ?>

<?php if(!empty($projectName)): ?>
Dossier  : <?php echo e($projectName); ?>

<?php endif; ?>
Echeance : <?php echo e($dueDate); ?><?php if(!empty($dueLabel)): ?> (<?php echo e($dueLabel); ?>)<?php endif; ?>

Voir la tache : <?php echo e($url); ?>


--
Vous recevez ce message car vous etes affecte a cette tache. <?php echo e($companyName); ?>

<?php /**PATH C:\laragon\www\CRM\resources\views/emails/txt/collab_task_update.blade.php ENDPATH**/ ?>