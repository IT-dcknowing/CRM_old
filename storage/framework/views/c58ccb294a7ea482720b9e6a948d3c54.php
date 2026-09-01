<link rel="stylesheet" href="<?php echo e(asset('vendor/css/dropzone.min.css')); ?>">
<style>
    #message-new .ql-editor {
        border: 1px solid #a3a3a3;
        border-radius: 6px;
        padding-left: 6px !important;
        height: 100% !important;
    }

    .ql-editor-disabled {
        border-radius: 6px;
        background-color: rgba(124, 0, 0, 0.2);
        transition-duration: 0.5s;
    }

    .ql-toolbar {
        display: none !important;
    }

</style>
<div class="modal-header">
    <h5 class="modal-title" id="modelHeading"><?php echo app('translator')->get("modules.messages.startConversation"); ?></h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">×</span></button>
</div>
<div class="modal-body">
    <?php if (isset($component)) { $__componentOriginal18ad2e0d264f9740dc73fff715357c28 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal18ad2e0d264f9740dc73fff715357c28 = $attributes; } ?>
<?php $component = App\View\Components\Form::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('form'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Form::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'createConversationForm']); ?>
        <div class="row">

            <div class="col-md-12 <?php echo e(isset($clientId) ? 'd-none' : ''); ?>">
                <div class="form-group">
                    <div class="d-flex">

                        <?php if(!in_array('client', user_roles())): ?>
                            <?php if(
                            $messageSetting->allow_client_employee == 'yes' && in_array('employee', user_roles())
                            || $messageSetting->allow_client_admin == 'yes' && in_array('admin', user_roles())
                            ): ?>
                                <?php if (isset($component)) { $__componentOriginalc709ddc147ddde534205d9546b4fb0db = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc709ddc147ddde534205d9546b4fb0db = $attributes; } ?>
