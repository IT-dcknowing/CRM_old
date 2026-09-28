{{--
    Importation massive de taches.

    Trois temps : telecharger le modele, deposer le fichier rempli,
    verifier l'apercu avant de confirmer. Rien n'est enregistre tant
    que la confirmation n'a pas ete donnee.

    L'ecran suit les composants du front — en-tete de page, cartes et
    boutons de l'application — plutot qu'une mise en page a lui.
--}}


<x-dck.page-head :titre="__('Importer des taches')"
                 :description="__('Creez plusieurs taches d\'un coup a partir d\'un fichier tableur.')">
    <x-slot name="actions">
        <x-forms.link-secondary :link="route('tasks.index')" icon="list">
            @lang('app.menu.tasks')
        </x-forms.link-secondary>
    </x-slot>
</x-dck.page-head>

<div class="dck-import">

    {{-- Ligne superieure : deux colonnes cote a cote --}}
    <div class="dck-import__top">

        {{-- 1. Le modele --}}
        <div class="dck-card dck-import__col dck-import__col--modele">
            <div class="dck-card__head d-flex">
                <h4 class="mb-0"><span class="dck-import__num">1</span> @lang('Telechargez le modele')</h4>
            </div>

            <div class="card-body">
                <p class="dck-import__intro">
                    @lang("Le modele contient les colonnes attendues et des menus deroulants batis sur vos propres projets, collaborateurs et domaines.")
                </p>

                <a class="btn btn-primary rounded f-14 p-2" href="{{ route('tasks.import.modele') }}">
                    <i class="fa fa-download mr-1"></i>
                    @lang('Telecharger le modele Excel')
                </a>

                <div class="table-responsive mt-4">
                    <table class="table dck-import__cols">
                        <thead>
                            <tr>
                                <th>@lang('Colonne')</th>
                                <th>@lang('Obligatoire')</th>
                                <th>@lang('Ce qui est attendu')</th>
                            </tr>
                        </thead>
                        <tbody>
                        @php
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
                        @endphp
                        @foreach ($colonnes as $c)
                            <tr>
                                <td><strong>{{ $c['nom'] }}</strong></td>
                                <td>
                                    @if ($c['requis'])
                                        <span class="dck-import__p is-req">@lang('oui')</span>
                                    @else
                                        <span class="dck-import__p">@lang('non')</span>
                                    @endif
                                </td>
                                <td>{{ $aide[$c['cle']] ?? '' }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <p class="dck-import__note">
                    <strong>@lang('Statuts reconnus') :</strong>
                    {{ \App\Models\TaskboardColumn::pluck('column_name')->implode(' - ') }}.
                    <br>
                    @lang("La casse et les accents n'ont pas d'importance.")
                    @lang("Sans statut, la tache est creee en Incomplete.")
                </p>

                <p class="dck-import__note">
                    <i class="fa fa-user-check mr-1"></i>
                    <strong>@lang('Assignation automatique') :</strong>
                    @lang("Toutes les taches importees vous sont automatiquement assignees.")
                </p>
            </div>
        </div>

        {{-- 2. Le fichier --}}
        <div class="dck-card dck-import__col dck-import__col--fichier">
            <div class="dck-card__head d-flex">
                <h4 class="mb-0"><span class="dck-import__num">2</span> @lang('Deposez votre fichier')</h4>
            </div>

            <div class="card-body">
                <form id="form-import-taches" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group mb-3">
                        <label for="projet_defaut" class="f-14 text-dark-grey mb-2">
                            @lang('Projet par defaut')
                            <span class="f-12 text-lightest">@lang('facultatif')</span>
                        </label>

                        <select name="projet_defaut" id="projet_defaut" class="form-control f-14">
                            <option value="">@lang('Aucun — le projet est lu dans le fichier')</option>
                            @foreach ($projets as $p)
                                <option value="{{ $p->id }}">{{ $p->project_name }}</option>
                            @endforeach
                        </select>

                        <p class="dck-import__note">
                            @lang("Applique aux lignes dont la colonne Projet est vide.")
                        </p>
                    </div>

                    <div class="form-group mb-3">
                        <label for="import_file" class="f-14 text-dark-grey mb-2">
                            @lang('Fichier') <sup class="text-danger">*</sup>
                        </label>

                        <div class="dck-import__dropzone" id="dck-dropzone">
                            <input type="file" name="import_file" id="import_file"
                                   class="dck-import__dropzone-input" accept=".xlsx,.xls,.ods,.csv" required>
                            <div class="dck-import__dropzone-label">
                                <i class="fa fa-cloud-upload-alt dck-import__dropzone-icon"></i>
                                <span class="dck-import__dropzone-text" id="dck-dropzone-text">
                                    @lang('Glissez votre fichier ici ou cliquez pour choisir')
                                </span>
                                <span class="dck-import__note mt-1 d-block">
                                    @lang('Formats acceptes : xlsx, xls, ods, csv.')
                                    {{ __('Maximum :') }} {{ \App\Imports\TaskImport::MAXIMUM_LIGNES }} {{ __('lignes par import.') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary rounded f-14 p-2 w-100" id="btn-analyser">
                        <i class="fa fa-search mr-1"></i> @lang('Analyser le fichier')
                    </button>
                </form>
            </div>
        </div>

    </div>

    {{-- 3. L'apercu, rempli par la reponse du serveur --}}
    <div class="dck-card mb-3 d-none" id="etape-apercu">
        <div class="dck-card__head d-flex">
            <h4 class="mb-0"><span class="dck-import__num">3</span> @lang('Verifiez avant de confirmer')</h4>
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
                : '{{ __("Glissez votre fichier ici ou cliquez pour choisir") }}';
            dropzone.classList.toggle('has-file', !!champFichier.files.length);
        });
    }

    /* Affiche un message d'erreur dans la zone apercu et fait defiler jusqu'a lui. */
    function afficherErreur(texte) {
        zone.innerHTML = '<div class="dck-import__msg is-err"><strong>{{ __("Erreur") }}</strong><p>' + texte + '</p></div>';
        etape.classList.remove('d-none');
        etape.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    /* Initialise les datepickers dans la zone de prévisualisation */
    function initDatepickers() {
        if (typeof datepicker === 'function') {
            zone.querySelectorAll('.import-datepicker').forEach(function (el) {
                datepicker(el, {
                    position: 'bl',
                    ...(typeof datepickerConfig !== 'undefined' ? datepickerConfig : {}),
                    onSelect: function (instance, date) {
                        verifierValidationLignes();
                    }
                });
            });
        }
    }

    /* Recalcule et met à jour les indicateurs de validation */
    function verifierValidationLignes() {
        var rows = zone.querySelectorAll('.row-task');
        var valides = 0;
        var erreurs = 0;

        rows.forEach(function (row) {
            var headingInput = row.querySelector('.field-heading');
            var startDateInput = row.querySelector('.field-start-date');
            var isRowValid = true;

            if (!headingInput || !headingInput.value.trim()) {
                isRowValid = false;
                if (headingInput) { headingInput.classList.add('is-invalid'); }
            } else {
                if (headingInput) { headingInput.classList.remove('is-invalid'); }
            }

            if (!startDateInput || !startDateInput.value.trim()) {
                isRowValid = false;
                if (startDateInput) { startDateInput.classList.add('is-invalid'); }
            } else {
                if (startDateInput) { startDateInput.classList.remove('is-invalid'); }
            }

            if (isRowValid) {
                valides++;
                row.classList.remove('table-warning', 'table-danger');
            } else {
                erreurs++;
                row.classList.add('table-warning');
            }
        });

        var countValidesEl = document.getElementById('count-valides');
        var countErreursEl = document.getElementById('count-erreurs');
        var badgeErreursEl = document.getElementById('badge-erreurs');

        if (countValidesEl) { countValidesEl.textContent = valides; }
        if (countErreursEl) { countErreursEl.textContent = erreurs; }
        if (badgeErreursEl) {
            badgeErreursEl.className = 'badge ' + (erreurs > 0 ? 'badge-danger' : 'badge-secondary') + ' px-2 py-1';
        }

        var btnImport = document.getElementById('btn-confirmer-import');
        if (btnImport) {
            btnImport.disabled = (valides === 0);
        }
    }

    /* Parse la reponse JSON de maniere securisee */
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
        bouton.innerHTML = '<i class="fa fa-spinner fa-spin mr-1"></i> {{ __("Analyse en cours") }}';

        var donnees = new FormData(form);

        fetch('{{ route("tasks.import.analyse", [], false) }}', {
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
                    : (r.corps.message || '{{ __("Le fichier n\'a pas pu etre analyse.") }}'));
                return;
            }

            if (!r.corps.html) {
                afficherErreur(r.corps.message || '{{ __("Le fichier n\'a pas pu etre analyse.") }}');
                return;
            }

            zone.innerHTML = r.corps.html;
            etape.classList.remove('d-none');
            etape.scrollIntoView({ behavior: 'smooth', block: 'start' });

            try {
                initDatepickers();
                verifierValidationLignes();
            } catch (e) {
                console.warn('Initialisation partielle du DataTable:', e);
            }
        })
        .catch(function (err) {
            console.error('Erreur import analyse:', err);
            afficherErreur('{{ __("Une erreur est survenue lors de l\'analyse du fichier :") }} ' + (err.message || '{{ __("Vérifiez votre connexion et réessayez.") }}'));
        })
        .finally(function () {
            bouton.disabled = false;
            bouton.innerHTML = '<i class="fa fa-search mr-1"></i> {{ __("Analyser le fichier") }}';
        });
    });

    /* ── Écouteurs interactifs dans le DataTable ─────────────────── */

    // 1. Validation en direct lors de la saisie
    zone.addEventListener('input', function (e) {
        if (e.target.classList.contains('field-heading') || e.target.classList.contains('field-start-date')) {
            verifierValidationLignes();
        }
    });

    // 2. Suppression de ligne
    zone.addEventListener('click', function (e) {
        var btnDelete = e.target.closest('.btn-delete-row');
        if (btnDelete) {
            var tr = btnDelete.closest('tr');
            if (tr) {
                tr.remove();
                verifierValidationLignes();
            }
            return;
        }

        // 3. Annulation de l'import
        var btnAnnuler = e.target.closest('#btn-annuler-import');
        if (btnAnnuler) {
            zone.innerHTML = '';
            etape.classList.add('d-none');
            if (champFichier) { champFichier.value = ''; }
            if (labelDropzone) { labelDropzone.textContent = '{{ __("Glissez votre fichier ici ou cliquez pour choisir") }}'; }
            if (dropzone) { dropzone.classList.remove('has-file'); }
            window.scrollTo({ top: 0, behavior: 'smooth' });
            return;
        }
    });

    /* ── Etape 3 : confirmation de l'importation définitive ──────── */

    zone.addEventListener('click', function (e) {
        var b = e.target.closest('#btn-confirmer-import');

        if (!b) { return; }

        // Récupération de toutes les lignes du DataTable
        var rows = zone.querySelectorAll('.row-task');
        if (rows.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: '{{ __("Aucune tâche") }}',
                text: '{{ __("Il n'y a aucune tâche à importer.") }}',
                showConfirmButton: true
            });
            return;
        }

        var tasksData = [];
        var hasError = false;

        rows.forEach(function (row, idx) {
            var heading = (row.querySelector('.field-heading') ? row.querySelector('.field-heading').value.trim() : '');
            var projectId = (row.querySelector('.field-project') ? row.querySelector('.field-project').value : '');
            var categoryId = (row.querySelector('.field-category') ? row.querySelector('.field-category').value : '');
            var statusId = (row.querySelector('.field-status') ? row.querySelector('.field-status').value : '');
            var priority = (row.querySelector('.field-priority') ? row.querySelector('.field-priority').value : 'medium');
            var startDate = (row.querySelector('.field-start-date') ? row.querySelector('.field-start-date').value.trim() : '');
            var dueDate = (row.querySelector('.field-due-date') ? row.querySelector('.field-due-date').value.trim() : '');
            var description = (row.querySelector('.field-description') ? row.querySelector('.field-description').value.trim() : '');

            if (!heading || !startDate) {
                hasError = true;
            }

            tasksData.push({
                numero: idx + 1,
                heading: heading,
                project_id: projectId,
                category_id: categoryId,
                board_column_id: statusId,
                priority: priority,
                start_date: startDate,
                due_date: dueDate,
                description: description
            });
        });

        if (hasError) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: '{{ __("Données incomplètes") }}',
                    text: '{{ __("Certaines tâches ont des champs obligatoires manquants (Titre ou Date de début). Veuillez les corriger avant d\'importer.") }}',
                    showConfirmButton: true
                });
            } else {
                alert('{{ __("Certaines tâches ont des champs obligatoires manquants (Titre ou Date de début). Veuillez les corriger avant d\'importer.") }}');
            }
            return;
        }

        b.disabled = true;
        b.innerHTML = '<i class="fa fa-spinner fa-spin mr-1"></i> {{ __("Importation en cours") }}';

        fetch('{{ route("tasks.import.process", [], false) }}', {
            method : 'POST',
            body   : JSON.stringify({
                _token: '{{ csrf_token() }}',
                jeton: b.dataset.jeton,
                tasks: tasksData
            }),
            headers: {
                'Content-Type'    : 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept'          : 'application/json'
            }
        })
        .then(parseJson)
        .then(function (r) {
            if (r.corps && r.corps.status === 'success') {
                /* Notification de succès et redirection */
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: '{{ __("Importation réussie") }}',
                        text: r.corps.message || '{{ __("Les tâches ont été importées avec succès.") }}',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(function () {
                        window.location.href = r.corps.redirectUrl || '{{ route("taskboards.index") }}';
                    });
                } else {
                    window.location.href = r.corps.redirectUrl || '{{ route("taskboards.index") }}';
                }
                return;
            }

            /* Échec serveur */
            var msg = (r.corps && r.corps.message)
                ? r.corps.message
                : '{{ __("L\'importation a échoué. Veuillez vérifier les données saisies.") }}';

            afficherErreur(msg);
            b.disabled = false;
            b.innerHTML = '<i class="fa fa-check-circle mr-1"></i> {{ __("Importer les tâches") }}';
        })
        .catch(function (err) {
            console.error('Erreur import:', err);
            afficherErreur('{{ __("Une erreur est survenue lors de la communication avec le serveur :") }} ' + (err.message || ''));
            b.disabled = false;
            b.innerHTML = '<i class="fa fa-check-circle mr-1"></i> {{ __("Importer les tâches") }}';
        });
    });

})();
</script>