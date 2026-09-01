<?php
$addProjectCategoryPermission = user()->permission('manage_project_category');
$addClientPermission = user()->permission('add_clients');
$editProjectMemberPermission = user()->permission('edit_project_members');
$addEmployeePermission = user()->permission('add_employees');
$addProjectMemberPermission = user()->permission('add_project_members');
$addProjectMemberPermission = user()->permission('add_project_members');
$createPublicProjectPermission = user()->permission('create_public_project');

?>

<link rel="stylesheet" href="<?php echo e(asset('vendor/css/dropzone.min.css')); ?>">

<div class="row">
    <div class="col-sm-12">
        <?php if (isset($component)) { $__componentOriginal18ad2e0d264f9740dc73fff715357c28 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal18ad2e0d264f9740dc73fff715357c28 = $attributes; } ?>
<?php $component = App\View\Components\Form::resolve(['method' => 'PUT'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('form'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Form::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'save-project-data-form']); ?>
            <div class="add-client bg-white rounded">
                <h4 class="mb-0 p-20 f-21 font-weight-normal  border-bottom-grey">
                    <?php echo app('translator')->get('modules.projects.projectInfo'); ?></h4>
                <div class="row p-20">
                    <div class="col-lg-4 col-md-4">
                        <?php if (isset($component)) { $__componentOriginal4e45e801405ab67097982370a6a83cba = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4e45e801405ab67097982370a6a83cba = $attributes; } ?>
<?php $component = App\View\Components\Forms\Text::resolve(['fieldLabel' => __('modules.taskShortCode'),'fieldName' => 'project_code','fieldRequired' => 'false','fieldId' => 'project_code','fieldPlaceholder' => __('placeholders.writeshortcode'),'fieldValue' => $project->project_short_code] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('forms.text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Forms\Text::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'mr-0 mr-lg-2 mr-md-2']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4e45e801405ab67097982370a6a83cba)): ?>
<?php $attributes = $__attributesOriginal4e45e801405ab67097982370a6a83cba; ?>
<?php unset($__attributesOriginal4e45e801405ab67097982370a6a83cba); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4e45e801405ab67097982370a6a83cba)): ?>
<?php $component = $__componentOriginal4e45e801405ab67097982370a6a83cba; ?>
<?php unset($__componentOriginal4e45e801405ab67097982370a6a83cba); ?>
<?php endif; ?>
                    </div>
                    <div class="col-lg-8 col-md-8">
                        <?php if (isset($component)) { $__componentOriginal4e45e801405ab67097982370a6a83cba = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4e45e801405ab67097982370a6a83cba = $attributes; } ?>
<?php $component = App\View\Components\Forms\Text::resolve(['fieldLabel' => __('modules.projects.projectName'),'fieldName' => 'project_name','fieldRequired' => 'true','fieldId' => 'project_name','fieldValue' => $project->project_name,'fieldPlaceholder' => __('placeholders.project')] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('forms.text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Forms\Text::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'mr-0 mr-lg-2 mr-md-2']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4e45e801405ab67097982370a6a83cba)): ?>
<?php $attributes = $__attributesOriginal4e45e801405ab67097982370a6a83cba; ?>
<?php unset($__attributesOriginal4e45e801405ab67097982370a6a83cba); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4e45e801405ab67097982370a6a83cba)): ?>
<?php $component = $__componentOriginal4e45e801405ab67097982370a6a83cba; ?>
<?php unset($__componentOriginal4e45e801405ab67097982370a6a83cba); ?>
<?php endif; ?>
                    </div>
                    <input type="hidden" name="project_id" value="<?php echo e($project->id); ?>">
                    <div class="col-md-6 col-lg-4">
                        <?php if (isset($component)) { $__componentOriginalf704f069031d81dfb7cf95f6709a6a66 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf704f069031d81dfb7cf95f6709a6a66 = $attributes; } ?>
<?php $component = App\View\Components\Forms\Datepicker::resolve(['fieldId' => 'start_date','fieldRequired' => 'true','fieldLabel' => __('modules.projects.startDate'),'fieldName' => 'start_date','fieldValue' => ($project->start_date ? $project->start_date->format(company()->date_format) : ''),'fieldPlaceholder' => __('placeholders.date')] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('forms.datepicker'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Forms\Datepicker::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf704f069031d81dfb7cf95f6709a6a66)): ?>
<?php $attributes = $__attributesOriginalf704f069031d81dfb7cf95f6709a6a66; ?>
<?php unset($__attributesOriginalf704f069031d81dfb7cf95f6709a6a66); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf704f069031d81dfb7cf95f6709a6a66)): ?>
<?php $component = $__componentOriginalf704f069031d81dfb7cf95f6709a6a66; ?>
<?php unset($__componentOriginalf704f069031d81dfb7cf95f6709a6a66); ?>
<?php endif; ?>
                    </div>

                    <div class="col-md-6 col-lg-4" id="deadlineBox">
                        <?php if (isset($component)) { $__componentOriginalf704f069031d81dfb7cf95f6709a6a66 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf704f069031d81dfb7cf95f6709a6a66 = $attributes; } ?>
<?php $component = App\View\Components\Forms\Datepicker::resolve(['fieldId' => 'deadline','fieldRequired' => 'true','fieldLabel' => __('modules.projects.deadline'),'fieldName' => 'deadline','fieldValue' => ($project->deadline ? $project->deadline->format(company()->date_format) : ''),'fieldPlaceholder' => __('placeholders.date')] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('forms.datepicker'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Forms\Datepicker::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf704f069031d81dfb7cf95f6709a6a66)): ?>
<?php $attributes = $__attributesOriginalf704f069031d81dfb7cf95f6709a6a66; ?>
<?php unset($__attributesOriginalf704f069031d81dfb7cf95f6709a6a66); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf704f069031d81dfb7cf95f6709a6a66)): ?>
<?php $component = $__componentOriginalf704f069031d81dfb7cf95f6709a6a66; ?>
<?php unset($__componentOriginalf704f069031d81dfb7cf95f6709a6a66); ?>
<?php endif; ?>
                    </div>

                    <div class="<?php echo e($project->deadline == null ? 'col-md-8' : 'col-md-6 col-lg-4'); ?>">
                        <div class="form-group">
                            <div class="d-flex mt-5">
                                <?php if (isset($component)) { $__componentOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3 = $attributes; } ?>
<?php $component = App\View\Components\Forms\Checkbox::resolve(['fieldId' => 'without_deadline','checked' => ($project->deadline == null) ? true : false,'fieldLabel' => __('modules.projects.withoutDeadline'),'fieldName' => 'without_deadline'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('forms.checkbox'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Forms\Checkbox::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3)): ?>
<?php $attributes = $__attributesOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3; ?>
<?php unset($__attributesOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3)): ?>
<?php $component = $__componentOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3; ?>
<?php unset($__componentOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3); ?>
<?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <?php if (isset($component)) { $__componentOriginal89b295b0763c93abe0143426334eb5d6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal89b295b0763c93abe0143426334eb5d6 = $attributes; } ?>
<?php $component = App\View\Components\Forms\Label::resolve(['fieldId' => 'category_id','fieldLabel' => __('modules.projects.projectCategory')] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('forms.label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Forms\Label::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'my-3']); ?>
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal89b295b0763c93abe0143426334eb5d6)): ?>
<?php $attributes = $__attributesOriginal89b295b0763c93abe0143426334eb5d6; ?>
<?php unset($__attributesOriginal89b295b0763c93abe0143426334eb5d6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal89b295b0763c93abe0143426334eb5d6)): ?>
<?php $component = $__componentOriginal89b295b0763c93abe0143426334eb5d6; ?>
<?php unset($__componentOriginal89b295b0763c93abe0143426334eb5d6); ?>
<?php endif; ?>
                        <?php if (isset($component)) { $__componentOriginalcbf9105fd4879d5d6ef9e1f6fe271af7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalcbf9105fd4879d5d6ef9e1f6fe271af7 = $attributes; } ?>
