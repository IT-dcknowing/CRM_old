<div class="d-flex align-items-center task-department-filter <?php echo e($wrapperClass ?? ''); ?>">
    <div style="min-width: 190px;">
        <select class="form-control select-picker" name="department" id="department" data-live-search="true" data-size="8">
            <option value="all"><?php echo e(__('Tous les départements')); ?></option>
            <?php if(isset($departments)): ?>
                <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($dept->id); ?>" <?php echo e((request('department') == $dept->id) ? 'selected' : ''); ?>><?php echo e($dept->team_name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        </select>
    </div>
</div>
<?php /**PATH C:\laragon\www\CRM\resources\views/tasks/filters/department.blade.php ENDPATH**/ ?>