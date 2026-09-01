<?php
    $viewTaskFilePermission = user()->permission('view_task_files');
    $deleteTaskFilePermission = user()->permission('delete_task_files');
?>

<?php $__empty_1 = true; $__currentLoopData = $files; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
<?php if (isset($component)) { $__componentOriginalcc3eadf431dc104666da55af50a04915 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalcc3eadf431dc104666da55af50a04915 = $attributes; } ?>
<?php $component = App\View\Components\FileCard::resolve(['fileName' => $file->filename,'dateAdded' => $file->created_at->diffForHumans()] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('file-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\FileCard::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <?php if (isset($component)) { $__componentOriginale3c978aa1f901c04844576cc043c206b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3c978aa1f901c04844576cc043c206b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.file-view-thumbnail','data' => ['file' => $file]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('file-view-thumbnail'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['file' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($file)]); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale3c978aa1f901c04844576cc043c206b)): ?>
<?php $attributes = $__attributesOriginale3c978aa1f901c04844576cc043c206b; ?>
<?php unset($__attributesOriginale3c978aa1f901c04844576cc043c206b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale3c978aa1f901c04844576cc043c206b)): ?>
<?php $component = $__componentOriginale3c978aa1f901c04844576cc043c206b; ?>
<?php unset($__componentOriginale3c978aa1f901c04844576cc043c206b); ?>
<?php endif; ?>

    <?php if($viewTaskFilePermission == 'all' || ($viewTaskFilePermission == 'added' && $file->added_by == user()->id)): ?>
         <?php $__env->slot('action', null, []); ?> 
            <div class="dropdown ml-auto file-action">
                <button class="btn btn-lg f-14 p-0 text-lightest  rounded  dropdown-toggle"
                    type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-ellipsis-h"></i>
                </button>

                <div class="dropdown-menu dropdown-menu-right border-grey rounded b-shadow-4 p-0"
                    aria-labelledby="dropdownMenuLink" tabindex="0">
                    <?php if($viewTaskFilePermission == 'all' || ($viewTaskFilePermission == 'added' && $file->added_by == user()->id)): ?>
                        <?php if($file->icon == 'images'): ?>
                            <a class="img-lightbox cursor-pointer d-block text-dark-grey f-13 pt-3 px-3" data-image-url="<?php echo e($file->file_url); ?>" href="javascript:;"><?php echo app('translator')->get('app.view'); ?></a>
                        <?php else: ?>
                            <a class="cursor-pointer d-block text-dark-grey f-13 pt-3 px-3 " target="_blank" href="<?php echo e($file->file_url); ?>"><?php echo app('translator')->get('app.view'); ?></a>
                        <?php endif; ?>
                        <a class="cursor-pointer d-block text-dark-grey f-13 py-3 px-3 "
                            href="<?php echo e(route('task_files.download', md5($file->id))); ?>"><?php echo app('translator')->get('app.download'); ?></a>
                    <?php endif; ?>

                    <?php if($deleteTaskFilePermission == 'all' || ($deleteTaskFilePermission == 'added' && $file->added_by == user()->id)): ?>
                        <a class="cursor-pointer d-block text-dark-grey f-13 pb-3 px-3 delete-file"
                            data-row-id="<?php echo e($file->id); ?>" href="javascript:;"><?php echo app('translator')->get('app.delete'); ?></a>
                    <?php endif; ?>
                </div>
            </div>
         <?php $__env->endSlot(); ?>
    <?php endif; ?>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalcc3eadf431dc104666da55af50a04915)): ?>
<?php $attributes = $__attributesOriginalcc3eadf431dc104666da55af50a04915; ?>
<?php unset($__attributesOriginalcc3eadf431dc104666da55af50a04915); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalcc3eadf431dc104666da55af50a04915)): ?>
<?php $component = $__componentOriginalcc3eadf431dc104666da55af50a04915; ?>
<?php unset($__componentOriginalcc3eadf431dc104666da55af50a04915); ?>
<?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <?php if (isset($component)) { $__componentOriginal269164c77d9d34462c34359c03da6a68 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal269164c77d9d34462c34359c03da6a68 = $attributes; } ?>
<?php $component = App\View\Components\Cards\NoRecord::resolve(['message' => __('messages.noFileUploaded'),'icon' => 'file'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('cards.no-record'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Cards\NoRecord::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal269164c77d9d34462c34359c03da6a68)): ?>
<?php $attributes = $__attributesOriginal269164c77d9d34462c34359c03da6a68; ?>
<?php unset($__attributesOriginal269164c77d9d34462c34359c03da6a68); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal269164c77d9d34462c34359c03da6a68)): ?>
<?php $component = $__componentOriginal269164c77d9d34462c34359c03da6a68; ?>
<?php unset($__componentOriginal269164c77d9d34462c34359c03da6a68); ?>
<?php endif; ?>
<?php endif; ?>
<?php /**PATH /home/cp2255957p00/public_html/tacheflux/resources/views/tasks/files/show.blade.php ENDPATH**/ ?>