<?php $component = App\View\Components\Forms\InputGroup::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('forms.input-group'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Forms\InputGroup::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                            <select class="form-control select-picker" name="category_id" id="project_category_id"
                                data-live-search="true">
                                <option value="">--</option>
                                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option <?php if($project->category_id == $category->id): echo 'selected'; endif; ?> value="<?php echo e($category->id); ?>">
                                        <?php echo e($category->category_name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>

                            <?php if($addProjectCategoryPermission == 'all' || $addProjectCategoryPermission == 'added'): ?>
                                 <?php $__env->slot('append', null, []); ?> 
                                    <button id="addProjectCategory" type="button"
                                        class="btn btn-outline-secondary border-grey"
                                        data-toggle="tooltip" data-original-title="<?php echo e(__('modules.projectCategory.addProjectCategory')); ?>"><?php echo app('translator')->get('app.add'); ?></button>
                                 <?php $__env->endSlot(); ?>
                            <?php endif; ?>
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalcbf9105fd4879d5d6ef9e1f6fe271af7)): ?>
<?php $attributes = $__attributesOriginalcbf9105fd4879d5d6ef9e1f6fe271af7; ?>
<?php unset($__attributesOriginalcbf9105fd4879d5d6ef9e1f6fe271af7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalcbf9105fd4879d5d6ef9e1f6fe271af7)): ?>
<?php $component = $__componentOriginalcbf9105fd4879d5d6ef9e1f6fe271af7; ?>
<?php unset($__componentOriginalcbf9105fd4879d5d6ef9e1f6fe271af7); ?>
<?php endif; ?>
                    </div>

                    <?php if(!in_array('client', user_roles()) && ($editProjectMembersPermission == 'all' || $editPermission == 'all')): ?>
                        <div class="col-md-4">
                            <?php if (isset($component)) { $__componentOriginal89b295b0763c93abe0143426334eb5d6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal89b295b0763c93abe0143426334eb5d6 = $attributes; } ?>
<?php $component = App\View\Components\Forms\Label::resolve(['fieldId' => 'department','fieldLabel' => __('app.department')] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('forms.label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Forms\Label::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'my-3']); ?>
                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal89b295b0763c93abe0143426334eb5d6)): ?>
<?php $attributes = $__attributesOriginal89b295b0763c93abe0143426334eb5d6; ?>
<?php unset($__attributesOriginal89b295b0763c93abe0143426334eb5d6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal89b295b0763c93abe0143426334eb5d6)): ?>
<?php $component = $__componentOriginal89b295b0763c93abe0143426334eb5d6; ?>
<?php unset($__componentOriginal89b295b0763c93abe0143426334eb5d6); ?>
<?php endif; ?>
                            <?php if (isset($component)) { $__componentOriginalcbf9105fd4879d5d6ef9e1f6fe271af7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalcbf9105fd4879d5d6ef9e1f6fe271af7 = $attributes; } ?>
<?php $component = App\View\Components\Forms\InputGroup::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('forms.input-group'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Forms\InputGroup::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                                <select class="form-control multiple-users" multiple name="team_id[]" id="employee_department"
                                    data-live-search="true">
                                    <?php $__currentLoopData = $teams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $team): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option data-content="<span class='badge badge-pill badge-light border p-2'><?php echo e($team->team_name); ?></span>"
                                        <?php if(in_array($team->id, $departmentIds)): echo 'selected'; endif; ?> value="<?php echo e($team->id); ?>">
                                            <?php echo e($team->team_name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalcbf9105fd4879d5d6ef9e1f6fe271af7)): ?>
<?php $attributes = $__attributesOriginalcbf9105fd4879d5d6ef9e1f6fe271af7; ?>
<?php unset($__attributesOriginalcbf9105fd4879d5d6ef9e1f6fe271af7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalcbf9105fd4879d5d6ef9e1f6fe271af7)): ?>
<?php $component = $__componentOriginalcbf9105fd4879d5d6ef9e1f6fe271af7; ?>
<?php unset($__componentOriginalcbf9105fd4879d5d6ef9e1f6fe271af7); ?>
<?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="col-md-4 <?php if(!in_array('clients', user_modules())): ?> d-none <?php endif; ?>">

                        <?php if (isset($component)) { $__componentOriginalcbf9105fd4879d5d6ef9e1f6fe271af7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalcbf9105fd4879d5d6ef9e1f6fe271af7 = $attributes; } ?>
<?php $component = App\View\Components\Forms\InputGroup::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('forms.input-group'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Forms\InputGroup::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                            <?php if (isset($component)) { $__componentOriginalcfa76c1cf34159bb19d1a4c8f5da7d2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalcfa76c1cf34159bb19d1a4c8f5da7d2c = $attributes; } ?>
<?php $component = App\View\Components\ClientSelectionDropdown::resolve(['labelClass' => 'my-3','clients' => $clients,'fieldRequired' => 'false','selected' => $project->client_id ?? null] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('client-selection-dropdown'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\ClientSelectionDropdown::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalcfa76c1cf34159bb19d1a4c8f5da7d2c)): ?>
<?php $attributes = $__attributesOriginalcfa76c1cf34159bb19d1a4c8f5da7d2c; ?>
<?php unset($__attributesOriginalcfa76c1cf34159bb19d1a4c8f5da7d2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalcfa76c1cf34159bb19d1a4c8f5da7d2c)): ?>
<?php $component = $__componentOriginalcfa76c1cf34159bb19d1a4c8f5da7d2c; ?>
<?php unset($__componentOriginalcfa76c1cf34159bb19d1a4c8f5da7d2c); ?>
<?php endif; ?>







                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalcbf9105fd4879d5d6ef9e1f6fe271af7)): ?>
<?php $attributes = $__attributesOriginalcbf9105fd4879d5d6ef9e1f6fe271af7; ?>
<?php unset($__attributesOriginalcbf9105fd4879d5d6ef9e1f6fe271af7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalcbf9105fd4879d5d6ef9e1f6fe271af7)): ?>
<?php $component = $__componentOriginalcbf9105fd4879d5d6ef9e1f6fe271af7; ?>
<?php unset($__componentOriginalcbf9105fd4879d5d6ef9e1f6fe271af7); ?>
<?php endif; ?>
                    </div>

                    <div class="col-md-12 col-lg-12">
                        <div class="form-group my-3">
                            <?php if (isset($component)) { $__componentOriginal89b295b0763c93abe0143426334eb5d6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal89b295b0763c93abe0143426334eb5d6 = $attributes; } ?>
