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
    {{-- ── En-tête de prévisualisation (Informations & Compteurs) ────────────────── --}}
    <div class="dck-import__preview-header mb-3 p-3 bg-white rounded border shadow-sm">
        <div class="d-flex flex-wrap align-items-center justify-content-between">
            <div class="d-flex align-items-center flex-wrap">
                <span class="badge badge-primary px-2.5 py-1.5 mr-2 f-13">
                    <i class="fa fa-file-excel mr-1"></i> {{ $nomFichier ?? __('Fichier chargé') }}
                </span>
                <span class="f-15 font-weight-bold text-dark" id="count-total-banner">
                    <span id="count-total-num">{{ $resultat->total }}</span>
                    <span id="count-total-text">{{ trans_choice('tâche détectée|tâches détectées', $resultat->total) }}</span>
                </span>
            </div>
            <div class="d-flex align-items-center mt-2 mt-md-0">
                <span class="badge badge-warning text-dark px-2.5 py-1.5 f-12 font-weight-normal {{ $resultat->enErreur > 0 ? '' : 'd-none' }}" id="badge-incompletes">
                    <i class="fa fa-edit mr-1"></i> <span id="count-incompletes">{{ $resultat->enErreur }}</span> @lang('à compléter')
                </span>
            </div>
        </div>
    </div>

    {{-- ── Styles spécifiques pour la lisibilité et l'expérience utilisateur douce ── --}}
    <style>
        .dck-import__tbl th {
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
            background-color: #f8fafc;
        }
        .dck-import__tbl td {
            vertical-align: top !important;
            padding: 8px 8px !important;
        }
        .dck-auto-expand {
            min-height: 38px;
            height: auto;
            resize: vertical;
            line-height: 1.45;
            word-break: break-word;
            white-space: pre-wrap;
            overflow-y: hidden;
            font-size: 12.5px;
            padding: 6px 9px;
            border-radius: 4px;
            width: 100%;
            transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        }
        .dck-auto-expand:focus {
            overflow-y: auto;
            background-color: #ffffff;
        }
        .dck-import__tbl td select.form-control-sm,
        .dck-import__tbl td input.form-control-sm {
            height: 38px;
            font-size: 12.5px;
            border-radius: 4px;
        }
        /* Mise en surbrillance douce au clic sur Importer si un champ est manquant */
        .field-missing {
            border-color: #e07a5f !important;
            background-color: #fffaf8 !important;
            box-shadow: 0 0 0 2px rgba(224, 122, 95, 0.2) !important;
        }
        .field-missing::placeholder {
            color: #c95d3f !important;
        }
    </style>

    {{-- ── DataTable interactif d'édition ────────────────────────────────── --}}
    <div class="dck-import__scroll mb-3" style="max-height: 560px; overflow-y: auto;">
        <table class="dck-import__tbl table table-bordered table-hover mb-0" id="table-import-preview">
            <thead class="thead-light" style="position: sticky; top: 0; z-index: 10;">
                <tr>
                    <th style="width: 45px; text-align: center;">#</th>
                    <th style="min-width: 250px; max-width: 380px;">@lang('app.task') <span class="text-danger">*</span></th>
                    <th style="min-width: 165px;">@lang('app.project')</th>
                    <th style="min-width: 145px;">@lang('modules.tasks.taskCategory')</th>
                    <th style="min-width: 135px;">@lang('app.status')</th>
                    <th style="min-width: 120px;">@lang('modules.tasks.priority')</th>
                    <th style="min-width: 130px;">@lang('modules.projects.startDate') <span class="text-danger">*</span></th>
                    <th style="min-width: 130px;">@lang('app.dueDate')</th>
                    <th style="min-width: 220px; max-width: 350px;">@lang('app.description')</th>
                    <th style="min-width: 135px;">@lang('modules.tasks.assignTo')</th>
                    <th style="width: 45px; text-align: center;">@lang('app.action')</th>
                </tr>
            </thead>
            <tbody id="tbody-import-tasks">
            @foreach ($resultat->lignes as $idx => $l)
                <tr class="row-task" data-index="{{ $idx }}" id="row-{{ $idx }}">
                    <td class="text-center font-weight-bold text-muted align-middle">
                        <span class="row-num">{{ $l->numero }}</span>
                        <input type="hidden" name="tasks[{{ $idx }}][numero]" class="field-numero" value="{{ $l->numero }}">
                    </td>

                    {{-- 1. Titre (Texte requis - auto-extensible pour lecture complète) --}}
                    <td>
                        <textarea name="tasks[{{ $idx }}][heading]" 
                                  class="form-control form-control-sm field-heading dck-auto-expand" 
                                  placeholder="@lang('Titre de la tâche...')" 
                                  rows="1" 
                                  required 
                                  title="{{ $l->titre }}">{{ $l->titre }}</textarea>
                    </td>

                    {{-- 2. Projet (Select) --}}
                    <td>
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
                    <td>
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
                    <td>
                        <select name="tasks[{{ $idx }}][board_column_id]" class="form-control form-control-sm field-status">
                            @foreach ($statuts as $st)
                                <option value="{{ $st->id }}" @selected($l->statut_id == $st->id)>
                                    {{ $st->column_name }}
                                </option>
                            @endforeach
                        </select>
                    </td>

                    {{-- 5. Priorité (Select) --}}
                    <td>
                        <select name="tasks[{{ $idx }}][priority]" class="form-control form-control-sm field-priority">
                            <option value="high" @selected($l->priorite == 'high')>🔴 @lang('modules.tasks.high')</option>
                            <option value="medium" @selected($l->priorite == 'medium')>🟡 @lang('modules.tasks.medium')</option>
                            <option value="low" @selected($l->priorite == 'low')>🟢 @lang('modules.tasks.low')</option>
                        </select>
                    </td>

                    {{-- 6. Date début (Date picker) --}}
                    <td>
                        <input type="text" 
                               name="tasks[{{ $idx }}][start_date]" 
                               class="form-control form-control-sm field-start-date import-datepicker" 
                               value="{{ $l->debut_str }}" 
                               placeholder="{{ company()->date_format }}" 
                               required>
                    </td>

                    {{-- 7. Date fin (Date picker) --}}
                    <td>
                        <input type="text" 
                               name="tasks[{{ $idx }}][due_date]" 
                               class="form-control form-control-sm field-due-date import-datepicker" 
                               value="{{ $l->fin_str }}" 
                               placeholder="{{ company()->date_format }}">
                    </td>

                    {{-- 8. Description (Textarea auto-extensible pour lecture complète) --}}
                    <td>
                        <textarea name="tasks[{{ $idx }}][description]" 
                                  class="form-control form-control-sm field-description dck-auto-expand" 
                                  placeholder="@lang('Description...')" 
                                  rows="1" 
                                  title="{{ $l->description }}">{{ $l->description }}</textarea>
                    </td>

                    {{-- 9. Assigné à (Select collaborateur / par défaut connecté) --}}
                    <td>
                        <select name="tasks[{{ $idx }}][user_id]" class="form-control form-control-sm field-assignee">
                            <option value="{{ user()->id }}" @selected(empty($l->user_id) || $l->user_id == user()->id)>
                                {{ user()->name }} (@lang('Moi-même'))
                            </option>
                            @foreach ($collaborateurs as $collab)
                                @if ($collab->id !== user()->id)
                                    <option value="{{ $collab->id }}" @selected(isset($l->user_id) && $l->user_id == $collab->id)>
                                        {{ $collab->name }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </td>

                    {{-- 10. Actions (Supprimer de la liste temporaire) --}}
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-danger border-0 btn-delete-row mt-1" title="@lang('app.delete')" data-toggle="tooltip">
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
        <tr class="row-task" data-index="__INDEX__" id="row-__INDEX__">
            <td class="text-center font-weight-bold text-muted align-middle">
                <span class="row-num">__NUMERO__</span>
                <input type="hidden" name="tasks[__INDEX__][numero]" class="field-numero" value="__NUMERO__">
            </td>

            {{-- 1. Titre (Texte requis - auto-extensible) --}}
            <td>
                <textarea name="tasks[__INDEX__][heading]" 
                          class="form-control form-control-sm field-heading dck-auto-expand" 
                          placeholder="@lang('Titre de la tâche...')" 
                          rows="1" 
                          required></textarea>
            </td>

            {{-- 2. Projet (Select) --}}
            <td>
                <select name="tasks[__INDEX__][project_id]" class="form-control form-control-sm field-project">
                    <option value="">-- @lang('Aucun') --</option>
                    @foreach ($projets as $p)
                        <option value="{{ $p->id }}">{{ $p->project_name }}</option>
                    @endforeach
                </select>
            </td>

            {{-- 3. Catégorie / Domaine (Select) --}}
            <td>
                <select name="tasks[__INDEX__][category_id]" class="form-control form-control-sm field-category">
                    <option value="">-- @lang('Aucun') --</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->category_name }}</option>
                    @endforeach
                </select>
            </td>

            {{-- 4. Statut (Select) --}}
            <td>
                <select name="tasks[__INDEX__][board_column_id]" class="form-control form-control-sm field-status">
                    @foreach ($statuts as $st)
                        <option value="{{ $st->id }}" @selected($st->slug === 'incomplete' || $loop->first)>
                            {{ $st->column_name }}
                        </option>
                    @endforeach
                </select>
            </td>

            {{-- 5. Priorité (Select) --}}
            <td>
                <select name="tasks[__INDEX__][priority]" class="form-control form-control-sm field-priority">
                    <option value="high">🔴 @lang('modules.tasks.high')</option>
                    <option value="medium" selected>🟡 @lang('modules.tasks.medium')</option>
                    <option value="low">🟢 @lang('modules.tasks.low')</option>
                </select>
            </td>

            {{-- 6. Date début (Date picker) --}}
            <td>
                <input type="text" 
                       name="tasks[__INDEX__][start_date]" 
                       class="form-control form-control-sm field-start-date import-datepicker" 
                       value="{{ now()->format(company()->date_format) }}" 
                       placeholder="{{ company()->date_format }}" 
                       required>
            </td>

            {{-- 7. Date fin (Date picker) --}}
            <td>
                <input type="text" 
                       name="tasks[__INDEX__][due_date]" 
                       class="form-control form-control-sm field-due-date import-datepicker" 
                       value="" 
                       placeholder="{{ company()->date_format }}">
            </td>

            {{-- 8. Description (Textarea auto-extensible) --}}
            <td>
                <textarea name="tasks[__INDEX__][description]" 
                          class="form-control form-control-sm field-description dck-auto-expand" 
                          placeholder="@lang('Description...')" 
                          rows="1"></textarea>
            </td>

            {{-- 9. Assigné à (Select collaborateur / par défaut connecté) --}}
            <td>
                <select name="tasks[__INDEX__][user_id]" class="form-control form-control-sm field-assignee">
                    <option value="{{ user()->id }}" selected>
                        {{ user()->name }} (@lang('Moi-même'))
                    </option>
                    @foreach ($collaborateurs as $collab)
                        @if ($collab->id !== user()->id)
                            <option value="{{ $collab->id }}">
                                {{ $collab->name }}
                            </option>
                        @endif
                    @endforeach
                </select>
            </td>

            {{-- 10. Actions (Supprimer de la liste temporaire) --}}
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger border-0 btn-delete-row mt-1" title="@lang('app.delete')" data-toggle="tooltip">
                    <i class="fa fa-trash"></i>
                </button>
            </td>
        </tr>
    </template>

@endif
