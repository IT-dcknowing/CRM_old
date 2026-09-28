

<?php if($resultat->total === 0 && empty($resultat->lignes)): ?>

    <div class="dck-import__msg is-err">
        <strong><?php echo app('translator')->get("Le fichier n'a pas pu être lu."); ?></strong>
        <p><?php echo e($resultat->message); ?></p>
    </div>

<?php else: ?>

    
    <div class="dck-import__preview-header mb-3 p-3 bg-white rounded border shadow-sm">
        <div class="d-flex flex-wrap align-items-center justify-content-between">
            <div class="d-flex align-items-center flex-wrap">
                <span class="badge badge-primary px-2.5 py-1.5 mr-2 f-13">
                    <i class="fa fa-file-excel mr-1"></i> <?php echo e($nomFichier ?? __('Fichier chargé')); ?>

                </span>
                <span class="f-15 font-weight-bold text-dark" id="count-total-banner">
                    <span id="count-total-num"><?php echo e($resultat->total); ?></span>
                    <span id="count-total-text"><?php echo e(trans_choice('tâche prête à être importée|tâches prêtes à être importées', $resultat->total)); ?></span>
                </span>
            </div>
            <div class="d-flex align-items-center mt-2 mt-md-0">
                <span class="badge badge-success mr-2 px-2 py-1 f-12" id="badge-valides">
                    <i class="fa fa-check-circle mr-1"></i> <span id="count-valides"><?php echo e($resultat->valides); ?></span> <?php echo app('translator')->get('valides'); ?>
                </span>
                <span class="badge <?php echo e($resultat->enErreur > 0 ? 'badge-danger' : 'badge-secondary'); ?> px-2 py-1 f-12" id="badge-erreurs">
                    <i class="fa fa-exclamation-triangle mr-1"></i> <span id="count-erreurs"><?php echo e($resultat->enErreur); ?></span> <?php echo app('translator')->get('en erreur'); ?>
                </span>
            </div>
        </div>
    </div>

    
    <div class="dck-import__scroll mb-3" style="max-height: 540px; overflow-y: auto;">
        <table class="dck-import__tbl table table-bordered table-hover mb-0" id="table-import-preview">
            <thead class="thead-light" style="position: sticky; top: 0; z-index: 10;">
                <tr>
                    <th style="width: 45px; text-align: center;">#</th>
                    <th style="min-width: 220px;"><?php echo app('translator')->get('app.task'); ?> <span class="text-danger">*</span></th>
                    <th style="min-width: 170px;"><?php echo app('translator')->get('app.project'); ?></th>
                    <th style="min-width: 150px;"><?php echo app('translator')->get('modules.tasks.taskCategory'); ?></th>
                    <th style="min-width: 140px;"><?php echo app('translator')->get('app.status'); ?></th>
                    <th style="min-width: 125px;"><?php echo app('translator')->get('modules.tasks.priority'); ?></th>
                    <th style="min-width: 135px;"><?php echo app('translator')->get('modules.projects.startDate'); ?> <span class="text-danger">*</span></th>
                    <th style="min-width: 135px;"><?php echo app('translator')->get('app.dueDate'); ?></th>
                    <th style="min-width: 180px;"><?php echo app('translator')->get('app.description'); ?></th>
                    <th style="min-width: 140px;"><?php echo app('translator')->get('modules.tasks.assignTo'); ?></th>
                    <th style="width: 50px; text-align: center;"><?php echo app('translator')->get('app.action'); ?></th>
                </tr>
            </thead>
            <tbody id="tbody-import-tasks">
            <?php $__currentLoopData = $resultat->lignes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="row-task <?php echo e($l->erreurs ? 'table-warning' : ''); ?>" data-index="<?php echo e($idx); ?>" id="row-<?php echo e($idx); ?>">
                    <td class="text-center font-weight-bold text-muted align-middle">
                        <span class="row-num"><?php echo e($l->numero); ?></span>
                        <input type="hidden" name="tasks[<?php echo e($idx); ?>][numero]" class="field-numero" value="<?php echo e($l->numero); ?>">
                    </td>

                    
                    <td class="align-middle">
                        <input type="text" 
                               name="tasks[<?php echo e($idx); ?>][heading]" 
                               class="form-control form-control-sm field-heading <?php echo e(empty($l->titre) ? 'is-invalid' : ''); ?>" 
                               value="<?php echo e($l->titre); ?>" 
                               placeholder="<?php echo app('translator')->get('placeholders.task'); ?>" 
                               required>
                        <div class="invalid-feedback f-11"><?php echo app('translator')->get('Le titre est requis'); ?></div>
                    </td>

                    
                    <td class="align-middle">
                        <select name="tasks[<?php echo e($idx); ?>][project_id]" class="form-control form-control-sm field-project">
                            <option value="">-- <?php echo app('translator')->get('Aucun'); ?> --</option>
                            <?php $__currentLoopData = $projets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($p->id); ?>" <?php if($l->projet_id == $p->id): echo 'selected'; endif; ?>>
                                    <?php echo e($p->project_name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </td>

                    
                    <td class="align-middle">
                        <select name="tasks[<?php echo e($idx); ?>][category_id]" class="form-control form-control-sm field-category">
                            <option value="">-- <?php echo app('translator')->get('Aucun'); ?> --</option>
                            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($cat->id); ?>" <?php if($l->categorie_id == $cat->id): echo 'selected'; endif; ?>>
                                    <?php echo e($cat->category_name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </td>

                    
                    <td class="align-middle">
                        <select name="tasks[<?php echo e($idx); ?>][board_column_id]" class="form-control form-control-sm field-status">
                            <?php $__currentLoopData = $statuts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($st->id); ?>" <?php if($l->statut_id == $st->id): echo 'selected'; endif; ?>>
                                    <?php echo e($st->column_name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </td>

                    
                    <td class="align-middle">
                        <select name="tasks[<?php echo e($idx); ?>][priority]" class="form-control form-control-sm field-priority">
                            <option value="high" <?php if($l->priorite == 'high'): echo 'selected'; endif; ?>>🔴 <?php echo app('translator')->get('modules.tasks.high'); ?></option>
                            <option value="medium" <?php if($l->priorite == 'medium'): echo 'selected'; endif; ?>>🟡 <?php echo app('translator')->get('modules.tasks.medium'); ?></option>
                            <option value="low" <?php if($l->priorite == 'low'): echo 'selected'; endif; ?>>🟢 <?php echo app('translator')->get('modules.tasks.low'); ?></option>
                        </select>
                    </td>

                    
                    <td class="align-middle">
                        <input type="text" 
                               name="tasks[<?php echo e($idx); ?>][start_date]" 
                               class="form-control form-control-sm field-start-date import-datepicker <?php echo e(empty($l->debut_str) ? 'is-invalid' : ''); ?>" 
                               value="<?php echo e($l->debut_str); ?>" 
                               placeholder="<?php echo e(company()->date_format); ?>" 
                               required>
                        <div class="invalid-feedback f-11"><?php echo app('translator')->get('La date de début est requise'); ?></div>
                    </td>

                    
                    <td class="align-middle">
                        <input type="text" 
                               name="tasks[<?php echo e($idx); ?>][due_date]" 
                               class="form-control form-control-sm field-due-date import-datepicker" 
                               value="<?php echo e($l->fin_str); ?>" 
                               placeholder="<?php echo e(company()->date_format); ?>">
                    </td>

                    
                    <td class="align-middle">
                        <input type="text" 
                               name="tasks[<?php echo e($idx); ?>][description]" 
                               class="form-control form-control-sm field-description" 
                               value="<?php echo e($l->description); ?>" 
                               placeholder="<?php echo app('translator')->get('Description...'); ?>">
                    </td>

                    
                    <td class="align-middle">
                        <span class="badge badge-light border text-dark p-2 w-100 text-left font-weight-normal" title="<?php echo app('translator')->get('Assigné automatiquement à votre compte'); ?>" data-toggle="tooltip">
                            <i class="fa fa-user-lock text-primary mr-1"></i> <?php echo e(user()->name); ?>

                        </span>
                    </td>

                    
                    <td class="text-center align-middle">
                        <button type="button" class="btn btn-sm btn-outline-danger border-0 btn-delete-row" title="<?php echo app('translator')->get('app.delete'); ?>" data-toggle="tooltip">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <tr id="no-task-row" class="<?php echo e(count($resultat->lignes) > 0 ? 'd-none' : ''); ?>">
                    <td colspan="11" class="text-center py-4 text-muted">
                        <i class="fa fa-inbox fa-2x mb-2 d-block text-lightest"></i>
                        <?php echo app('translator')->get('Aucune tâche dans le tableau. Cliquez sur « Ajouter une tâche » pour en ajouter une.'); ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    
    <div id="import-validation-summary" class="d-none alert alert-warning py-2 px-3 mb-3">
        <i class="fa fa-exclamation-circle mr-1"></i> <span id="validation-summary-text"></span>
    </div>

    
    <div class="d-flex flex-wrap align-items-center justify-content-between pt-2 border-top">
        <div class="d-flex align-items-center flex-wrap my-1">
            <button type="button" class="btn btn-secondary rounded f-14 p-2 mr-2 btn-ajouter-tache" id="btn-ajouter-tache">
                <i class="fa fa-plus mr-1"></i> <?php echo app('translator')->get('Ajouter une tâche'); ?>
            </button>
            <button type="button" class="btn btn-secondary rounded f-14 p-2 btn-annuler-import">
                <i class="fa fa-times mr-1"></i> <?php echo app('translator')->get('Annuler l\'import'); ?>
            </button>
        </div>

        <div class="d-flex align-items-center flex-wrap my-1">
            <button type="button" class="btn btn-primary rounded f-14 p-2 font-weight-bold btn-confirmer-import" data-jeton="<?php echo e($jeton); ?>">
                <i class="fa fa-check-circle mr-1"></i> <?php echo app('translator')->get('Importer les tâches'); ?>
            </button>
        </div>
    </div>

    
    <template id="template-nouvelle-tache">
        <tr class="row-task table-warning" data-index="__INDEX__" id="row-__INDEX__">
            <td class="text-center font-weight-bold text-muted align-middle">
                <span class="row-num">__NUMERO__</span>
                <input type="hidden" name="tasks[__INDEX__][numero]" class="field-numero" value="__NUMERO__">
            </td>

            
            <td class="align-middle">
                <input type="text" 
                       name="tasks[__INDEX__][heading]" 
                       class="form-control form-control-sm field-heading is-invalid" 
                       value="" 
                       placeholder="<?php echo app('translator')->get('placeholders.task'); ?>" 
                       required>
                <div class="invalid-feedback f-11"><?php echo app('translator')->get('Le titre est requis'); ?></div>
            </td>

            
            <td class="align-middle">
                <select name="tasks[__INDEX__][project_id]" class="form-control form-control-sm field-project">
                    <option value="">-- <?php echo app('translator')->get('Aucun'); ?> --</option>
                    <?php $__currentLoopData = $projets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($p->id); ?>"><?php echo e($p->project_name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </td>

            
            <td class="align-middle">
                <select name="tasks[__INDEX__][category_id]" class="form-control form-control-sm field-category">
                    <option value="">-- <?php echo app('translator')->get('Aucun'); ?> --</option>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($cat->id); ?>"><?php echo e($cat->category_name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </td>

            
            <td class="align-middle">
                <select name="tasks[__INDEX__][board_column_id]" class="form-control form-control-sm field-status">
                    <?php $__currentLoopData = $statuts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($st->id); ?>" <?php if($st->slug === 'incomplete' || $loop->first): echo 'selected'; endif; ?>>
                            <?php echo e($st->column_name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </td>

            
            <td class="align-middle">
                <select name="tasks[__INDEX__][priority]" class="form-control form-control-sm field-priority">
                    <option value="high">🔴 <?php echo app('translator')->get('modules.tasks.high'); ?></option>
                    <option value="medium" selected>🟡 <?php echo app('translator')->get('modules.tasks.medium'); ?></option>
                    <option value="low">🟢 <?php echo app('translator')->get('modules.tasks.low'); ?></option>
                </select>
            </td>

            
            <td class="align-middle">
                <input type="text" 
                       name="tasks[__INDEX__][start_date]" 
                       class="form-control form-control-sm field-start-date import-datepicker <?php echo e(empty(now()->format(company()->date_format)) ? 'is-invalid' : ''); ?>" 
                       value="<?php echo e(now()->format(company()->date_format)); ?>" 
                       placeholder="<?php echo e(company()->date_format); ?>" 
                       required>
                <div class="invalid-feedback f-11"><?php echo app('translator')->get('La date de début est requise'); ?></div>
            </td>

            
            <td class="align-middle">
                <input type="text" 
                       name="tasks[__INDEX__][due_date]" 
                       class="form-control form-control-sm field-due-date import-datepicker" 
                       value="" 
                       placeholder="<?php echo e(company()->date_format); ?>">
            </td>

            
            <td class="align-middle">
                <input type="text" 
                       name="tasks[__INDEX__][description]" 
                       class="form-control form-control-sm field-description" 
                       value="" 
                       placeholder="<?php echo app('translator')->get('Description...'); ?>">
            </td>

            
            <td class="align-middle">
                <span class="badge badge-light border text-dark p-2 w-100 text-left font-weight-normal" title="<?php echo app('translator')->get('Assigné automatiquement à votre compte'); ?>" data-toggle="tooltip">
                    <i class="fa fa-user-lock text-primary mr-1"></i> <?php echo e(user()->name); ?>

                </span>
            </td>

            
            <td class="text-center align-middle">
                <button type="button" class="btn btn-sm btn-outline-danger border-0 btn-delete-row" title="<?php echo app('translator')->get('app.delete'); ?>" data-toggle="tooltip">
                    <i class="fa fa-trash"></i>
                </button>
            </td>
        </tr>
    </template>

<?php endif; ?>
<?php /**PATH C:\laragon\www\CRM\resources\views/tasks/ajax/import_apercu.blade.php ENDPATH**/ ?>