<?php $component = App\View\Components\Forms\Label::resolve(['fieldId' => 'project_summary','fieldLabel' => __('modules.projects.projectSummary')] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('forms.label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Forms\Label::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'my-3']); ?>
                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal89b295b0763c93abe0143426334eb5d6)): ?>
<?php $attributes = $__attributesOriginal89b295b0763c93abe0143426334eb5d6; ?>
<?php unset($__attributesOriginal89b295b0763c93abe0143426334eb5d6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal89b295b0763c93abe0143426334eb5d6)): ?>
<?php $component = $__componentOriginal89b295b0763c93abe0143426334eb5d6; ?>
<?php unset($__componentOriginal89b295b0763c93abe0143426334eb5d6); ?>
<?php endif; ?>
                            <div id="project_summary"><?php echo $project->project_summary; ?></div>
                            <textarea name="project_summary" id="project_summary-text"
                                class="d-none"><?php echo $project->project_summary; ?></textarea>
                        </div>
                    </div>

                    <div class="col-md-12 col-lg-4">
                        <div class="form-group my-3">
                            <label class="f-14 text-dark-grey mb-12 w-100 mt-3"
                                for="usr"><?php echo app('translator')->get('modules.projects.viewPublicGanttChart'); ?></label>
                            <div class="d-flex">
                                <?php if (isset($component)) { $__componentOriginalc709ddc147ddde534205d9546b4fb0db = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc709ddc147ddde534205d9546b4fb0db = $attributes; } ?>
<?php $component = App\View\Components\Forms\Radio::resolve(['fieldId' => 'public_gantt_chart-yes','fieldLabel' => __('app.enable'),'fieldName' => 'public_gantt_chart','fieldValue' => 'enable','checked' => 'true'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('forms.radio'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Forms\Radio::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc709ddc147ddde534205d9546b4fb0db)): ?>
<?php $attributes = $__attributesOriginalc709ddc147ddde534205d9546b4fb0db; ?>
<?php unset($__attributesOriginalc709ddc147ddde534205d9546b4fb0db); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc709ddc147ddde534205d9546b4fb0db)): ?>
<?php $component = $__componentOriginalc709ddc147ddde534205d9546b4fb0db; ?>
<?php unset($__componentOriginalc709ddc147ddde534205d9546b4fb0db); ?>
<?php endif; ?>
                                <?php if (isset($component)) { $__componentOriginalc709ddc147ddde534205d9546b4fb0db = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc709ddc147ddde534205d9546b4fb0db = $attributes; } ?>
<?php $component = App\View\Components\Forms\Radio::resolve(['fieldId' => 'public_gantt_chart-no','fieldLabel' => __('app.disable'),'fieldValue' => 'disable','fieldName' => 'public_gantt_chart','checked' => $project->public_gantt_chart == 'disable'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('forms.radio'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Forms\Radio::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc709ddc147ddde534205d9546b4fb0db)): ?>
<?php $attributes = $__attributesOriginalc709ddc147ddde534205d9546b4fb0db; ?>
<?php unset($__attributesOriginalc709ddc147ddde534205d9546b4fb0db); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc709ddc147ddde534205d9546b4fb0db)): ?>
<?php $component = $__componentOriginalc709ddc147ddde534205d9546b4fb0db; ?>
<?php unset($__componentOriginalc709ddc147ddde534205d9546b4fb0db); ?>
<?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 col-lg-4">
                        <div class="form-group my-3">
                            <label class="f-14 text-dark-grey mb-12 w-100 mt-3"
                                for="usr"><?php echo app('translator')->get('app.public'); ?> <?php echo app('translator')->get('modules.tasks.taskBoard'); ?></label>
                            <div class="d-flex">
                                <?php if (isset($component)) { $__componentOriginalc709ddc147ddde534205d9546b4fb0db = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc709ddc147ddde534205d9546b4fb0db = $attributes; } ?>
<?php $component = App\View\Components\Forms\Radio::resolve(['fieldId' => 'public_taskboard-yes','fieldLabel' => __('app.enable'),'fieldName' => 'public_taskboard','fieldValue' => 'enable','checked' => 'true'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('forms.radio'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Forms\Radio::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc709ddc147ddde534205d9546b4fb0db)): ?>
<?php $attributes = $__attributesOriginalc709ddc147ddde534205d9546b4fb0db; ?>
<?php unset($__attributesOriginalc709ddc147ddde534205d9546b4fb0db); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc709ddc147ddde534205d9546b4fb0db)): ?>
<?php $component = $__componentOriginalc709ddc147ddde534205d9546b4fb0db; ?>
<?php unset($__componentOriginalc709ddc147ddde534205d9546b4fb0db); ?>
<?php endif; ?>
                                <?php if (isset($component)) { $__componentOriginalc709ddc147ddde534205d9546b4fb0db = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc709ddc147ddde534205d9546b4fb0db = $attributes; } ?>
<?php $component = App\View\Components\Forms\Radio::resolve(['fieldId' => 'public_taskboard-no','fieldLabel' => __('app.disable'),'fieldValue' => 'disable','fieldName' => 'public_taskboard','checked' => $project->public_taskboard == 'disable'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('forms.radio'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Forms\Radio::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc709ddc147ddde534205d9546b4fb0db)): ?>
<?php $attributes = $__attributesOriginalc709ddc147ddde534205d9546b4fb0db; ?>
<?php unset($__attributesOriginalc709ddc147ddde534205d9546b4fb0db); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc709ddc147ddde534205d9546b4fb0db)): ?>
<?php $component = $__componentOriginalc709ddc147ddde534205d9546b4fb0db; ?>
<?php unset($__componentOriginalc709ddc147ddde534205d9546b4fb0db); ?>
<?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 col-lg-4">
                        <div class="form-group my-3">
                            <label class="f-14 text-dark-grey mb-12 w-100 mt-3"
                                for="usr"><?php echo app('translator')->get('modules.projects.needApproval'); ?></label>
                            <div class="d-flex">
                                <?php if (isset($component)) { $__componentOriginalc709ddc147ddde534205d9546b4fb0db = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc709ddc147ddde534205d9546b4fb0db = $attributes; } ?>
<?php $component = App\View\Components\Forms\Radio::resolve(['fieldId' => 'need_approval_by_admin-yes','fieldLabel' => __('app.enable'),'fieldName' => 'need_approval_by_admin','fieldValue' => '1','checked' => 'true'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('forms.radio'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Forms\Radio::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc709ddc147ddde534205d9546b4fb0db)): ?>
<?php $attributes = $__attributesOriginalc709ddc147ddde534205d9546b4fb0db; ?>
<?php unset($__attributesOriginalc709ddc147ddde534205d9546b4fb0db); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc709ddc147ddde534205d9546b4fb0db)): ?>
<?php $component = $__componentOriginalc709ddc147ddde534205d9546b4fb0db; ?>
<?php unset($__componentOriginalc709ddc147ddde534205d9546b4fb0db); ?>
<?php endif; ?>
                                <?php if (isset($component)) { $__componentOriginalc709ddc147ddde534205d9546b4fb0db = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc709ddc147ddde534205d9546b4fb0db = $attributes; } ?>
