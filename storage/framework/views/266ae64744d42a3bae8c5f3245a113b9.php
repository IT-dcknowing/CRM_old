

<?php
    $donnees = $notification->data ?? [];
    $classe = class_basename($notification->type);

    /* Le libelle le plus parlant que porte la notification. */
    $texte = collect(['heading', 'title', 'name', 'project_name', 'message'])
        ->map(fn ($cle) => $donnees[$cle] ?? null)
        ->first(fn ($valeur) => filled($valeur) && is_string($valeur));

    /* A defaut, le nom de la classe rendu lisible : « TaskInitNotification »
       devient « Task Init Notification ». Ce n'est pas ideal, mais c'est
       preferable a une ligne vide. */
    $intitule = \Illuminate\Support\Str::headline($classe);

    /* Le lien n'est propose que si l'on sait vers quoi pointer.

       On ne devine pas a partir du seul identifiant : « id » designe
       une tache dans une notification de tache, un projet dans une
       notification de projet. Envoyer l'un vers l'autre ouvrirait la
       mauvaise fiche, ou une page d'erreur. */
    $lien = 'javascript:;';

    if (isset($donnees['id']) && is_numeric($donnees['id'])) {
        if (\Illuminate\Support\Str::contains($classe, 'Task')) {
            $lien = route('tasks.show', $donnees['id']);
        }
        elseif (\Illuminate\Support\Str::contains($classe, 'Project')) {
            $lien = route('projects.show', $donnees['id']);
        }
    }
?>

<?php if (isset($component)) { $__componentOriginal42cf517c5716db26aa0e5d62b08b607b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal42cf517c5716db26aa0e5d62b08b607b = $attributes; } ?>
<?php $component = App\View\Components\Cards\Notification::resolve(['notification' => $notification,'link' => $lien,'type' => 'icon','image' => '<i class="fa fa-bell"></i>','title' => $intitule,'text' => $texte ?? '','time' => $notification->created_at] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('cards.notification'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Cards\Notification::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal42cf517c5716db26aa0e5d62b08b607b)): ?>
<?php $attributes = $__attributesOriginal42cf517c5716db26aa0e5d62b08b607b; ?>
<?php unset($__attributesOriginal42cf517c5716db26aa0e5d62b08b607b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal42cf517c5716db26aa0e5d62b08b607b)): ?>
<?php $component = $__componentOriginal42cf517c5716db26aa0e5d62b08b607b; ?>
<?php unset($__componentOriginal42cf517c5716db26aa0e5d62b08b607b); ?>
<?php endif; ?>
<?php /**PATH C:\laragon\www\CRM\resources\views/notifications/all/_repli.blade.php ENDPATH**/ ?>