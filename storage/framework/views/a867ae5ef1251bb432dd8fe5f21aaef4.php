Bonjour <?php echo e($recipientName); ?>,

Votre contrat est pret. Vous pouvez le lire integralement puis le signer en
ligne, sans rien imprimer ni renvoyer.

<?php echo e($subject); ?>


Reference        : <?php echo e($contractNumber); ?>

Type de contrat  : <?php echo e($contractType); ?>

Etabli le        : <?php echo e($date); ?>

A signer avant le: <?php echo e($expiryDate); ?>


Lire et signer le contrat : <?php echo e($url); ?>


Ce lien vous est personnel et reste valable <?php echo e($expiryDays); ?> jours. Ne le
transferez pas : toute signature apposee depuis ce lien vous sera attribuee.
Passe ce delai, demandez-nous simplement un nouvel envoi.

Une question sur une clause avant de signer ? Repondez a cet e-mail.

--
<?php echo e($companyName); ?><?php if(!empty($company->company_email)): ?> · <?php echo e($company->company_email); ?><?php endif; ?>
<?php /**PATH /home/cp2255957p00/public_html/tacheflux/resources/views/emails/txt/contract_init.blade.php ENDPATH**/ ?>