<?php $component = App\View\Components\Forms\Radio::resolve(['fieldId' => 'need_approval_by_admin-no','fieldLabel' => __('app.disable'),'fieldValue' => '0','fieldName' => 'need_approval_by_admin','checked' => $project->need_approval_by_admin == '0'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('forms.radio'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Forms\Radio::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc709ddc147ddde534205d9546b4fb0db)): ?>
<?php $attributes = $__attributesOriginalc709ddc147ddde534205d9546b4fb0db; ?>
<?php unset($__attributesOriginalc709ddc147ddde534205d9546b4fb0db); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc709ddc147ddde534205d9546b4fb0db)): ?>
<?php $component = $__componentOriginalc709ddc147ddde534205d9546b4fb0db; ?>
<?php unset($__componentOriginalc709ddc147ddde534205d9546b4fb0db); ?>
<?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <?php if($project->public == 1 && $createPublicProjectPermission == 'all'): ?>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <div class="d-flex mt-2">
                                    <?php if (isset($component)) { $__componentOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3 = $attributes; } ?>
<?php $component = App\View\Components\Forms\Checkbox::resolve(['fieldId' => 'is_private','fieldLabel' => __('modules.projects.createPrivateProject'),'fieldName' => 'private'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('forms.checkbox'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Forms\Checkbox::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3)): ?>
<?php $attributes = $__attributesOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3; ?>
<?php unset($__attributesOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3)): ?>
<?php $component = $__componentOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3; ?>
<?php unset($__componentOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3); ?>
<?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if($project->public == 0 && $createPublicProjectPermission == 'all'): ?>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <div class="d-flex mt-2">
                                    <?php if (isset($component)) { $__componentOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3 = $attributes; } ?>
<?php $component = App\View\Components\Forms\Checkbox::resolve(['fieldId' => 'is_public','fieldLabel' => __('modules.projects.changeToPublicProject'),'fieldName' => 'public'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('forms.checkbox'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Forms\Checkbox::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3)): ?>
<?php $attributes = $__attributesOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3; ?>
<?php unset($__attributesOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3)): ?>
<?php $component = $__componentOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3; ?>
<?php unset($__componentOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3); ?>
<?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>


                    <?php if($editProjectMembersPermission == 'all' || $editPermission == 'all'): ?>
                        <div class="col-md-12 <?php if($project->public == 1): ?> d-none <?php endif; ?>" id="edit_members">
                           <div class="form-group my-3">

                            <?php
                                // Retrieve selected employee IDs and their statuses
                                $selectedEmployeeIds = $project->members->pluck('user_id')->toArray();
                                $selectedEmployees = $employees->filter(function ($employee) use ($selectedEmployeeIds) {
                                    return in_array($employee->id, $selectedEmployeeIds);
                                });

                                // Get the active employees list
                                $activeEmployees = $employees->filter(function ($employee) {
                                    return $employee->status === 'active';
                                });

                                $employeesToShow = $activeEmployees->merge($selectedEmployees->filter(function ($employee) {
                                    return !$employee->is_active;
                                }));
                            ?>
                                <?php if (isset($component)) { $__componentOriginal89b295b0763c93abe0143426334eb5d6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal89b295b0763c93abe0143426334eb5d6 = $attributes; } ?>
<?php $component = App\View\Components\Forms\Label::resolve(['fieldId' => 'selectAssignee','fieldRequired' => 'true','fieldLabel' => __('modules.tasks.assignTo')] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('forms.label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Forms\Label::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal89b295b0763c93abe0143426334eb5d6)): ?>
<?php $attributes = $__attributesOriginal89b295b0763c93abe0143426334eb5d6; ?>
<?php unset($__attributesOriginal89b295b0763c93abe0143426334eb5d6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal89b295b0763c93abe0143426334eb5d6)): ?>
<?php $component = $__componentOriginal89b295b0763c93abe0143426334eb5d6; ?>
<?php unset($__componentOriginal89b295b0763c93abe0143426334eb5d6); ?>
<?php endif; ?>
                                <?php if (isset($component)) { $__componentOriginalcbf9105fd4879d5d6ef9e1f6fe271af7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalcbf9105fd4879d5d6ef9e1f6fe271af7 = $attributes; } ?>
<?php $component = App\View\Components\Forms\InputGroup::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('forms.input-group'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Forms\InputGroup::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                                    <select class="form-control multiple-users" multiple name="member_id[]"
                                        id="selectEmployee" data-live-search="true" data-size="8">
                                        <?php $__currentLoopData = $employeesToShow; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $selected = '';
                                                $isMember = false;
                                            ?>

                                            <?php $__currentLoopData = $project->members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php if($member->user->id == $item->id): ?>
                                                    <?php
                                                        $selected = 'selected';
                                                        $isMember = true;
                                                    ?>
                                                <?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php if($item->status === 'active' || $isMember): ?>
                                                <?php if (isset($component)) { $__componentOriginal6c7097547485b98631a37d273a171e9f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6c7097547485b98631a37d273a171e9f = $attributes; } ?>
<?php $component = App\View\Components\UserOption::resolve(['user' => $item,'selected' => $selected,'pill' => true] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('user-option'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\UserOption::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6c7097547485b98631a37d273a171e9f)): ?>
<?php $attributes = $__attributesOriginal6c7097547485b98631a37d273a171e9f; ?>
<?php unset($__attributesOriginal6c7097547485b98631a37d273a171e9f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6c7097547485b98631a37d273a171e9f)): ?>
<?php $component = $__componentOriginal6c7097547485b98631a37d273a171e9f; ?>
<?php unset($__componentOriginal6c7097547485b98631a37d273a171e9f); ?>
<?php endif; ?>
                                            <?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php if($addEmployeePermission == 'all' || $addEmployeePermission == 'added'): ?>
                                         <?php $__env->slot('append', null, []); ?> 
                                            <button id="add-employee" type="button"
                                                class="btn btn-outline-secondary border-grey"><?php echo app('translator')->get('app.add'); ?></button>
                                         <?php $__env->endSlot(); ?>
                                    <?php endif; ?>
                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalcbf9105fd4879d5d6ef9e1f6fe271af7)): ?>
<?php $attributes = $__attributesOriginalcbf9105fd4879d5d6ef9e1f6fe271af7; ?>
<?php unset($__attributesOriginalcbf9105fd4879d5d6ef9e1f6fe271af7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalcbf9105fd4879d5d6ef9e1f6fe271af7)): ?>
<?php $component = $__componentOriginalcbf9105fd4879d5d6ef9e1f6fe271af7; ?>
<?php unset($__componentOriginalcbf9105fd4879d5d6ef9e1f6fe271af7); ?>
<?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if($project->public == 1 && $editProjectMembersPermission || $editPermission == 'all'): ?>
                        <div class="col-md-12 d-none" id="add_members">
                            <div class="form-group my-3">
                                <?php if (isset($component)) { $__componentOriginal89b295b0763c93abe0143426334eb5d6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal89b295b0763c93abe0143426334eb5d6 = $attributes; } ?>
<?php $component = App\View\Components\Forms\Label::resolve(['fieldId' => 'selectEmployee','fieldRequired' => 'true','fieldLabel' => __('modules.projects.addMemberTitle')] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('forms.label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Forms\Label::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'my-3']); ?>
                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal89b295b0763c93abe0143426334eb5d6)): ?>
