{{--
    « Mon tableau de bord » — conforme a l'ecran mon-espace de la
    maquette dck-flow-task : quatre indicateurs, les taches du moment,
    la semaine pointee, les diligences, les annonces et la presence.

    Les donnees viennent d'EmployeeDashboard, qui les preparait deja
    pour les widgets historiques — pendingTasks, inProcessTasks,
    dueTasks, notices, weekWiseTimelogs, currentClockIn — completees
    par EspaceCollaborateur pour l'avancement des diligences, le solde
    de conges et les taches terminees du mois. Aucune requete ici.
--}}

@php
    $enRetard = isset($dueTasks) ? $dueTasks->count() : 0;
    $termineeCol = \App\Models\TaskboardColumn::completeColumn();
@endphp

<div class="dck-espace">

    {{-- ── Alerte d'echeance depassee ─────────────────────────── --}}
    @if ($enRetard > 0)
        <div class="dck-note is-err">
            <span class="dck-note__i"><i class="bi bi-exclamation-triangle"></i></span>
            <div>
                <b>{{ trans_choice('Une de vos tâches a dépassé son échéance|:n de vos tâches ont dépassé leur échéance', $enRetard, ['n' => $enRetard]) }}</b>
                <p>Mettez-les à jour ou signalez un blocage à votre responsable.</p>
            </div>
            <a class="dck-note__act" href="{{ route('tasks.index') }}">Ouvrir</a>
        </div>
    @endif

    {{-- ── Indicateurs ────────────────────────────────────────── --}}
    <div class="dck-grid dck-g4">
        <a class="dck-kpi2" href="{{ route('tasks.index') }}">
            <div class="dck-kpi2__top">
                <span class="dck-kpi2__ico"><i class="bi bi-check2-square"></i></span>
                <span class="dck-kpi2__lab">@lang('Tâches en cours')</span>
            </div>
            <div class="dck-kpi2__val">{{ $inProcessTasks ?? 0 }}</div>
            <div class="dck-kpi2__sub">qui vous sont assignées</div>
        </a>

        <div class="dck-kpi2">
            <div class="dck-kpi2__top">
                <span class="dck-kpi2__ico"><i class="bi bi-check2-circle"></i></span>
                <span class="dck-kpi2__lab">@lang('Terminées ce mois')</span>
            </div>
            <div class="dck-kpi2__val">{{ $espaceTermineesMois ?? 0 }}</div>
            <div class="dck-kpi2__sub">depuis le {{ now()->startOfMonth()->translatedFormat('j F') }}</div>
        </div>

        <a class="dck-kpi2" href="{{ route('projects.index') }}">
            <div class="dck-kpi2__top">
                <span class="dck-kpi2__ico"><i class="bi bi-folder2-open"></i></span>
                <span class="dck-kpi2__lab">@lang('Mes diligences')</span>
            </div>
            <div class="dck-kpi2__val">{{ $totalProjects ?? 0 }}</div>
            <div class="dck-kpi2__sub">dont vous êtes membre</div>
        </a>

        <div class="dck-kpi2">
            <div class="dck-kpi2__top">
                <span class="dck-kpi2__ico"><i class="bi bi-calendar4-week"></i></span>
                <span class="dck-kpi2__lab">@lang('Congés restants')</span>
            </div>
            <div class="dck-kpi2__val">{{ is_null($espaceConges ?? null) ? '—' : $espaceConges }}</div>
            <div class="dck-kpi2__sub">jours sur l'exercice {{ now()->year }}</div>
        </div>
    </div>

    {{-- ── Mes taches et ma semaine ───────────────────────────── --}}
    <div class="dck-grid dck-g23">

        <div class="dck-c">
            <div class="dck-c__head">
                <div>
                    <h3>@lang('Mes tâches du moment')</h3>
                    <div class="dck-c__sub">Classées par échéance</div>
                </div>
                <div class="dck-c__right">
                    <a class="dck-lien" href="{{ route('tasks.index') }}">Tout voir</a>
                </div>
            </div>
            <div class="dck-c__body dck-pile">
                @forelse (($pendingTasks ?? collect())->sortBy('due_date')->take(6) as $t)
                    @php $tRetard = $t->due_date && $t->due_date->endOfDay()->isPast(); @endphp
                    <a class="dck-tc" href="{{ route('tasks.show', $t->id) }}">
                        <div class="dck-tc__top">
                            <span class="dck-tc__id">{{ $t->task_short_code ?: '—' }}</span>
                            <span class="dck-pill {{ $tRetard ? 'is-late' : 'is-doing' }}">
                                {{ $tRetard ? __('En retard') : optional($t->boardColumn)->column_name }}
                            </span>
                        </div>
                        <h5>{{ $t->heading }}</h5>
                        <div class="dck-tc__meta">
                            <i class="bi bi-folder2"></i>
                            <span>{{ optional($t->activeProject)->project_name ?: __('Sans diligence') }}</span>
                            <span class="dck-tc__ech {{ $tRetard ? 'is-late' : '' }}">
                                {{ $t->due_date ? $t->due_date->translatedFormat('d/m/Y') : '—' }}
                            </span>
                        </div>
                    </a>
                @empty
                    <p class="dck-vide2">@lang('Aucune tâche en cours. Bonne journée.')</p>
                @endforelse
            </div>
        </div>

        <div class="dck-c">
            <div class="dck-c__head">
                <div>
                    <h3>@lang('Ma semaine')</h3>
                    <div class="dck-c__sub">Heures pointées</div>
                </div>
            </div>
            <div class="dck-c__body">
                @php
                    $jours = collect($weekWiseTimelogs ?? [])->take(7);
                    $maxMin = max(1, $jours->max('total_minutes') ?: 1);
                    $totalMin = $jours->sum('total_minutes');
                @endphp
                @forelse ($jours as $j)
                    @php
                        $min = (int) ($j->total_minutes ?? 0);
                        $pct = round(100 * $min / $maxMin);
                    @endphp
                    <div class="dck-bl">
                        <span class="dck-bl__j">{{ \Carbon\Carbon::parse($j->date ?? now())->translatedFormat('D j') }}</span>
                        <span class="dck-bar"><i style="width:{{ $pct }}%"></i></span>
                        <span class="dck-bl__v">{{ intdiv($min, 60) }} h {{ str_pad($min % 60, 2, '0', STR_PAD_LEFT) }}</span>
                    </div>
                @empty
                    <p class="dck-vide2">@lang('Aucune heure pointée cette semaine.')</p>
                @endforelse

                <div class="dck-total">
                    <span>@lang('Total de la semaine')</span>
                    <b>{{ intdiv($totalMin, 60) }} h {{ str_pad($totalMin % 60, 2, '0', STR_PAD_LEFT) }}</b>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Diligences, annonces et presence ───────────────────── --}}
    <div class="dck-grid dck-g3">

        <div class="dck-c">
            <div class="dck-c__head">
                <div><h3>@lang('Mes diligences')</h3></div>
                <div class="dck-c__right">
                    <a class="dck-lien" href="{{ route('projects.index') }}">Ouvrir</a>
                </div>
            </div>
            <div class="dck-c__body dck-pile-l">
                @forelse ($espaceDiligences ?? [] as $d)
                    @php $av = (int) $d->completion_percent; @endphp
                    <div>
                        <div class="dck-dl2">
                            <a class="dck-lien2" href="{{ route('projects.show', $d->id) }}">{{ $d->project_name }}</a>
                            <b>{{ $av }} %</b>
                        </div>
                        <span class="dck-bar {{ $av >= 80 ? 'is-ok' : ($av >= 40 ? '' : 'is-warn') }}">
                            <i style="width:{{ $av }}%"></i>
                        </span>
                        <div class="dck-dl2__sub">{{ $d->taches_faites }} / {{ $d->taches_total }} tâches terminées</div>
                    </div>
                @empty
                    <p class="dck-vide2">@lang('Vous n\'êtes membre d\'aucune diligence.')</p>
                @endforelse
            </div>
        </div>

        <div class="dck-c">
            <div class="dck-c__head">
                <div><h3>@lang('Annonces')</h3></div>
            </div>
            <div class="dck-c__body dck-pile">
                @forelse (($notices ?? collect())->take(3) as $n)
                    <div class="dck-an">
                        <span class="dck-an__p"></span>
                        <div>
                            <b>{{ $n->heading }}</b>
                            <div class="dck-an__d">{{ $n->created_at->translatedFormat('d/m/Y') }}</div>
                        </div>
                    </div>
                @empty
                    <p class="dck-vide2">@lang('Aucune annonce.')</p>
                @endforelse
            </div>
        </div>

        <div class="dck-c">
            <div class="dck-c__head">
                <div>
                    <h3>@lang('Ma présence')</h3>
                    <div class="dck-c__sub">{{ now()->translatedFormat('d/m/Y') }}</div>
                </div>
            </div>
            <div class="dck-c__body dck-pres">
                <div>
                    <div class="dck-pres__l">@lang('Arrivée')</div>
                    <b>{{ isset($currentClockIn) && $currentClockIn ? $currentClockIn->clock_in_time->timezone(company()->timezone)->format('H:i') : '—' }}</b>
                </div>
                <div>
                    <div class="dck-pres__l">@lang('Temps effectué')</div>
                    <b>{{ $totalTimeFormatted ?? '—' }}</b>
                </div>
            </div>
        </div>
    </div>

</div>
