<div class="form-group my-3">
    <?php if (isset($component)) { $__componentOriginal89b295b0763c93abe0143426334eb5d6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal89b295b0763c93abe0143426334eb5d6 = $attributes; } ?>
<?php $component = App\View\Components\Forms\Label::resolve(['fieldId' => 'chosen_channels','fieldLabel' => __('Canaux de notification')] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
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
    <div class="d-flex flex-wrap mt-2">
        <div class="custom-control custom-checkbox custom-control-inline mr-4">
            <input type="checkbox" class="custom-control-input" id="channel_email" name="chosen_channels[]" value="email" checked>
            <label class="custom-control-label pt-1" for="channel_email">Email</label>
        </div>
        <div class="custom-control custom-checkbox custom-control-inline mr-4">
            <input type="checkbox" class="custom-control-input" id="channel_whatsapp" name="chosen_channels[]" value="whatsapp" checked>
            <label class="custom-control-label pt-1" for="channel_whatsapp">WhatsApp</label>
        </div>
        <div class="custom-control custom-checkbox custom-control-inline">
            <input type="checkbox" class="custom-control-input" id="channel_sms" name="chosen_channels[]" value="sms">
            <label class="custom-control-label pt-1" for="channel_sms">SMS</label>
        </div>
    </div>
    <small class="form-text text-muted">Sélectionnez les moyens par lesquels vous souhaitez notifier le client et les collaborateurs.</small>
</div>
<?php /**PATH /home/cp2255957p00/public_html/tacheflux/resources/views/components/forms/select-channels.blade.php ENDPATH**/ ?>