<?php $attributes = $__attributesOriginal89b295b0763c93abe0143426334eb5d6; ?>
<?php unset($__attributesOriginal89b295b0763c93abe0143426334eb5d6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal89b295b0763c93abe0143426334eb5d6)): ?>
<?php $component = $__componentOriginal89b295b0763c93abe0143426334eb5d6; ?>
<?php unset($__componentOriginal89b295b0763c93abe0143426334eb5d6); ?>
<?php endif; ?>
                                <?php if (isset($component)) { $__componentOriginalcbf9105fd4879d5d6ef9e1f6fe271af7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalcbf9105fd4879d5d6ef9e1f6fe271af7 = $attributes; } ?>
<?php $component = App\View\Components\Forms\InputGroup::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('forms.input-group'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Forms\InputGroup::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                                    <select class="form-control multiple-users" multiple name="user_id[]"
                                        id="selectEmployee" data-live-search="true" data-size="8">
                                        <?php if($employees != ''): ?>

                                            <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php if (isset($component)) { $__componentOriginal6c7097547485b98631a37d273a171e9f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6c7097547485b98631a37d273a171e9f = $attributes; } ?>
<?php $component = App\View\Components\UserOption::resolve(['user' => $item,'pill' => true,'selected' => request()->has('default_assign') && request('default_assign') == $item->id ||(isset($projectTemplateMembers) && in_array($item->id, $projectTemplateMembers))] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('user-option'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\UserOption::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6c7097547485b98631a37d273a171e9f)): ?>
<?php $attributes = $__attributesOriginal6c7097547485b98631a37d273a171e9f; ?>
<?php unset($__attributesOriginal6c7097547485b98631a37d273a171e9f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6c7097547485b98631a37d273a171e9f)): ?>
<?php $component = $__componentOriginal6c7097547485b98631a37d273a171e9f; ?>
<?php unset($__componentOriginal6c7097547485b98631a37d273a171e9f); ?>
<?php endif; ?>

                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                    </select>

                                    <?php if($addEmployeePermission == 'all' || $addEmployeePermission == 'added'): ?>
                                         <?php $__env->slot('append', null, []); ?> 
                                            <button id="add-employee" type="button"
                                                class="btn btn-outline-secondary border-grey"><?php echo app('translator')->get('app.add'); ?></button>
                                         <?php $__env->endSlot(); ?>
                                    <?php endif; ?>
                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalcbf9105fd4879d5d6ef9e1f6fe271af7)): ?>
<?php $attributes = $__attributesOriginalcbf9105fd4879d5d6ef9e1f6fe271af7; ?>
<?php unset($__attributesOriginalcbf9105fd4879d5d6ef9e1f6fe271af7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalcbf9105fd4879d5d6ef9e1f6fe271af7)): ?>
<?php $component = $__componentOriginalcbf9105fd4879d5d6ef9e1f6fe271af7; ?>
<?php unset($__componentOriginalcbf9105fd4879d5d6ef9e1f6fe271af7); ?>
<?php endif; ?>
                            </div>
                        </div>
                    <?php elseif(in_array('employee', user_roles())): ?>
                        <input type="hidden" name="user_id[]" value="<?php echo e(user()->id); ?>">
                    <?php endif; ?>

                    <div class="col-md-12 col-lg-4">
                        <?php if (isset($component)) { $__componentOriginal67cd5dc9866c6185ad92d933c387fa86 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal67cd5dc9866c6185ad92d933c387fa86 = $attributes; } ?>
<?php $component = App\View\Components\Forms\Select::resolve(['fieldId' => 'project_status','fieldLabel' => __('app.project') . ' ' . __('app.status'),'fieldName' => 'status','search' => 'true'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('forms.select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Forms\Select::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                            <?php $__currentLoopData = $projectStatus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option
                                data-content="<i class='fa fa-circle mr-1 f-15' style='color:<?php echo e($status->color); ?>'></i><?php echo e($status->status_name); ?>"
                                <?php if($project->status == $status->status_name): echo 'selected'; endif; ?>
                                value="<?php echo e($status->status_name); ?>">
                                </option>

                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal67cd5dc9866c6185ad92d933c387fa86)): ?>
<?php $attributes = $__attributesOriginal67cd5dc9866c6185ad92d933c387fa86; ?>
<?php unset($__attributesOriginal67cd5dc9866c6185ad92d933c387fa86); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal67cd5dc9866c6185ad92d933c387fa86)): ?>
<?php $component = $__componentOriginal67cd5dc9866c6185ad92d933c387fa86; ?>
<?php unset($__componentOriginal67cd5dc9866c6185ad92d933c387fa86); ?>
<?php endif; ?>
                    </div>

                    <div class="col-md-12 col-lg-4">
                        <?php if (isset($component)) { $__componentOriginalcf373da1865405df959093f7d4973945 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalcf373da1865405df959093f7d4973945 = $attributes; } ?>
<?php $component = App\View\Components\Forms\Range::resolve(['disabled' => ($project->calculate_task_progress == 'true' ? 'true' : 'false'),'fieldLabel' => __('modules.projects.projectCompletionStatus'),'fieldName' => 'completion_percent','fieldId' => 'completion_percent','fieldValue' => $project->completion_percent] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('forms.range'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Forms\Range::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'mr-0 mr-lg-2 mr-md-2']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalcf373da1865405df959093f7d4973945)): ?>
<?php $attributes = $__attributesOriginalcf373da1865405df959093f7d4973945; ?>
<?php unset($__attributesOriginalcf373da1865405df959093f7d4973945); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalcf373da1865405df959093f7d4973945)): ?>
<?php $component = $__componentOriginalcf373da1865405df959093f7d4973945; ?>
<?php unset($__componentOriginalcf373da1865405df959093f7d4973945); ?>
<?php endif; ?>
                    </div>

                    <div class="col-md-12 col-lg-4">
                        <div class="form-group">
                            <div class="d-flex mt-5">
                                <?php if (isset($component)) { $__componentOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3 = $attributes; } ?>
<?php $component = App\View\Components\Forms\Checkbox::resolve(['fieldId' => 'calculate-task-progress','checked' => ($project->calculate_task_progress == 'true') ? true : false,'fieldLabel' => __('modules.projects.calculateTasksProgress'),'fieldName' => 'calculate_task_progress'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('forms.checkbox'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Forms\Checkbox::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3)): ?>
<?php $attributes = $__attributesOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3; ?>
<?php unset($__attributesOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3)): ?>
<?php $component = $__componentOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3; ?>
<?php unset($__componentOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3); ?>
<?php endif; ?>
                                    <i class="fa fa-question-circle mt-2" title="<?php echo e(__('messages.calculateTaskProgress')); ?>" data-toggle="tooltip"></i>
                            </div>
                        </div>
                    </div>


                </div>

                <h4 class="mb-0 p-20 f-21 font-weight-normal  border-top-grey">
                    <?php echo app('translator')->get('modules.client.clientOtherDetails'); ?></h4>

                <div class="row p-20">
                    <div class="col-lg-4">
                        <?php if (isset($component)) { $__componentOriginal67cd5dc9866c6185ad92d933c387fa86 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal67cd5dc9866c6185ad92d933c387fa86 = $attributes; } ?>
