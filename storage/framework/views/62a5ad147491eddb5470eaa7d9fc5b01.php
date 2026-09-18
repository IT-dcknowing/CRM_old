

<?php if($resultat->total === 0): ?>

    <div class="dck-import__msg is-err">
        <strong><?php echo app('translator')->get("Le fichier n'a pas pu être lu."); ?></strong>
        <p><?php echo e($resultat->message); ?></p>
    </div>

<?php else: ?>

    
    <div class="dck-import__bilan">
        <div class="dck-import__k">
            <span class="dck-import__kl"><?php echo app('translator')->get('Lignes détectées'); ?></span>
            <span class="dck-import__kv"><?php echo e($resultat->total); ?></span>
        </div>
        <div class="dck-import__k is-ok">
            <span class="dck-import__kl"><?php echo app('translator')->get('Valides'); ?></span>
            <span class="dck-import__kv"><?php echo e($resultat->valides); ?></span>
        </div>
        <div class="dck-import__k <?php echo e($resultat->enErreur > 0 ? 'is-err' : ''); ?>">
            <span class="dck-import__kl"><?php echo app('translator')->get('En erreur'); ?></span>
            <span class="dck-import__kv"><?php echo e($resultat->enErreur); ?></span>
        </div>
    </div>

    
    <?php if($resultat->enErreur > 0): ?>
        <div class="dck-import__msg is-err">
            <strong><?php echo e($resultat->enErreur); ?>

                <?php echo e(trans_choice('ligne comporte une erreur|lignes comportent des erreurs', $resultat->enErreur)); ?>.</strong>
            <p><?php echo app('translator')->get("Aucune tâche ne sera créée tant que le fichier n'est pas corrigé. Reprenez votre fichier, corrigez les lignes ci-dessous, puis déposez-le à nouveau."); ?></p>

            <ul class="dck-import__erreurs">
                <?php $__currentLoopData = array_slice($resultat->erreurs, 0, 40); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($e); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>

            <?php if(count($resultat->erreurs) > 40): ?>
                <p class="dck-import__note">
                    … <?php echo e(count($resultat->erreurs) - 40); ?> <?php echo app('translator')->get('autres erreurs non affichées.'); ?>
                </p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    
    <div class="dck-import__scroll">
        <table class="dck-import__tbl">
            <thead>
                <tr>
                    <th><?php echo app('translator')->get('Ligne'); ?></th>
                    <th><?php echo app('translator')->get('Tâche'); ?></th>
                    <th><?php echo app('translator')->get('Projet'); ?></th>
                    <th><?php echo app('translator')->get('Statut'); ?></th>
                    <th><?php echo app('translator')->get('Assigné à'); ?></th>
                    <th><?php echo app('translator')->get('Début'); ?></th>
                    <th><?php echo app('translator')->get('Fin'); ?></th>
                    <th><?php echo app('translator')->get('État'); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php $__currentLoopData = $resultat->lignes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="<?php echo e($l->erreurs ? 'est-erreur' : ''); ?>">
                    <td class="dck-import__n"><?php echo e($l->numero); ?></td>
                    <td><?php echo e(\Illuminate\Support\Str::limit($l->titre, 46) ?: '—'); ?></td>
                    <td><?php echo e($l->projet->project_name ?? ($l->projetNom ?: '—')); ?></td>
                    <td><?php echo e($l->statut->column_name ?? '—'); ?></td>
                    <td><?php echo e($l->gensNoms ? implode(', ', $l->gensNoms) : '—'); ?></td>
                    <td class="dck-import__n"><?php echo e($l->debut?->format(company()->date_format) ?: '—'); ?></td>
                    <td class="dck-import__n"><?php echo e($l->fin?->format(company()->date_format) ?: '—'); ?></td>
                    <td>
                        <?php if($l->erreurs): ?>
                            <span class="dck-import__p is-err"><?php echo app('translator')->get('erreur'); ?></span>
                        <?php else: ?>
                            <span class="dck-import__p is-ok"><?php echo app('translator')->get('valide'); ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>

    
    <?php if($resultat->ok && $jeton): ?>
        <div class="dck-import__valider">
            <button type="button" class="btn btn-primary rounded f-14 p-2" id="btn-confirmer-import"
                    data-jeton="<?php echo e($jeton); ?>">
                <i class="fa fa-check mr-1"></i>
                <?php echo e(sprintf(__('Importer les %d tâches'), $resultat->total)); ?>

            </button>
            <span class="dck-import__note"><?php echo app('translator')->get("Les tâches seront créées comme si elles avaient été saisies une à une."); ?></span>
        </div>
    <?php endif; ?>

<?php endif; ?>
<?php /**PATH C:\laragon\www\dc_knowing\CRM\resources\views/tasks/ajax/import_apercu.blade.php ENDPATH**/ ?>