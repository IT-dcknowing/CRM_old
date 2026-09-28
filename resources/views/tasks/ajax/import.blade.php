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
        bouton.innerHTML = '<i class="fa fa-spinner fa-spin mr-1"></i> {{ __("Analyse en cours") }}';

        var donnees = new FormData(form);

        fetch('{{ route("tasks.import.analyse") }}', {
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
        })
        .catch(function (err) {
            afficherErreur('{{ __("Une erreur reseau est survenue. Verifiez votre connexion et reessayez.") }}');
        })
        .finally(function () {
            bouton.disabled = false;
            bouton.innerHTML = '<i class="fa fa-search mr-1"></i> {{ __("Analyser le fichier") }}';
        });
    });

    /* ── Etape 3 : confirmation de l'importation ───────────────── */

    /* Le bouton est injecte avec l'apercu : on ecoute via delegation
       sur zone, qui existe des le chargement de la page. */
    zone.addEventListener('click', function (e) {
        var b = e.target.closest('#btn-confirmer-import');

        if (!b) { return; }

        b.disabled = true;
        b.innerHTML = '<i class="fa fa-spinner fa-spin mr-1"></i> {{ __("Importation en cours") }}';

        var d = new FormData();
        d.append('_token', '{{ csrf_token() }}');
        d.append('jeton',  b.dataset.jeton);

        /* Transmet le meme projet par defaut : la confirmation relit
           le fichier conserve cote serveur, elle doit le relire dans
           les memes termes que l'analyse. */
        var projetSelect = document.getElementById('projet_defaut');
        d.append('projet_defaut', projetSelect ? projetSelect.value : '');

        fetch('{{ route("tasks.import.process") }}', {
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
                window.location.href = r.corps.redirectUrl || '{{ route("taskboards.index") }}';
                return;
            }

            /* Echec serveur : on reaffiche le message et on libere le bouton. */
            var msg = (r.corps && r.corps.message)
                ? r.corps.message
                : '{{ __("L\'importation a echoue. Veuillez reessayer.") }}';

            afficherErreur(msg);
            b.disabled = false;
            b.innerHTML = '<i class="fa fa-check mr-1"></i> {{ __("Confirmer l\'importation") }}';
        })
        .catch(function () {
            afficherErreur('{{ __("Une erreur reseau est survenue. Verifiez votre connexion et reessayez.") }}');
            b.disabled = false;
            b.innerHTML = '<i class="fa fa-check mr-1"></i> {{ __("Confirmer l\'importation") }}';
        });
    });

})();
</script>