<?php $component = App\View\Components\Forms\Select::resolve(['fieldId' => 'currency_id','fieldLabel' => __('modules.invoices.currency'),'fieldName' => 'currency_id','search' => 'true'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('forms.select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Forms\Select::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                            <?php $__currentLoopData = $currencies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $currency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option <?php if($currency->id == $project->currency_id): echo 'selected'; endif; ?> value="<?php echo e($currency->id); ?>">
                                    <?php echo e($currency->currency_symbol . ' (' . $currency->currency_code . ')'); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal67cd5dc9866c6185ad92d933c387fa86)): ?>
<?php $attributes = $__attributesOriginal67cd5dc9866c6185ad92d933c387fa86; ?>
<?php unset($__attributesOriginal67cd5dc9866c6185ad92d933c387fa86); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal67cd5dc9866c6185ad92d933c387fa86)): ?>
<?php $component = $__componentOriginal67cd5dc9866c6185ad92d933c387fa86; ?>
<?php unset($__componentOriginal67cd5dc9866c6185ad92d933c387fa86); ?>
<?php endif; ?>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <?php if (isset($component)) { $__componentOriginal1fded940a0a5d34bf1b88a1f45916593 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1fded940a0a5d34bf1b88a1f45916593 = $attributes; } ?>
<?php $component = App\View\Components\Forms\Number::resolve(['fieldLabel' => __('modules.projects.projectBudget'),'fieldName' => 'project_budget','fieldId' => 'project_budget','fieldValue' => $project->project_budget,'fieldPlaceholder' => __('placeholders.price')] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('forms.number'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Forms\Number::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'mr-0 mr-lg-2 mr-md-2']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1fded940a0a5d34bf1b88a1f45916593)): ?>
<?php $attributes = $__attributesOriginal1fded940a0a5d34bf1b88a1f45916593; ?>
<?php unset($__attributesOriginal1fded940a0a5d34bf1b88a1f45916593); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1fded940a0a5d34bf1b88a1f45916593)): ?>
<?php $component = $__componentOriginal1fded940a0a5d34bf1b88a1f45916593; ?>
<?php unset($__componentOriginal1fded940a0a5d34bf1b88a1f45916593); ?>
<?php endif; ?>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <?php if (isset($component)) { $__componentOriginal1fded940a0a5d34bf1b88a1f45916593 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1fded940a0a5d34bf1b88a1f45916593 = $attributes; } ?>
<?php $component = App\View\Components\Forms\Number::resolve(['fieldLabel' => __('modules.projects.hours_allocated'),'fieldName' => 'hours_allocated','fieldId' => 'hours_allocated','fieldValue' => $project->hours_allocated,'fieldPlaceholder' => __('placeholders.hourEstimate')] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('forms.number'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Forms\Number::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'mr-0 mr-lg-2 mr-md-2']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1fded940a0a5d34bf1b88a1f45916593)): ?>
<?php $attributes = $__attributesOriginal1fded940a0a5d34bf1b88a1f45916593; ?>
<?php unset($__attributesOriginal1fded940a0a5d34bf1b88a1f45916593); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1fded940a0a5d34bf1b88a1f45916593)): ?>
<?php $component = $__componentOriginal1fded940a0a5d34bf1b88a1f45916593; ?>
<?php unset($__componentOriginal1fded940a0a5d34bf1b88a1f45916593); ?>
<?php endif; ?>
                    </div>

                    <div class="col-md-6 col-lg-4">
                        <div class="form-group">
                            <div class="d-flex mt-5">
                                <?php if (isset($component)) { $__componentOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3 = $attributes; } ?>
<?php $component = App\View\Components\Forms\Checkbox::resolve(['fieldId' => 'manual_timelog','fieldLabel' => __('modules.projects.manualTimelog'),'checked' => ($project->manual_timelog
                                    == 'enable'),'fieldName' => 'manual_timelog'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('forms.checkbox'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Forms\Checkbox::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3)): ?>
<?php $attributes = $__attributesOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3; ?>
<?php unset($__attributesOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3)): ?>
<?php $component = $__componentOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3; ?>
<?php unset($__componentOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3); ?>
<?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-4" id="clientNotification">
                        <div class="form-group">
                            <div class="d-flex mt-5">
                                <?php if (isset($component)) { $__componentOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3 = $attributes; } ?>
<?php $component = App\View\Components\Forms\Checkbox::resolve(['fieldId' => 'client_task_notification','checked' => ($project->allow_client_notification
                                == 'enable'),'fieldLabel' => __('modules.projects.clientTaskNotification'),'fieldName' => 'client_task_notification'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('forms.checkbox'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Forms\Checkbox::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3)): ?>
<?php $attributes = $__attributesOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3; ?>
<?php unset($__attributesOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3)): ?>
<?php $component = $__componentOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3; ?>
<?php unset($__componentOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3); ?>
<?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-4" id="collaboratorNotification">
                        <div class="form-group">
                            <div class="d-flex mt-5">
                                <?php if (isset($component)) { $__componentOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3 = $attributes; } ?>
<?php $component = App\View\Components\Forms\Checkbox::resolve(['fieldId' => 'collaborator_task_notification','checked' => ($project->allow_collaborator_notification
                                == 'enable'),'fieldLabel' => ('Envoyer une notification de tache au collaborateur'),'fieldName' => 'collaborator_task_notification'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('forms.checkbox'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Forms\Checkbox::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3)): ?>
<?php $attributes = $__attributesOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3; ?>
<?php unset($__attributesOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3)): ?>
<?php $component = $__componentOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3; ?>
<?php unset($__componentOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3); ?>
<?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <?php if($editPermission == 'all'): ?>
                        <div class="col-lg-3 col-md-6">
                            <?php
                                $activeEmployees = $employees->filter(function ($employee) {
                                    return $employee->status !== 'deactive';
                                });

                                $selectedEmployee = $employees->firstWhere('id', $project->added_by);

                                if ($selectedEmployee && $selectedEmployee->status === 'deactive') {
                                    $employeesToShow = $activeEmployees->push($selectedEmployee);
                                } else {
                                    $employeesToShow = $activeEmployees;
                                }
                            ?>
                            <?php if (isset($component)) { $__componentOriginal67cd5dc9866c6185ad92d933c387fa86 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal67cd5dc9866c6185ad92d933c387fa86 = $attributes; } ?>
