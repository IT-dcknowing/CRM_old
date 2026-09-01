<a <?php if($active): ?>
    <?php echo e($attributes->merge(['class' => 'nav-item nav-link f-15 active'])); ?>

<?php else: ?>
    <?php echo e($attributes->merge(['class' => 'nav-item nav-link f-15'])); ?>

    <?php endif; ?>
    href="<?php echo e($link); ?>" role="tab" aria-selected="true">
    <?php echo e($slot); ?>

</a>
<?php /**PATH /home/cp2255957p00/public_html/tacheflux/resources/views/components/tab-item.blade.php ENDPATH**/ ?>