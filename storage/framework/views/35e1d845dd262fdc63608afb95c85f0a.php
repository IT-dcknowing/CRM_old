


<?php if (isset($component)) { $__componentOriginale44c97c7bfb598419dda3f1c66ab979c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale44c97c7bfb598419dda3f1c66ab979c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dck.page-head','data' => ['titre' => __('Importer des taches'),'description' => __('Creez plusieurs taches d\'un coup a partir d\'un fichier tableur.')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('dck.page-head'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['titre' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Importer des taches')),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Creez plusieurs taches d\'un coup a partir d\'un fichier tableur.'))]); ?>
     <?php $__env->slot('actions', null, []); ?> 
        <?php if (isset($component)) { $__componentOriginal29acd9b76240152ae380821b082bd629 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal29acd9b76240152ae380821b082bd629 = $attributes; } ?>
<?php $component = App\View\Components\Forms\LinkSecondary::resolve(['link' => route('tasks.index'),'icon' => 'list'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('forms.link-secondary'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Forms\LinkSecondary::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
            <?php echo app('translator')->get('app.menu.tasks'); ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal29acd9b76240152ae380821b082bd629)): ?>
<?php $attributes = $__attributesOriginal29acd9b76240152ae380821b082bd629; ?>
<?php unset($__attributesOriginal29acd9b76240152ae380821b082bd629); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal29acd9b76240152ae380821b082bd629)): ?>
<?php $component = $__componentOriginal29acd9b76240152ae380821b082bd629; ?>
<?php unset($__componentOriginal29acd9b76240152ae380821b082bd629); ?>
<?php endif; ?>
     <?php $__env->endSlot(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale44c97c7bfb598419dda3f1c66ab979c)): ?>
<?php $attributes = $__attributesOriginale44c97c7bfb598419dda3f1c66ab979c; ?>
<?php unset($__attributesOriginale44c97c7bfb598419dda3f1c66ab979c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale44c97c7bfb598419dda3f1c66ab979c)): ?>
<?php $component = $__componentOriginale44c97c7bfb598419dda3f1c66ab979c; ?>
<?php unset($__componentOriginale44c97c7bfb598419dda3f1c66ab979c); ?>
<?php endif; ?>