<?php $component = App\View\Components\Forms\Select::resolve(['fieldId' => 'added_by','fieldLabel' => __('app.added').' '.__('app.by'),'fieldName' => 'added_by'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('forms.select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Forms\Select::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                                <option value="">--</option>
                                <?php $__currentLoopData = $employeesToShow; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if (isset($component)) { $__componentOriginal6c7097547485b98631a37d273a171e9f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6c7097547485b98631a37d273a171e9f = $attributes; } ?>
<?php $component = App\View\Components\UserOption::resolve(['user' => $item,'selected' => $project->added_by == $item->id] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('user-option'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\UserOption::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6c7097547485b98631a37d273a171e9f)): ?>
<?php $attributes = $__attributesOriginal6c7097547485b98631a37d273a171e9f; ?>
<?php unset($__attributesOriginal6c7097547485b98631a37d273a171e9f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6c7097547485b98631a37d273a171e9f)): ?>
<?php $component = $__componentOriginal6c7097547485b98631a37d273a171e9f; ?>
<?php unset($__componentOriginal6c7097547485b98631a37d273a171e9f); ?>
<?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal67cd5dc9866c6185ad92d933c387fa86)): ?>
<?php $attributes = $__attributesOriginal67cd5dc9866c6185ad92d933c387fa86; ?>
<?php unset($__attributesOriginal67cd5dc9866c6185ad92d933c387fa86); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal67cd5dc9866c6185ad92d933c387fa86)): ?>
<?php $component = $__componentOriginal67cd5dc9866c6185ad92d933c387fa86; ?>
<?php unset($__componentOriginal67cd5dc9866c6185ad92d933c387fa86); ?>
<?php endif; ?>
                        </div>
                    <?php endif; ?>



                </div>

                <div class="row p-20">
                    <div class="col-md-6 col-lg-3">
                        <div class="form-group">
                            <div class="d-flex mt-5">
                                <?php if (isset($component)) { $__componentOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3 = $attributes; } ?>
<?php $component = App\View\Components\Forms\Checkbox::resolve(['fieldId' => 'miroboard_checkbox','fieldLabel' => __('modules.projects.enableMiroboard'),'fieldName' => 'miroboard_checkbox','checked' => $project ? $project->enable_miroboard : ''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('forms.checkbox'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Forms\Checkbox::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3)): ?>
<?php $attributes = $__attributesOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3; ?>
<?php unset($__attributesOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3)): ?>
<?php $component = $__componentOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3; ?>
<?php unset($__componentOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3); ?>
<?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <input type = "hidden" name = "mention_user_ids" id = "mentionUserId" class ="mention_user_ids">

                    <div class="col-md-6 col-lg-6 <?php echo e(!is_null($project) && $project->enable_miroboard ? '' : 'd-none'); ?>" id="miroboard_detail">
                        <div class="form-group my-3">
                            <div class="row">
                                <div class="col-md-6 mt-6">
                                    <?php if (isset($component)) { $__componentOriginal4e45e801405ab67097982370a6a83cba = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4e45e801405ab67097982370a6a83cba = $attributes; } ?>
<?php $component = App\View\Components\Forms\Text::resolve(['fieldLabel' => __('modules.projects.miroBoardId'),'fieldName' => 'miro_board_id','fieldRequired' => 'true','fieldId' => 'miro_board_id','fieldValue' => $project->miro_board_id] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('forms.text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Forms\Text::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'mr-0 mr-lg-2 mr-md-2']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4e45e801405ab67097982370a6a83cba)): ?>
<?php $attributes = $__attributesOriginal4e45e801405ab67097982370a6a83cba; ?>
<?php unset($__attributesOriginal4e45e801405ab67097982370a6a83cba); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4e45e801405ab67097982370a6a83cba)): ?>
<?php $component = $__componentOriginal4e45e801405ab67097982370a6a83cba; ?>
<?php unset($__componentOriginal4e45e801405ab67097982370a6a83cba); ?>
<?php endif; ?>
                                </div>
                                <div class="col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <div class="d-flex mt-5">
                                    <?php if (isset($component)) { $__componentOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3 = $attributes; } ?>
<?php $component = App\View\Components\Forms\Checkbox::resolve(['fieldId' => 'client_access','fieldLabel' => __('modules.projects.clientMiroAccess'),'fieldName' => 'client_access','checked' => $project ? $project->client_access : ''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('forms.checkbox'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Forms\Checkbox::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3)): ?>
<?php $attributes = $__attributesOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3; ?>
<?php unset($__attributesOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3)): ?>
<?php $component = $__componentOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3; ?>
<?php unset($__componentOriginal9c5d7e5b2e4b8b16cfa941b5e69189f3); ?>
<?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if (isset($component)) { $__componentOriginalfa1d9407bf58c9650823154ec52dea3e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfa1d9407bf58c9650823154ec52dea3e = $attributes; } ?>
<?php $component = App\View\Components\Forms\CustomField::resolve(['fields' => $fields,'model' => $project] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('forms.custom-field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Forms\CustomField::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfa1d9407bf58c9650823154ec52dea3e)): ?>
<?php $attributes = $__attributesOriginalfa1d9407bf58c9650823154ec52dea3e; ?>
<?php unset($__attributesOriginalfa1d9407bf58c9650823154ec52dea3e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfa1d9407bf58c9650823154ec52dea3e)): ?>
<?php $component = $__componentOriginalfa1d9407bf58c9650823154ec52dea3e; ?>
<?php unset($__componentOriginalfa1d9407bf58c9650823154ec52dea3e); ?>
<?php endif; ?>


                <?php if (isset($component)) { $__componentOriginalb19caa501eea72410c04d1917a586963 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb19caa501eea72410c04d1917a586963 = $attributes; } ?>
<?php $component = App\View\Components\FormActions::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('form-actions'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\FormActions::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                    <?php if (isset($component)) { $__componentOriginalcf8d12533ff890e0d6573daf32b7618d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalcf8d12533ff890e0d6573daf32b7618d = $attributes; } ?>
<?php $component = App\View\Components\Forms\ButtonPrimary::resolve(['icon' => 'check'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('forms.button-primary'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Forms\ButtonPrimary::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'save-project-form','class' => 'mr-3']); ?><?php echo app('translator')->get('app.save'); ?>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalcf8d12533ff890e0d6573daf32b7618d)): ?>
<?php $attributes = $__attributesOriginalcf8d12533ff890e0d6573daf32b7618d; ?>
<?php unset($__attributesOriginalcf8d12533ff890e0d6573daf32b7618d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalcf8d12533ff890e0d6573daf32b7618d)): ?>
<?php $component = $__componentOriginalcf8d12533ff890e0d6573daf32b7618d; ?>
<?php unset($__componentOriginalcf8d12533ff890e0d6573daf32b7618d); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginalc35c79ed7e812580313ad04118477974 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc35c79ed7e812580313ad04118477974 = $attributes; } ?>
<?php $component = App\View\Components\Forms\ButtonCancel::resolve(['link' => route('projects.index')] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('forms.button-cancel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Forms\ButtonCancel::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'border-0']); ?><?php echo app('translator')->get('app.cancel'); ?>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc35c79ed7e812580313ad04118477974)): ?>
<?php $attributes = $__attributesOriginalc35c79ed7e812580313ad04118477974; ?>
<?php unset($__attributesOriginalc35c79ed7e812580313ad04118477974); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc35c79ed7e812580313ad04118477974)): ?>
<?php $component = $__componentOriginalc35c79ed7e812580313ad04118477974; ?>
<?php unset($__componentOriginalc35c79ed7e812580313ad04118477974); ?>
<?php endif; ?>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb19caa501eea72410c04d1917a586963)): ?>
<?php $attributes = $__attributesOriginalb19caa501eea72410c04d1917a586963; ?>
<?php unset($__attributesOriginalb19caa501eea72410c04d1917a586963); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb19caa501eea72410c04d1917a586963)): ?>
<?php $component = $__componentOriginalb19caa501eea72410c04d1917a586963; ?>
<?php unset($__componentOriginalb19caa501eea72410c04d1917a586963); ?>
<?php endif; ?>

            </div>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal18ad2e0d264f9740dc73fff715357c28)): ?>
