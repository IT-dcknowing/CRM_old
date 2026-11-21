<table <?php echo e($attributes->merge(['class' => 'table', 'id' => 'example' ])); ?>>
    <?php if(isset($thead)): ?>
        <thead class="<?php echo e($headType); ?>">
            <tr>
                <?php echo $thead; ?>

            </tr>
        </thead>
    <?php endif; ?>
    <tbody>
        <?php echo e($slot); ?>

    </tbody>
</table>
<?php /**PATH /home/cp2255957p00/public_html/tacheflux/resources/views/components/table.blade.php ENDPATH**/ ?>