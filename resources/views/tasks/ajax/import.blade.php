{{--
    Importation massive de taches.

    Trois temps : telecharger le modele, deposer le fichier rempli,
    verifier l'apercu avant de confirmer. Rien n'est enregistre tant
    que la confirmation n'a pas ete donnee.

    L'ecran suit les composants du front — en-tete de page, cartes et
    boutons de l'application — plutot qu'une mise en page a lui.
--}}

<x-dck.page-head :titre="__('Importer des tâches')"
                 :description="__('Créez plusieurs tâches d\'un coup à partir d\'un fichier tableur.')">
    <x-slot name="actions">
        <x-forms.link-secondary :link="route('tasks.index')" icon="list">
            @lang('app.menu.tasks')
        </x-forms.link-secondary>
    </x-slot>
</x-dck.page-head>

<div class="dck-import">

    {{-- Etapes 1 et 2 cote a cote sur grand ecran, empilees sinon. --}}
    <div class="row">
    <div class="col-xl-7">

    {{-- ── 1. Le modele ──────────────────────────────────────────── --}}
    <div class="dck-card mb-3">
        <div class="dck-card__head d-flex">
            <h4 class="mb-0"><span class="dck-import__num">1</span> @lang('Téléchargez le modèle')</h4>
        </div>

        <div class="card-body">
            <p class="dck-import__intro">
                @lang("Le modèle contient les colonnes attendues et des menus déroulants bâtis sur vos propres projets, collaborateurs et domaines : vous choisissez au lieu de saisir. Une ligne d'exemple grisée montre le remplissage attendu ; supprimez-la avant d'importer.")
            </p>

            <a class="btn btn-primary rounded f-14 p-2" href="{{ route('tasks.import.modele') }}">
                <i class="fa fa-download mr-1"></i>
                @lang('Télécharger le modèle Excel')
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
                            'heading'     => __("Le titre de la tâche."),
                            'project'     => __("Un projet existant, à choisir dans la liste. Laissez vide si la tâche n'est rattachée à aucun projet, ou si vous indiquez un projet par défaut à l'étape 2."),
                            'start_date'  => __('Format :') . ' ' . company()->date_format,
                            'due_date'    => __('Même format. Doit être postérieure à la date de début.'),
                            'status'      => __('Un des statuts du tableau ; voir la liste ci-dessous.'),
                            'assigned'    => __("Le nom d'un collaborateur, à choisir dans la liste. Pour en confier à plusieurs, séparez les noms par une virgule."),
                            'category'    => __("Un domaine existant, à choisir dans la liste. L'ancien intitulé « Catégorie » reste accepté."),
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
                {{ \App\Models\TaskboardColumn::pluck('column_name')->implode(' · ') }}.
                <br>
                @lang("La casse et les accents n'ont pas d'importance : « en cours », « En cours » et « EN COURS » sont acceptés.")
                @lang('Sans statut, la tâche est créée en « Incomplète », comme à la création manuelle.')
            </p>
        </div>
    </div>

    </div>
    <div class="col-xl-5">

    {{-- ── 2. Le fichier ─────────────────────────────────────────── --}}
    <div class="dck-card mb-3">
        <div class="dck-card__head d-flex">
            <h4 class="mb-0"><span class="dck-import__num">2</span> @lang('Déposez votre fichier')</h4>
        </div>

        <div class="card-body">
            <form id="form-import-taches" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    {{-- Projet par defaut.

                         Un lot de taches concerne le plus souvent un
                         seul projet ; le choisir une fois ici evite de
                         recopier son nom sur chaque ligne du fichier.
                         Une ligne qui porte un projet garde le sien, de
                         sorte qu'un fichier melangeant plusieurs projets
                         reste possible. --}}
                    <div class="col-12">
                        <div class="form-group mb-3">
                            <label for="projet_defaut" class="f-14 text-dark-grey mb-2">
                                @lang('Projet par défaut')
                                <span class="f-12 text-lightest">— @lang('facultatif')</span>
                            </label>

                            <select name="projet_defaut" id="projet_defaut" class="form-control f-14">
                                <option value="">@lang('Aucun — le projet est lu dans le fichier')</option>
                                @foreach ($projets as $p)
                                    <option value="{{ $p->id }}">{{ $p->project_name }}</option>
                                @endforeach
                            </select>

                            <p class="dck-import__note">
                                @lang("Appliqué aux lignes dont la colonne « Projet » est vide.")
                            </p>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group mb-3">
                            <label for="import_file" class="f-14 text-dark-grey mb-2">
                                @lang('Fichier') <sup class="text-danger">*</sup>
                            </label>

                            <input type="file" name="import_file" id="import_file"
                                   class="form-control f-14" accept=".xlsx,.xls,.ods,.csv" required>

                            <p class="dck-import__note">
                                @lang('Formats acceptés : xlsx, xls, ods, csv.')
                                {{ __('Maximum :') }} {{ \App\Imports\TaskImport::MAXIMUM_LIGNES }} {{ __('lignes par import.') }}
                            </p>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary rounded f-14 p-2" id="btn-analyser">
                    <i class="fa fa-search mr-1"></i> @lang('Analyser le fichier')
                </button>
            </form>
        </div>
    </div>

    </div>
    </div>

    {{-- ── 3. L'apercu, rempli par la reponse du serveur ─────────── --}}
    <div class="dck-card mb-3 d-none" id="etape-apercu">
        <div class="dck-card__head d-flex">
            <h4 class="mb-0"><span class="dck-import__num">3</span> @lang('Vérifiez avant de confirmer')</h4>
        </div>

        <div class="card-body" id="zone-apercu"></div>
    </div>

</div>

<script>
(function () {
    'use strict';

    var form = document.getElementById('form-import-taches'),
        etape = document.getElementById('etape-apercu'),
        zone = document.getElementById('zone-apercu'),
        bouton = document.getElementById('btn-analyser');

    if (!form) { return; }

    function erreur(texte) {
        zone.innerHTML = '<div class="dck-import__msg is-err">' + texte + '</div>';
        etape.classList.remove('d-none');
        etape.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        var champ = document.getElementById('import_file');

        if (!champ.files.length) { return; }

        bouton.disabled = true;
        bouton.innerHTML = '{{ __("Analyse en cours…") }}';

        var donnees = new FormData(form);

        fetch('{{ route("tasks.import.analyse") }}', {
            method: 'POST',
            body: donnees,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function (r) {
            return r.json().then(function (corps) { return { code: r.status, corps: corps }; });
        })
        .then(function (r) {
            /* Un refus de validation — un PDF depose, par exemple —
               revient en 422, sans apercu. Le serveur dit alors
               precisement ce qui ne va pas : on affiche son message
               plutot que d'ouvrir une etape 3 vide. */
            if (r.code === 422) {
                var e = r.corps.errors || {};

                erreur(e.import_file ? e.import_file[0]
                    : (r.corps.message || '{{ __("Le fichier n’a pas pu être analysé.") }}'));

                return;
            }

            zone.innerHTML = r.corps.html || '';
            etape.classList.remove('d-none');
            etape.scrollIntoView({ behavior: 'smooth', block: 'start' });
        })
        .catch(function () {
            erreur('{{ __("Le fichier n’a pas pu être analysé.") }}');
        })
        .finally(function () {
            bouton.disabled = false;
            bouton.innerHTML = '<i class="fa fa-search mr-1"></i> {{ __("Analyser le fichier") }}';
        });
    });

    /* Le bouton de confirmation est injecte avec l'apercu : on ecoute
       donc au niveau du conteneur, qui lui existe des le depart. */
    zone.addEventListener('click', function (e) {
        var b = e.target.closest('#btn-confirmer-import');

        if (!b) { return; }

        b.disabled = true;
        b.innerHTML = '{{ __("Importation en cours…") }}';

        var d = new FormData();
        d.append('_token', '{{ csrf_token() }}');
        d.append('jeton', b.dataset.jeton);

        /* Le meme projet par defaut qu'a l'analyse : la confirmation
           relit le fichier, elle doit le relire dans les memes termes. */
        var projet = document.getElementById('projet_defaut');
        d.append('projet_defaut', projet ? projet.value : '');

        fetch('{{ route("tasks.import.process") }}', {
            method: 'POST',
            body: d,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function (r) { return r.json(); })
        .then(function (rep) {
            if (rep.status === 'success') {
                window.location.href = rep.redirectUrl || '{{ route("tasks.index") }}';
            }
            else {
                erreur(rep.message || '{{ __("L’importation a échoué.") }}');
            }
        })
        .catch(function () {
            b.disabled = false;
            b.innerHTML = '{{ __("Confirmer l’importation") }}';
        });
    });
})();
</script>