<?php $attributes = $__attributesOriginal18ad2e0d264f9740dc73fff715357c28; ?>
<?php unset($__attributesOriginal18ad2e0d264f9740dc73fff715357c28); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal18ad2e0d264f9740dc73fff715357c28)): ?>
<?php $component = $__componentOriginal18ad2e0d264f9740dc73fff715357c28; ?>
<?php unset($__componentOriginal18ad2e0d264f9740dc73fff715357c28); ?>
<?php endif; ?>

    </div>
</div>


<script>
    $(document).ready(function() {

        $('.custom-date-picker').each(function(ind, el) {
            datepicker(el, {
                position: 'bl',
                ...datepickerConfig
            });
        });

        $(".multiple-users").selectpicker({
            actionsBox: true,
            selectAllText: "<?php echo e(__('modules.permission.selectAll')); ?>",
            deselectAllText: "<?php echo e(__('modules.permission.deselectAll')); ?>",
            multipleSeparator: " ",
            selectedTextFormat: "count > 8",
            countSelectedText: function(selected, total) {
                return selected + " <?php echo e(__('app.membersSelected')); ?> ";
            }
        });

        const dp1 = datepicker('#start_date', {
            position: 'bl',
            dateSelected: new Date("<?php echo e(str_replace('-', '/', $project->start_date)); ?>"),
            onSelect: (instance, date) => {
                dp2.setMin(date);
            },
            ...datepickerConfig
        });

        const dp2 = datepicker('#deadline', {
            position: 'bl',
            dateSelected: new Date("<?php echo e($project->deadline ? str_replace('-', '/', $project->deadline) : str_replace('-', '/', now(company()->timezone))); ?>"),
            onSelect: (instance, date) => {
                dp1.setMax(date);
            },
            ...datepickerConfig
        });

        <?php if($project->deadline == null): ?>
            $('#deadlineBox').hide();
        <?php endif; ?>

        $('#without_deadline').click(function() {
            var check = $('#without_deadline').is(":checked") ? true : false;
            if (check == true) {
                $('#deadlineBox').hide();
                $('#without_deadline').closest('.col-md-6.col-lg-4').removeClass('col-md-6 col-lg-4').addClass('col-md-8');
            } else {
                $('#deadlineBox').show();
                $('#without_deadline').closest('.col-md-8').removeClass('col-md-8').addClass('col-md-6 col-lg-4');
            }
        });
        const atValues = <?php echo json_encode($userData, 15, 512) ?>;

        quillMention(atValues, '#project_summary');

        $('#save-project-form').click(function(e, fromModal) {
            if (fromModal) {
                var note = document.getElementById('project_summary').children[0].innerHTML;
                document.getElementById('project_summary-text').value = note;

                var user = $('#project_summary span[data-id]').map(function(){
                                return $(this).attr('data-id')
                            }).get();

                var mention_user_id  =  $.makeArray(user);
                $('#mentionUserId').val(mention_user_id.join(','));
                const url = "<?php echo e(route('projects.update', $project->id)); ?>";

            $.easyAjax({
                url: url,
                container: '#save-project-data-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                file:true,
                buttonSelector: "#save-project-form",
                data: $('#save-project-data-form').serialize(),
                success: function(response) {
                    if (response.status == 'success') {
                        window.location.href = response.redirectUrl;
                    }
                }
            });
                return;
            }

            // Sinon on affiche le modal de choix de canal
            const url = "<?php echo e(route('projects.select_channels')); ?>";
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });

        $('#addProjectCategory').click(function() {
            const url = "<?php echo e(route('projectCategory.create')); ?>";
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });

        $('#department-setting').click(function() {
            const url = "<?php echo e(route('departments.create')); ?>";
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });

        $('#client_view_task').change(function() {
            $('#clientNotification').toggleClass('d-none');
        });

        $('#is_private').change(function() {
            $('#add_members').toggleClass('d-none');
            $('#edit_members').addClass('d-none');
        });

        $('#is_public').change(function() {
            $('#edit_members').toggleClass('d-none');
            $('#add_members').addClass('d-none');
        });

        $('#miroboard_checkbox').change(function() {
            $('#miroboard_detail').toggleClass('d-none');
        });

        $('#add-client').click(function() {
            $(MODAL_XL).modal('show');

            const url = "<?php echo e(route('clients.create')); ?>";

            $.easyAjax({
                url: url,
                blockUI: true,
                container: MODAL_XL,
                success: function(response) {
                    if (response.status == "success") {
                        $(MODAL_XL + ' .modal-body').html(response.html);
                        $(MODAL_XL + ' .modal-title').html(response.title);
                        init(MODAL_XL);
                    }
                }
            });
        });

        $('#add-employee').click(function() {
            $(MODAL_XL).modal('show');

            const url = "<?php echo e(route('employees.create')); ?>";

            $.easyAjax({
                url: url,
                blockUI: true,
                container: MODAL_XL,
                success: function(response) {
                    if (response.status == "success") {
                        $(MODAL_XL + ' .modal-body').html(response.html);
                        $(MODAL_XL + ' .modal-title').html(response.title);
                        init(MODAL_XL);
                    }
                }
            });
        });

        $('#calculate-task-progress').change(function() {
            if ($(this).is(':checked')) {
                $('#completion_percent').attr('disabled', 'true');
            } else {
                $('#completion_percent').removeAttr('disabled');
            }
        });

        <?php if (isset($component)) { $__componentOriginal7e99458729620cde50aeae9b165a19fc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7e99458729620cde50aeae9b165a19fc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.forms.custom-field-filejs','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('forms.custom-field-filejs'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7e99458729620cde50aeae9b165a19fc)): ?>
<?php $attributes = $__attributesOriginal7e99458729620cde50aeae9b165a19fc; ?>
<?php unset($__attributesOriginal7e99458729620cde50aeae9b165a19fc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7e99458729620cde50aeae9b165a19fc)): ?>
<?php $component = $__componentOriginal7e99458729620cde50aeae9b165a19fc; ?>
<?php unset($__componentOriginal7e99458729620cde50aeae9b165a19fc); ?>
<?php endif; ?>

        init(RIGHT_MODAL);
    });

    $('#save-project-data-form').on('change', '#employee_department', function () {
            let id = $(this).val();
            if (id === '' || id.length === 0) {
                id = 0;
            }
            let userId = '<?php echo e($project->members->pluck("user.id")->implode(",")); ?>';
            let url = "<?php echo e(route('departments.members', ':id')); ?>?userId="+userId;
            url = url.replace(':id', id);

            $.easyAjax({
                url: url,
                type: "GET",
                container: '#save-project-data-form',
                blockUI: true,
                redirect: true,
                success: function (data) {
                    $('#selectEmployee').html(data.data);
                    $('#selectEmployee').selectpicker('refresh');
                }
            })
        });

</script>
<?php /**PATH /home/cp2255957p00/public_html/tacheflux/resources/views/projects/ajax/edit.blade.php ENDPATH**/ ?>