<div class="dck-import">

    
    <div class="dck-import__top">

        
        <div class="dck-card dck-import__col dck-import__col--modele">
            <div class="dck-card__head d-flex">
                <h4 class="mb-0"><span class="dck-import__num">1</span> <?php echo app('translator')->get('Telechargez le modele'); ?></h4>
            </div>

            <div class="card-body">
                <p class="dck-import__intro">
                    <?php echo app('translator')->get("Le modele contient les colonnes attendues et des menus deroulants batis sur vos propres projets, collaborateurs et domaines."); ?>
                </p>

                <a class="btn btn-primary rounded f-14 p-2" href="<?php echo e(route('tasks.import.modele')); ?>">
                    <i class="fa fa-download mr-1"></i>
                    <?php echo app('translator')->get('Telecharger le modele Excel'); ?>
                </a>

                <div class="table-responsive mt-4">
                    <table class="table dck-import__cols">
                        <thead>
                            <tr>
                                <th><?php echo app('translator')->get('Colonne'); ?></th>
                                <th><?php echo app('translator')->get('Obligatoire'); ?></th>
                                <th><?php echo app('translator')->get('Ce qui est attendu'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            $aide = [
                                'heading'     => __("Le titre de la tache."),
                                'project'     => __("Un projet existant, a choisir dans la liste."),
                                'start_date'  => __('Format :') . ' ' . company()->date_format,
                                'due_date'    => __('Meme format. Doit etre posterieure a la date de debut.'),
                                'status'      => __('Un des statuts du tableau ; voir la liste ci-dessous.'),
                                'assigned'    => __("Le nom d'un collaborateur, a choisir dans la liste."),
                                'category'    => __("Un domaine existant, a choisir dans la liste."),
                                'description' => __('Texte libre.'),
                            ];
                        ?>
                        <?php $__currentLoopData = $colonnes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><strong><?php echo e($c['nom']); ?></strong></td>
                                <td>
                                    <?php if($c['requis']): ?>
                                        <span class="dck-import__p is-req"><?php echo app('translator')->get('oui'); ?></span>
                                    <?php else: ?>
                                        <span class="dck-import__p"><?php echo app('translator')->get('non'); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($aide[$c['cle']] ?? ''); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>

                <p class="dck-import__note">
                    <strong><?php echo app('translator')->get('Statuts reconnus'); ?> :</strong>
                    <?php echo e(\App\Models\TaskboardColumn::pluck('column_name')->implode(' - ')); ?>.
                    <br>
                    <?php echo app('translator')->get("La casse et les accents n'ont pas d'importance."); ?>
                    <?php echo app('translator')->get("Sans statut, la tache est creee en Incomplete."); ?>
                </p>

                <p class="dck-import__note">
                    <i class="fa fa-user-check mr-1"></i>
                    <strong><?php echo app('translator')->get('Assignation automatique'); ?> :</strong>
                    <?php echo app('translator')->get("Toutes les taches importees vous sont automatiquement assignees."); ?>
                </p>
            </div>
        </div>

        
        <div class="dck-card dck-import__col dck-import__col--fichier">
            <div class="dck-card__head d-flex">
                <h4 class="mb-0"><span class="dck-import__num">2</span> <?php echo app('translator')->get('Deposez votre fichier'); ?></h4>
            </div>

            <div class="card-body">
                <form id="form-import-taches" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>

                    <div class="form-group mb-3">
                        <label for="projet_defaut" class="f-14 text-dark-grey mb-2">
                            <?php echo app('translator')->get('Projet par defaut'); ?>
                            <span class="f-12 text-lightest"><?php echo app('translator')->get('facultatif'); ?></span>
                        </label>

                        <select name="projet_defaut" id="projet_defaut" class="form-control f-14">
                            <option value=""><?php echo app('translator')->get('Aucun — le projet est lu dans le fichier'); ?></option>
                            <?php $__currentLoopData = $projets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($p->id); ?>"><?php echo e($p->project_name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>

                        <p class="dck-import__note">
                            <?php echo app('translator')->get("Applique aux lignes dont la colonne Projet est vide."); ?>
                        </p>
                    </div>

                    <div class="form-group mb-3">
                        <label for="import_file" class="f-14 text-dark-grey mb-2">
                            <?php echo app('translator')->get('Fichier'); ?> <sup class="text-danger">*</sup>
                        </label>

                        <div class="dck-import__dropzone" id="dck-dropzone">
                            <input type="file" name="import_file" id="import_file"
                                   class="dck-import__dropzone-input" accept=".xlsx,.xls,.ods,.csv" required>
                            <div class="dck-import__dropzone-label">
                                <i class="fa fa-cloud-upload-alt dck-import__dropzone-icon"></i>
                                <span class="dck-import__dropzone-text" id="dck-dropzone-text">
                                    <?php echo app('translator')->get('Glissez votre fichier ici ou cliquez pour choisir'); ?>
                                </span>
                                <span class="dck-import__note mt-1 d-block">
                                    <?php echo app('translator')->get('Formats acceptes : xlsx, xls, ods, csv.'); ?>
                                    <?php echo e(__('Maximum :')); ?> <?php echo e(\App\Imports\TaskImport::MAXIMUM_LIGNES); ?> <?php echo e(__('lignes par import.')); ?>

                                </span>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary rounded f-14 p-2 w-100" id="btn-analyser">
                        <i class="fa fa-search mr-1"></i> <?php echo app('translator')->get('Analyser le fichier'); ?>
                    </button>
                </form>
            </div>
        </div>

    </div>

    
    <div class="dck-card mb-3 d-none" id="etape-apercu">
        <div class="dck-card__head d-flex">
            <h4 class="mb-0"><span class="dck-import__num">3</span> <?php echo app('translator')->get('Verifiez avant de confirmer'); ?></h4>
        </div>

        <div class="card-body" id="zone-apercu"></div>
    </div>

</div>

<script>
(function () {
    'use strict';

    var form    = document.getElementById('form-import-taches'),
        etape   = document.getElementById('etape-apercu'),
        zone    = document.getElementById('zone-apercu'),
        bouton  = document.getElementById('btn-analyser');

    if (!form) { return; }

    /* Zone de depot : met a jour le libelle quand un fichier est choisi */
    var champFichier = document.getElementById('import_file'),
        labelDropzone = document.getElementById('dck-dropzone-text'),
        dropzone = document.getElementById('dck-dropzone');

    if (champFichier && labelDropzone) {
        champFichier.addEventListener('change', function () {
            labelDropzone.textContent = champFichier.files.length
                ? champFichier.files[0].name
                : '<?php echo e(__("Glissez votre fichier ici ou cliquez pour choisir")); ?>';
            dropzone.classList.toggle('has-file', !!champFichier.files.length);
        });
    }

    /* Affiche un message d'erreur dans la zone apercu et fait defiler jusqu'a lui. */
    function afficherErreur(texte) {
        zone.innerHTML = '<div class="dck-import__msg is-err"><strong><?php echo e(__("Erreur")); ?></strong><p>' + texte + '</p></div>';
        etape.classList.remove('d-none');
        etape.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    /* Parse la reponse JSON de maniere securisee : retourne null si le
       corps n'est pas du JSON valide (Laravel peut renvoyer du HTML
       dans certains cas d'erreur 500). */
    function parseJson(reponse) {
        return reponse.text().then(function (texte) {
            try {
                return { code: reponse.status, corps: JSON.parse(texte) };
            } catch (e) {
                return { code: reponse.status, corps: { status: 'fail', message: texte.substring(0, 200) } };
            }
        });
    }

    /* ── Etape 2 : analyse du fichier ──────────────────────────── */

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        var champ = document.getElementById('import_file');

        if (!champ || !champ.files.length) { return; }

        bouton.disabled = true;
        bouton.innerHTML = '<i class="fa fa-spinner fa-spin mr-1"></i> <?php echo e(__("Analyse en cours")); ?>';

        var donnees = new FormData(form);

        fetch('<?php echo e(route("tasks.import.analyse")); ?>', {
            method : 'POST',
            body   : donnees,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept'          : 'application/json'
            }
        })
        .then(parseJson)
        .then(function (r) {
            if (r.code === 422) {
                var errs = r.corps.errors || {};
                afficherErreur(errs.import_file
                    ? errs.import_file[0]
                    : (r.corps.message || '<?php echo e(__("Le fichier n\'a pas pu etre analyse.")); ?>'));
                return;
            }

            if (!r.corps.html) {
                afficherErreur(r.corps.message || '<?php echo e(__("Le fichier n\'a pas pu etre analyse.")); ?>');
                return;
            }

            zone.innerHTML = r.corps.html;
            etape.classList.remove('d-none');
            etape.scrollIntoView({ behavior: 'smooth', block: 'start' });
        })
        .catch(function (err) {
            afficherErreur('<?php echo e(__("Une erreur reseau est survenue. Verifiez votre connexion et reessayez.")); ?>');
        })
        .finally(function () {
            bouton.disabled = false;
            bouton.innerHTML = '<i class="fa fa-search mr-1"></i> <?php echo e(__("Analyser le fichier")); ?>';
        });
    });

    /* ── Etape 3 : confirmation de l'importation ───────────────── */

    /* Le bouton est injecte avec l'apercu : on ecoute via delegation
       sur zone, qui existe des le chargement de la page. */
    zone.addEventListener('click', function (e) {
        var b = e.target.closest('#btn-confirmer-import');

        if (!b) { return; }

        b.disabled = true;
        b.innerHTML = '<i class="fa fa-spinner fa-spin mr-1"></i> <?php echo e(__("Importation en cours")); ?>';

        var d = new FormData();
        d.append('_token', '<?php echo e(csrf_token()); ?>');
        d.append('jeton',  b.dataset.jeton);

        /* Transmet le meme projet par defaut : la confirmation relit
           le fichier conserve cote serveur, elle doit le relire dans
           les memes termes que l'analyse. */
        var projetSelect = document.getElementById('projet_defaut');
        d.append('projet_defaut', projetSelect ? projetSelect.value : '');

        fetch('<?php echo e(route("tasks.import.process")); ?>', {
            method : 'POST',
            body   : d,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept'          : 'application/json'
            }
        })
        .then(parseJson)
        .then(function (r) {
            if (r.corps && r.corps.status === 'success') {
                /* Redirection vers le tableau de bord des taches. */
                window.location.href = r.corps.redirectUrl || '<?php echo e(route("taskboards.index")); ?>';
                return;
            }

            /* Echec serveur : on reaffiche le message et on libere le bouton. */
            var msg = (r.corps && r.corps.message)
                ? r.corps.message
                : '<?php echo e(__("L\'importation a echoue. Veuillez reessayer.")); ?>';

            afficherErreur(msg);
            b.disabled = false;
            b.innerHTML = '<i class="fa fa-check mr-1"></i> <?php echo e(__("Confirmer l\'importation")); ?>';
        })
        .catch(function () {
            afficherErreur('<?php echo e(__("Une erreur reseau est survenue. Verifiez votre connexion et reessayez.")); ?>');
            b.disabled = false;
            b.innerHTML = '<i class="fa fa-check mr-1"></i> <?php echo e(__("Confirmer l\'importation")); ?>';
        });
    });

})();
</script><?php /**PATH C:\laragon\www\CRM\resources\views/tasks/ajax/import.blade.php ENDPATH**/ ?>