<?php $component = App\View\Components\Forms\Radio::resolve(['fieldId' => 'user-type-employee','fieldLabel' => __('app.member'),'fieldValue' => 'employee','fieldName' => 'user_type','checked' => 'true'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
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
<?php $component = App\View\Components\Forms\Radio::resolve(['fieldId' => 'user-type-client','fieldLabel' => __('app.client'),'fieldValue' => 'client','fieldName' => 'user_type'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
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
                            <?php else: ?>
                                <input type="hidden" name="user_type" value="employee">
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if(in_array('client', user_roles())): ?>
                            <?php if($messageSetting->allow_client_employee == 'yes' || $messageSetting->allow_client_admin == 'yes'): ?>
                                <input type="hidden" name="user_type" value="employee">
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <input type="hidden" name="mention_user_id" id="mentionUserIds" class="mention_user_ids">

            <div class="col-md-12" id="member-list">
                <div class="form-group">
                    <?php if (isset($component)) { $__componentOriginal67cd5dc9866c6185ad92d933c387fa86 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal67cd5dc9866c6185ad92d933c387fa86 = $attributes; } ?>
<?php $component = App\View\Components\Forms\Select::resolve(['fieldId' => 'selectEmployee','fieldLabel' => __('modules.messages.chooseMember'),'fieldName' => 'user_id[]','search' => 'true','fieldRequired' => 'true','multiple' => 'true'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('forms.select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Forms\Select::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                        <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if (isset($component)) { $__componentOriginal6c7097547485b98631a37d273a171e9f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6c7097547485b98631a37d273a171e9f = $attributes; } ?>
<?php $component = App\View\Components\UserOption::resolve(['user' => $item,'pill' => true] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
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
            </div>

            <div class="col-md-12 d-none" id="client-list">
                <div class="form-group">
                    <?php if (isset($component)) { $__componentOriginal67cd5dc9866c6185ad92d933c387fa86 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal67cd5dc9866c6185ad92d933c387fa86 = $attributes; } ?>
<?php $component = App\View\Components\Forms\Select::resolve(['fieldId' => 'client_id','fieldLabel' => __('modules.messages.chooseClient'),'fieldName' => 'client_id[]','search' => 'true','fieldRequired' => 'true','multiple' => 'true'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('forms.select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Forms\Select::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                        <?php if(isset($clients)): ?>
                            <?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if (isset($component)) { $__componentOriginal6c7097547485b98631a37d273a171e9f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6c7097547485b98631a37d273a171e9f = $attributes; } ?>
<?php $component = App\View\Components\UserOption::resolve(['user' => $item,'pill' => true,'selected' => (isset($clientId) && $clientId == $item->id),'additionalText' => $item->company_name ?? ''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
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
            </div>

            <input type="hidden" name="task_id" value="<?php echo e($taskId ?? ''); ?>">

            <div class="col-md-12">
                <div class="form-group">
                    <?php if (isset($component)) { $__componentOriginal67cd5dc9866c6185ad92d933c387fa86 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal67cd5dc9866c6185ad92d933c387fa86 = $attributes; } ?>
<?php $component = App\View\Components\Forms\Select::resolve(['fieldId' => 'channel','fieldLabel' => __('modules.tasks.channel'),'fieldName' => 'channel','search' => 'false','fieldRequired' => 'true'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('forms.select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Forms\Select::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                        <option value="internal" <?php echo e(!isset($taskId) ? 'selected' : ''); ?>><?php echo app('translator')->get('app.chatInternal'); ?></option>
                        <option value="email" <?php echo e(isset($taskId) ? 'selected' : ''); ?>><?php echo app('translator')->get('app.email'); ?></option>
                        <option value="whatsapp"><?php echo app('translator')->get('app.whatsapp'); ?></option>
                        <option value="sms"><?php echo app('translator')->get('app.sms'); ?></option>
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
            </div>

            <div class="col-md-12">
                <div class="form-group">
                    <?php if (isset($component)) { $__componentOriginal89b295b0763c93abe0143426334eb5d6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal89b295b0763c93abe0143426334eb5d6 = $attributes; } ?>
<?php $component = App\View\Components\Forms\Label::resolve(['fieldLabel' => __('app.message'),'fieldRequired' => 'true','fieldId' => 'description'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
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
                    <div id="message-new"></div>
                    <input type="hidden" name="types" value="modal"/>
                    <textarea name="message" id="new-message-text" class="d-none"></textarea>
                </div>
            </div>

            <div class="col-md-12 my-5">
                <?php if (isset($component)) { $__componentOriginal22e84ee8172e1045de536542f4ffc9a0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal22e84ee8172e1045de536542f4ffc9a0 = $attributes; } ?>
<?php $component = App\View\Components\Forms\FileMultiple::resolve(['fieldLabel' => __('app.menu.addFile'),'fieldName' => 'file','fieldId' => 'message-file-upload-dropzone'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('forms.file-multiple'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Forms\FileMultiple::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'mr-0 mr-lg-2 mr-md-2']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal22e84ee8172e1045de536542f4ffc9a0)): ?>
<?php $attributes = $__attributesOriginal22e84ee8172e1045de536542f4ffc9a0; ?>
<?php unset($__attributesOriginal22e84ee8172e1045de536542f4ffc9a0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal22e84ee8172e1045de536542f4ffc9a0)): ?>
<?php $component = $__componentOriginal22e84ee8172e1045de536542f4ffc9a0; ?>
<?php unset($__componentOriginal22e84ee8172e1045de536542f4ffc9a0); ?>
<?php endif; ?>
                <input type="hidden" name="message_id" id="message_id">
                <input type="hidden" name="type" id="message">

                
                <input type="hidden" name="user_list" id="user_list">
                <input type="hidden" name="message_list" id="message_list">
                <input type="hidden" name="receiver_id" id="receiver_id">
            </div>

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
<div class="modal-footer">
    <?php if (isset($component)) { $__componentOriginalc35c79ed7e812580313ad04118477974 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc35c79ed7e812580313ad04118477974 = $attributes; } ?>
<?php $component = App\View\Components\Forms\ButtonCancel::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('forms.button-cancel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Forms\ButtonCancel::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['data-dismiss' => 'modal','class' => 'border-0 mr-3']); ?><?php echo app('translator')->get('app.cancel'); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc35c79ed7e812580313ad04118477974)): ?>
<?php $attributes = $__attributesOriginalc35c79ed7e812580313ad04118477974; ?>
<?php unset($__attributesOriginalc35c79ed7e812580313ad04118477974); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc35c79ed7e812580313ad04118477974)): ?>
<?php $component = $__componentOriginalc35c79ed7e812580313ad04118477974; ?>
<?php unset($__componentOriginalc35c79ed7e812580313ad04118477974); ?>
<?php endif; ?>
    <?php if (isset($component)) { $__componentOriginalcf8d12533ff890e0d6573daf32b7618d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalcf8d12533ff890e0d6573daf32b7618d = $attributes; } ?>
<?php $component = App\View\Components\Forms\ButtonPrimary::resolve(['icon' => 'check'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('forms.button-primary'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Forms\ButtonPrimary::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'save-message']); ?><?php echo app('translator')->get('app.send'); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalcf8d12533ff890e0d6573daf32b7618d)): ?>
<?php $attributes = $__attributesOriginalcf8d12533ff890e0d6573daf32b7618d; ?>
<?php unset($__attributesOriginalcf8d12533ff890e0d6573daf32b7618d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalcf8d12533ff890e0d6573daf32b7618d)): ?>
<?php $component = $__componentOriginalcf8d12533ff890e0d6573daf32b7618d; ?>
<?php unset($__componentOriginalcf8d12533ff890e0d6573daf32b7618d); ?>
<?php endif; ?>
</div>

<script>

    $('#selectEmployee, #client_id').selectpicker();

    var atValues = <?php echo json_encode($userData, 15, 512) ?>;
    quillMention(atValues, '#message-new');

    $("input[name=user_type]").click(function () {
        if ($(this).val() == 'employee') {
            $('#member-list').removeClass('d-none');
            $('#client-list').addClass('d-none');
            // Activer employee, désactiver client (évite la soumission des valeurs cachées)
            $('#selectEmployee').prop('disabled', false);
            $('#client_id').prop('disabled', true);
            // Remettre le canal par défaut sur internal pour les employés
            $('#channel').selectpicker('val', 'internal');
        } else {
            $('#member-list').addClass('d-none');
            $('#client-list').removeClass('d-none');
            // Désactiver employee, activer client
            $('#selectEmployee').prop('disabled', true);
            $('#client_id').prop('disabled', false);
            // Forcer le canal email quand on sélectionne un client
            $('#channel').selectpicker('val', 'email');
        }
    });

    /* Upload images */
    Dropzone.autoDiscover = false;

    //Dropzone class
    taskDropzone1 = new Dropzone("#message-file-upload-dropzone", {
        dictDefaultMessage: "<?php echo e(__('app.dragDrop')); ?>",
        url: "<?php echo e(route('message-file.store')); ?>",
        headers: {
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        },
        paramName: "file",
        maxFilesize: DROPZONE_MAX_FILESIZE,
        maxFiles: DROPZONE_MAX_FILES,
        autoProcessQueue: false,
        uploadMultiple: true,
        addRemoveLinks: true,
        parallelUploads: DROPZONE_MAX_FILES,
        acceptedFiles: DROPZONE_FILE_ALLOW,
        init: function () {
            taskDropzone1 = this;
            this.on("success", function (file, response) {
                $('#message_list').val(response.message_list);
                if (typeof showContent === "function") {
                    showContent(response.message_list);
                    $(MODAL_LG).modal('hide');
                } else {
                    setContent(response);
                }
                $.easyUnblockUI();
                taskDropzone1.removeAllFiles(true);
            })
        }
    });
    taskDropzone1.on('sending', function (file, xhr, formData) {
        var ids = $('#message_id').val();
        formData.append('message_id', ids);
        formData.append('type', 'message');
        formData.append('receiver_id', $('#receiver_id').val());
        $.easyBlockUI();
    });
    taskDropzone1.on('uploadprogress', function () {
        $.easyBlockUI();
    });
    taskDropzone1.on('removedfile', function () {
        var grp = $('div#file-upload-dropzone').closest(".form-group");
        var label = $('div#file-upload-box').siblings("label");
        $(grp).removeClass("has-error");
        $(label).removeClass("is-invalid");
    });
    taskDropzone1.on('error', function (file, message) {
        taskDropzone1.removeFile(file);
        var grp = $('div#file-upload-dropzone').closest(".form-group");
        var label = $('div#file-upload-box').siblings("label");
        $(grp).find(".help-block").remove();
        var helpBlockContainer = $(grp);

        if (helpBlockContainer.length == 0) {
            helpBlockContainer = $(grp);
        }

        helpBlockContainer.append('<div class="help-block invalid-feedback">' + message + '</div>');
        $(grp).addClass("has-error");
        $(label).addClass("is-invalid");

    });

    $('#save-message').click(function () {
        var note = document.getElementById('message-new').children[0].innerHTML;
        document.getElementById('new-message-text').value = note;
        var mention_user_id = $('#message-new span[data-id]').map(function () {
            return $(this).attr('data-id')
        }).get();
        $('#mentionUserIds').val(mention_user_id.join(','));

        // Channel Validation
        var channel = $('#channel').val();
        var error = false;
        var selectedRecipients = [];
        var userType = $("input[name='user_type']").is(':radio') ? $("input[name='user_type']:checked").val() : $("input[name='user_type']").val();

        if (userType == 'employee') {
            selectedRecipients = $('#selectEmployee option:selected').map(function() {
                return { name: $(this).text().trim(), email: $(this).data('email'), mobile: $(this).data('mobile') };
            }).get();
        } else {
            selectedRecipients = $('#client_id option:selected').map(function() {
                return { name: $(this).text().trim(), email: $(this).data('email'), mobile: $(this).data('mobile') };
            }).get();
        }

        if (selectedRecipients.length === 0) {
            Swal.fire({
                icon: 'error',
                text: "<?php echo e(__('messages.selectRecipient')); ?>",
            });
            return false;
        }

        selectedRecipients.forEach(function(recipient) {
            if (channel === 'email' && (!recipient.email || recipient.email === '')) {
                error = "L'email est manquant pour : " + recipient.name;
            } else if ((channel === 'sms' || channel === 'whatsapp') && (!recipient.mobile || recipient.mobile === '')) {
                error = "Le numéro de téléphone est manquant pour : " + recipient.name;
            }
        });

        if (error) {
            Swal.fire({
                icon: 'error',
                text: error,
            });
            return false;
        }

        var url = "<?php echo e(route('messages.store')); ?>";
        $.easyAjax({
            url: url,
            container: '#createConversationForm',
            disableButton: true,
            blockUI: true,
            buttonSelector: "#save-message",
            type: "POST",
            data: $('#createConversationForm').serialize(),
            success: function (response) {
                if (response.status === 'success') {
                    if (taskDropzone1.getQueuedFiles().length > 0) {
                        message_id = response.message_id;
                        $('#message_id').val(response.message_id);
                        taskDropzone1.processQueue();
                    } else {
                        if (typeof showContent === "function") {
                             showContent(response.message_list);
                             $(MODAL_LG).modal('hide');
                        } else {
                             setContent(response);
                        }
                    }
                }
            }
        })
    });

    function setContent(response) {
        if (!response) return;

        <?php if(isset($client) && !isset($taskId)): ?>
        let clientId = $('#client_id').val();
        var redirectUrl = "<?php echo e(route('messages.index')); ?>?clientId=" + clientId;
        window.location.href = redirectUrl;
        <?php endif; ?>

        if (response.user_list) {
            document.getElementById('msgLeft').innerHTML = response.user_list;
        }
        if (response.message_list) {
            document.getElementById('chatBox').innerHTML = response.message_list;
            $('#sendMessageForm').removeClass('d-none');
        }

        if (response.receiver_id) {
            $('#current_user_id').val(response.receiver_id);
            $('#receiver_id').val(response.receiver_id);
            $('.message-user').html(response.userName);
            $('.show-user-messages').removeClass('active');
            $('#user-no-' + response.receiver_id + ' a').addClass('active');
        }

        $(MODAL_LG).modal('hide');
        scrollChat();
    }

    // If request comes from project overview tab where client id is set, then it will select that client name default
    <?php if(isset($client) || isset($taskId)): ?>
        $("#user-type-client").prop("checked", true);
        $('#member-list').addClass('d-none');
        $('#client-list').removeClass('d-none');
        // Désactiver employee pour ne pas soumettre ses valeurs
        $('#selectEmployee').prop('disabled', true);
        $('#client_id').prop('disabled', false);
        // Pré-sélectionner email comme canal pour les clients
        setTimeout(() => { $('#channel').selectpicker('val', 'email'); }, 100);
        <?php if(isset($taskId)): ?>
            $('#channel').selectpicker('val', 'email');
            
            setTimeout(() => {
                let taskHeading = "<?php echo $task->heading; ?>";
                let taskId = "<?php echo e($task->id); ?>";
                let project = "<?php echo e($task->project ? $task->project->project_name : '--'); ?>";
                let status = "<?php echo e($task->boardColumn ? $task->boardColumn->column_name : '--'); ?>";
                let dueDate = "<?php echo e($task->due_date ? $task->due_date->format($global->date_format) : '--'); ?>";
                let users = "<?php echo $task->users->pluck('name')->implode(', '); ?>";

                let content = "<h2><strong>Communication : [" + taskHeading + " #" + taskId + "]</strong></h2>";
                content += "<p><strong>Diligence :</strong> " + project + "</p>";
                content += "<p><strong>Statut :</strong> " + status + "</p>";
                content += "<p><strong>Date d'échéance :</strong> " + dueDate + "</p>";
                content += "<p><strong>Responsable(s) :</strong> " + users + "</p>";
                content += "<hr>";
                content += "<p>Bonjour,</p>";
                content += "<p>Nous revenons vers vous concernant l'avancement de la tâche : <strong>" + taskHeading + "</strong>.</p>";
                quill.root.innerHTML = content;
            }, 500);
        <?php endif; ?>
    <?php endif; ?>

    init('#createConversationForm');
</script>
<?php /**PATH /home/cp2255957p00/public_html/tacheflux/resources/views/messages/create.blade.php ENDPATH**/ ?>