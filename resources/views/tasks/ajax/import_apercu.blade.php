{{--
    DataTable de Prévisualisation et d'Édition Interactive des Tâches avant Import
--}}

@if ($resultat->total === 0 && empty($resultat->lignes))

    <div class="dck-import__msg is-err">
        <strong>@lang("Le fichier n'a pas pu être lu.")</strong>
        <p>{{ $resultat->message }}</p>
    </div>

@else

    {{-- ── En-tête de prévisualisation (Informations & Compteurs) ────────────────── --}}
    <div class="dck-import__preview-header mb-3 p-3 bg-white rounded border shadow-sm">
        <div class="d-flex flex-wrap align-items-center justify-content-between">
            <div class="d-flex align-items-center flex-wrap">
                <span class="badge badge-primary px-2.5 py-1.5 mr-2 f-13">
                    <i class="fa fa-file-excel mr-1"></i> {{ $nomFichier ?? __('Fichier chargé') }}
                </span>
                <span class="f-15 font-weight-bold text-dark" id="count-total-banner">
                    <span id="count-total-num">{{ $resultat->total }}</span>
                    <span id="count-total-text">{{ trans_choice('tâche prête à être importée|tâches prêtes à être importées', $resultat->total) }}</span>
                </span>
            </div>
            <div class="d-flex align-items-center mt-2 mt-md-0">
                <span class="badge badge-success mr-2 px-2 py-1 f-12" id="badge-valides">
                    <i class="fa fa-check-circle mr-1"></i> <span id="count-valides">{{ $resultat->valides }}</span> @lang('valides')
                </span>
                <span class="badge {{ $resultat->enErreur > 0 ? 'badge-danger' : 'badge-secondary' }} px-2 py-1 f-12" id="badge-erreurs">
                    <i class="fa fa-exclamation-triangle mr-1"></i> <span id="count-erreurs">{{ $resultat->enErreur }}</span> @lang('en erreur')
                </span>
            </div>
        </div>
    </div>

    {{-- ── DataTable interactif d'édition ────────────────────────────────── --}}
    <div class="dck-import__scroll mb-3" style="max-height: 540px; overflow-y: auto;">
        <table class="dck-import__tbl table table-bordered table-hover mb-0" id="table-import-preview">
            <thead class="thead-light" style="position: sticky; top: 0; z-index: 10;">
                <tr>
                    <th style="width: 45px; text-align: center;">#</th>
                    <th style="min-width: 220px;">@lang('app.task') <span class="text-danger">*</span></th>
                    <th style="min-width: 170px;">@lang('app.project')</th>
                    <th style="min-width: 150px;">@lang('modules.tasks.taskCategory')</th>
                    <th style="min-width: 140px;">@lang('app.status')</th>
                    <th style="min-width: 125px;">@lang('modules.tasks.priority')</th>
                    <th style="min-width: 135px;">@lang('modules.projects.startDate') <span class="text-danger">*</span></th>
                    <th style="min-width: 135px;">@lang('app.dueDate')</th>
                    <th style="min-width: 180px;">@lang('app.description')</th>
                    <th style="min-width: 140px;">@lang('modules.tasks.assignTo')</th>
                    <th style="width: 50px; text-align: center;">@lang('app.action')</th>
                </tr>
            </thead>
            <tbody id="tbody-import-tasks">
            @foreach ($resultat->lignes as $idx => $l)
                <tr class="row-task {{ $l->erreurs ? 'table-warning' : '' }}" data-index="{{ $idx }}" id="row-{{ $idx }}">
                    <td class="text-center font-weight-bold text-muted align-middle">
                        <span class="row-num">{{ $l->numero }}</span>
                        <input type="hidden" name="tasks[{{ $idx }}][numero]" class="field-numero" value="{{ $l->numero }}">
                    </td>

                    {{-- 1. Titre (Texte requis) --}}
                    <td class="align-middle">
                        <input type="text" 
                               name="tasks[{{ $idx }}][heading]" 
                               class="form-control form-control-sm field-heading {{ empty($l->titre) ? 'is-invalid' : '' }}" 
                               value="{{ $l->titre }}" 
                               placeholder="@lang('placeholders.task')" 
                               required>
                        <div class="invalid-feedback f-11">@lang('Le titre est requis')</div>
                    </td>

                    {{-- 2. Projet (Select) --}}
                    <td class="align-middle">
                        <select name="tasks[{{ $idx }}][project_id]" class="form-control form-control-sm field-project">
                            <option value="">-- @lang('Aucun') --</option>
                            @foreach ($projets as $p)
                                <option value="{{ $p->id }}" @selected($l->projet_id == $p->id)>
                                    {{ $p->project_name }}
                                </option>
                            @endforeach
                        </select>
                    </td>

                    {{-- 3. Catégorie / Domaine (Select) --}}
                    <td class="align-middle">
                        <select name="tasks[{{ $idx }}][category_id]" class="form-control form-control-sm field-category">
                            <option value="">-- @lang('Aucun') --</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" @selected($l->categorie_id == $cat->id)>
                                    {{ $cat->category_name }}
                                </option>
                            @endforeach
                        </select>
                    </td>

                    {{-- 4. Statut (Select) --}}
                    <td class="align-middle">
                        <select name="tasks[{{ $idx }}][board_column_id]" class="form-control form-control-sm field-status">
                            @foreach ($statuts as $st)
                                <option value="{{ $st->id }}" @selected($l->statut_id == $st->id)>
                                    {{ $st->column_name }}
                                </option>
                            @endforeach
                        </select>
                    </td>

                    {{-- 5. Priorité (Select) --}}
                    <td class="align-middle">
                        <select name="tasks[{{ $idx }}][priority]" class="form-control form-control-sm field-priority">
                            <option value="high" @selected($l->priorite == 'high')>🔴 @lang('modules.tasks.high')</option>
                            <option value="medium" @selected($l->priorite == 'medium')>🟡 @lang('modules.tasks.medium')</option>
                            <option value="low" @selected($l->priorite == 'low')>🟢 @lang('modules.tasks.low')</option>
                        </select>
                    </td>

                    {{-- 6. Date début (Date picker) --}}
                    <td class="align-middle">
                        <input type="text" 
                               name="tasks[{{ $idx }}][start_date]" 
                               class="form-control form-control-sm field-start-date import-datepicker {{ empty($l->debut_str) ? 'is-invalid' : '' }}" 
                               value="{{ $l->debut_str }}" 
                               placeholder="{{ company()->date_format }}" 
                               required>
                        <div class="invalid-feedback f-11">@lang('La date de début est requise')</div>
                    </td>

                    {{-- 7. Date fin (Date picker) --}}
                    <td class="align-middle">
                        <input type="text" 
                               name="tasks[{{ $idx }}][due_date]" 
                               class="form-control form-control-sm field-due-date import-datepicker" 
                               value="{{ $l->fin_str }}" 
                               placeholder="{{ company()->date_format }}">
                    </td>

                    {{-- 8. Description (Text input) --}}
                    <td class="align-middle">
                        <input type="text" 
                               name="tasks[{{ $idx }}][description]" 
                               class="form-control form-control-sm field-description" 
                               value="{{ $l->description }}" 
                               placeholder="@lang('Description...')">
                    </td>

                    {{-- 9. Assigné à (Fixe non éditable : utilisateur connecté) --}}
                    <td class="align-middle">
                        <span class="badge badge-light border text-dark p-2 w-100 text-left font-weight-normal" title="@lang('Assigné automatiquement à votre compte')" data-toggle="tooltip">
                            <i class="fa fa-user-lock text-primary mr-1"></i> {{ user()->name }}
                        </span>
                    </td>

                    {{-- 10. Actions (Supprimer de la liste temporaire) --}}
                    <td class="text-center align-middle">
                        <button type="button" class="btn btn-sm btn-outline-danger border-0 btn-delete-row" title="@lang('app.delete')" data-toggle="tooltip">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            @endforeach

                <tr id="no-task-row" class="{{ count($resultat->lignes) > 0 ? 'd-none' : '' }}">
                    <td colspan="11" class="text-center py-4 text-muted">
                        <i class="fa fa-inbox fa-2x mb-2 d-block text-lightest"></i>
                        @lang('Aucune tâche dans le tableau. Cliquez sur « Ajouter une tâche » pour en ajouter une.')
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- ── Zone d'avertissements et alertes ───────────────────────────────── --}}
    <div id="import-validation-summary" class="d-none alert alert-warning py-2 px-3 mb-3">
        <i class="fa fa-exclamation-circle mr-1"></i> <span id="validation-summary-text"></span>
    </div>

    {{-- ── Actions inférieures (En bas du DataTable) ───────────────────────── --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between pt-2 border-top">
        <div class="d-flex align-items-center flex-wrap my-1">
            <button type="button" class="btn btn-secondary rounded f-14 p-2 mr-2 btn-ajouter-tache" id="btn-ajouter-tache">
                <i class="fa fa-plus mr-1"></i> @lang('Ajouter une tâche')
            </button>
            <button type="button" class="btn btn-secondary rounded f-14 p-2 btn-annuler-import">
                <i class="fa fa-times mr-1"></i> @lang('Annuler l\'import')
            </button>
        </div>

        <div class="d-flex align-items-center flex-wrap my-1">
            <button type="button" class="btn btn-primary rounded f-14 p-2 font-weight-bold btn-confirmer-import" data-jeton="{{ $jeton }}">
                <i class="fa fa-check-circle mr-1"></i> @lang('Importer les tâches')
            </button>
        </div>
    </div>

    {{-- ── Template pour l'ajout dynamique d'une nouvelle tâche ───────────── --}}
    <template id="template-nouvelle-tache">
        <tr class="row-task table-warning" data-index="__INDEX__" id="row-__INDEX__">
            <td class="text-center font-weight-bold text-muted align-middle">
                <span class="row-num">__NUMERO__</span>
                <input type="hidden" name="tasks[__INDEX__][numero]" class="field-numero" value="__NUMERO__">
            </td>

            {{-- 1. Titre (Texte requis) --}}
            <td class="align-middle">
                <input type="text" 
                       name="tasks[__INDEX__][heading]" 
                       class="form-control form-control-sm field-heading is-invalid" 
                       value="" 
                       placeholder="@lang('placeholders.task')" 
                       required>
                <div class="invalid-feedback f-11">@lang('Le titre est requis')</div>
            </td>

            {{-- 2. Projet (Select) --}}
            <td class="align-middle">
                <select name="tasks[__INDEX__][project_id]" class="form-control form-control-sm field-project">
                    <option value="">-- @lang('Aucun') --</option>
                    @foreach ($projets as $p)
                        <option value="{{ $p->id }}">{{ $p->project_name }}</option>
                    @endforeach
                </select>
            </td>

            {{-- 3. Catégorie / Domaine (Select) --}}
            <td class="align-middle">
                <select name="tasks[__INDEX__][category_id]" class="form-control form-control-sm field-category">
                    <option value="">-- @lang('Aucun') --</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->category_name }}</option>
                    @endforeach
                </select>
            </td>

            {{-- 4. Statut (Select) --}}
            <td class="align-middle">
                <select name="tasks[__INDEX__][board_column_id]" class="form-control form-control-sm field-status">
                    @foreach ($statuts as $st)
                        <option value="{{ $st->id }}" @selected($st->slug === 'incomplete' || $loop->first)>
                            {{ $st->column_name }}
                        </option>
                    @endforeach
                </select>
            </td>

            {{-- 5. Priorité (Select) --}}
            <td class="align-middle">
                <select name="tasks[__INDEX__][priority]" class="form-control form-control-sm field-priority">
                    <option value="high">🔴 @lang('modules.tasks.high')</option>
                    <option value="medium" selected>🟡 @lang('modules.tasks.medium')</option>
                    <option value="low">🟢 @lang('modules.tasks.low')</option>
                </select>
            </td>

            {{-- 6. Date début (Date picker) --}}
            <td class="align-middle">
                <input type="text" 
                       name="tasks[__INDEX__][start_date]" 
                       class="form-control form-control-sm field-start-date import-datepicker {{ empty(now()->format(company()->date_format)) ? 'is-invalid' : '' }}" 
                       value="{{ now()->format(company()->date_format) }}" 
                       placeholder="{{ company()->date_format }}" 
                       required>
                <div class="invalid-feedback f-11">@lang('La date de début est requise')</div>
            </td>

            {{-- 7. Date fin (Date picker) --}}
            <td class="align-middle">
                <input type="text" 
                       name="tasks[__INDEX__][due_date]" 
                       class="form-control form-control-sm field-due-date import-datepicker" 
                       value="" 
                       placeholder="{{ company()->date_format }}">
            </td>

            {{-- 8. Description (Text input) --}}
            <td class="align-middle">
                <input type="text" 
                       name="tasks[__INDEX__][description]" 
                       class="form-control form-control-sm field-description" 
                       value="" 
                       placeholder="@lang('Description...')">
            </td>

            {{-- 9. Assigné à (Fixe non éditable : connecté) --}}
            <td class="align-middle">
                <span class="badge badge-light border text-dark p-2 w-100 text-left font-weight-normal" title="@lang('Assigné automatiquement à votre compte')" data-toggle="tooltip">
                    <i class="fa fa-user-lock text-primary mr-1"></i> {{ user()->name }}
                </span>
            </td>

            {{-- 10. Actions (Supprimer de la liste temporaire) --}}
            <td class="text-center align-middle">
                <button type="button" class="btn btn-sm btn-outline-danger border-0 btn-delete-row" title="@lang('app.delete')" data-toggle="tooltip">
                    <i class="fa fa-trash"></i>
                </button>
            </td>
        </tr>
    </template>

@endif
