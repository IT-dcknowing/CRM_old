{{--
    « Tableau de bord » du cabinet — conforme a PAGES.dashboard de la
    maquette dck-flow-task : quatre indicateurs, la charge de travail
    sur six mois, la repartition des taches par colonne, les diligences
    a surveiller, l'activite recente, les taches en cours et le taux de
    cloture.

    Les valeurs viennent de PilotageCabinet et de $projectActivities,
    deja prepare par OverviewDashboard. Aucune requete ici.
--}}

@php
    $k = $cabIndicateurs;
    $enRetard = \App\Models\Task::withoutGlobalScopes()
        ->where('company_id', company()->id)->whereNull('deleted_at')
        ->whereNotNull('due_date')->whereDate('due_date', '<', now())
        ->where('board_column_id', '<>', \App\Models\TaskboardColumn::completeColumn()->id)
        ->count();
@endphp

<section class="dck-pilotage">

    {{-- ── Alerte ─────────────────────────────────────────────── --}}
    @if ($enRetard > 0)
        <div class="dck-note is-warn">
            <span class="dck-note__i"><i class="bi bi-exclamation-triangle"></i></span>
            <div>
                <b>{{ $enRetard }} {{ trans_choice('tâche a dépassé son échéance|tâches ont dépassé leur échéance', $enRetard) }}</b>
                <p>Ouvrez le tableau des tâches pour les réaffecter.</p>
            </div>
            <a class="dck-note__act" href="{{ route('taskboards.index') }}">Voir les tâches</a>
        </div>
    @endif

    {{-- ── Indicateurs ────────────────────────────────────────── --}}
    <div class="dck-grid dck-g4">
        <a class="dck-kpi2" href="{{ route('tasks.index') }}">
            <div class="dck-kpi2__top">
                <span class="dck-kpi2__ico"><i class="bi bi-check2-square"></i></span>
                <span class="dck-kpi2__lab">@lang('Tâches en cours')</span>
            </div>
            <div class="dck-kpi2__val">{{ $k->tachesOuvertes }}</div>
            <div class="dck-kpi2__sub">
                <span class="dck-k__delta {{ $k->tachesRecentes > 0 ? 'is-up' : 'is-flat' }}">
                    {!! $k->tachesRecentes > 0 ? '&#9650;' : '&#9644;' !!} +{{ $k->tachesRecentes }}
                </span>
                sur l'ensemble des diligences
            </div>
        </a>

        <a class="dck-kpi2" href="{{ route('projects.index') }}">
            <div class="dck-kpi2__top">
                <span class="dck-kpi2__ico"><i class="bi bi-folder2-open"></i></span>
                <span class="dck-kpi2__lab">@lang('Diligences actives')</span>
            </div>
            <div class="dck-kpi2__val">{{ $k->diligences }}</div>
            <div class="dck-kpi2__sub">dont {{ $k->diligencesMois }} clôturées ce mois</div>
        </a>

        <a class="dck-kpi2" href="{{ route('contracts.index') }}">
            <div class="dck-kpi2__top">
                <span class="dck-kpi2__ico"><i class="bi bi-file-earmark-text"></i></span>
                <span class="dck-kpi2__lab">@lang('Contrats en vigueur')</span>
            </div>
            <div class="dck-kpi2__val">{{ $k->contrats }}</div>
            <div class="dck-kpi2__sub">{{ $k->contratsBientot }} arrivent à échéance</div>
        </a>

        <div class="dck-kpi2">
            <div class="dck-kpi2__top">
                <span class="dck-kpi2__ico"><i class="bi bi-people"></i></span>
                <span class="dck-kpi2__lab">@lang('Collaborateurs')</span>
            </div>
            <div class="dck-kpi2__val">{{ $k->collaborateurs }}</div>
            <div class="dck-kpi2__sub">actifs dans le cabinet</div>
        </div>
    </div>

    {{-- ── Charge de travail et repartition ───────────────────── --}}
    <div class="dck-grid dck-g23">

        <div class="dck-c">
            <div class="dck-c__head">
                <div>
                    <h3>@lang('Charge de travail')</h3>
                    <div class="dck-c__sub">Tâches créées et terminées sur six mois</div>
                </div>
            </div>
            <div class="dck-c__body">
                @php
                    $lab = $cabCharge['labels'];
                    $s1 = $cabCharge['creees'];
                    $s2 = $cabCharge['terminees'];
                    $haut = max(1, max(max($s1), max($s2)));
                    $L = 560; $H = 200;
                    $mg = ['t' => 12, 'r' => 8, 'b' => 24, 'l' => 30];
                    $pl = $L - $mg['l'] - $mg['r'];
                    $ph = $H - $mg['t'] - $mg['b'];
                    $pas = count($lab) > 1 ? $pl / (count($lab) - 1) : $pl;
                    $pt = function ($serie) use ($mg, $pas, $ph, $haut) {
                        $p = [];
                        foreach ($serie as $i => $v) {
                            $p[] = round($mg['l'] + $pas * $i, 1) . ',' . round($mg['t'] + $ph - ($ph * $v / $haut), 1);
                        }
                        return implode(' ', $p);
                    };
                @endphp
                <div class="dck-chart">
                    <svg class="dck-aires" viewBox="0 0 {{ $L }} {{ $H }}" role="img"
                         aria-label="Tâches créées et terminées sur six mois">
                        @for ($i = 0; $i <= 4; $i++)
                            @php $y = $mg['t'] + $ph - ($ph * $i / 4); @endphp
                            <line class="{{ $i === 0 ? 'dck-zero' : 'dck-grid' }}"
                                  x1="{{ $mg['l'] }}" y1="{{ round($y, 1) }}"
                                  x2="{{ $L - $mg['r'] }}" y2="{{ round($y, 1) }}"/>
                            <text class="dck-axe" x="{{ $mg['l'] - 6 }}" y="{{ round($y + 3, 1) }}"
                                  text-anchor="end">{{ round($haut * $i / 4) }}</text>
                        @endfor

                        <polyline class="dck-serie dck-serie--1" points="{{ $pt($s1) }}"/>
                        <polyline class="dck-serie dck-serie--2" points="{{ $pt($s2) }}"/>

                        @foreach ($lab as $i => $l)
                            <text class="dck-axe" x="{{ round($mg['l'] + $pas * $i, 1) }}"
                                  y="{{ $H - 6 }}" text-anchor="middle">{{ $l }}</text>
                        @endforeach
                    </svg>
                </div>
                <div class="dck-legende2">
                    <span><i style="background:#175CD3"></i>Tâches créées</span>
                    <span><i style="background:#0E7C5A"></i>Tâches terminées</span>
                </div>
            </div>
        </div>

        <div class="dck-c">
            <div class="dck-c__head">
                <div>
                    <h3>@lang('Répartition des tâches')</h3>
                    <div class="dck-c__sub">Par colonne du tableau</div>
                </div>
            </div>
            <div class="dck-c__body">
                @php $maxCol = max(1, $cabRepartition->max('n')); @endphp
                @foreach ($cabRepartition as $col)
                    <div class="dck-bl">
                        <span class="dck-bl__j">{{ $col->column_name }}</span>
                        <span class="dck-bar">
                            <i style="width:{{ round(100 * $col->n / $maxCol) }}%;background:{{ $col->label_color }}"></i>
                        </span>
                        <span class="dck-bl__v">{{ $col->n }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── Diligences a surveiller et activite ────────────────── --}}
    <div class="dck-grid dck-g23">

        <div class="dck-c">
            <div class="dck-c__head">
                <div>
                    <h3>@lang('Diligences à surveiller')</h3>
                    <div class="dck-c__sub">Échéance dans moins de 45 jours</div>
                </div>
                <div class="dck-c__right">
                    <a class="dck-lien" href="{{ route('projects.index') . '?projects=all' }}">Tout voir</a>
                </div>
            </div>
            <div class="dck-tbl">
                <table>
                    <thead>
                        <tr>
                            <th>@lang('Diligence')</th>
                            <th>@lang('Client')</th>
                            <th>@lang('Avancement')</th>
                            <th>@lang('Échéance')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($cabASurveiller as $d)
                            @php $depasse = $d->deadline && $d->deadline->isPast(); @endphp
                            <tr>
                                <td><a class="dck-ttl" href="{{ route('projects.show', $d->id) }}">{{ $d->project_name }}</a></td>
                                <td class="dck-cl">{{ optional($d->client)->name ?: '—' }}</td>
                                <td>
                                    <span class="dck-bar dck-bar--min">
                                        <i style="width:{{ (int) $d->completion_percent }}%"></i>
                                    </span>
                                    <span class="dck-av">{{ (int) $d->completion_percent }} %</span>
                                </td>
                                <td class="dck-date {{ $depasse ? 'is-late' : '' }}">
                                    {{ $d->deadline ? $d->deadline->translatedFormat('d/m/Y') : '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="dck-vide">@lang('Aucune échéance proche')</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="dck-c">
            <div class="dck-c__head">
                <div><h3>@lang('Activité récente')</h3></div>
            </div>
            <div class="dck-c__body dck-pile">
                @forelse (($projectActivities ?? collect())->take(6) as $a)
                    <div class="dck-an">
                        <span class="dck-an__p"></span>
                        <div>
                            <b>{!! __($a->activity) !!}</b>
                            <div class="dck-an__d">
                                {{ optional($a->project)->project_name }} ·
                                {{ $a->created_at->timezone(company()->timezone)->translatedFormat('d/m H:i') }}
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="dck-vide2">@lang('Aucune activité récente.')</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ── Taux de cloture et echeancier ──────────────────────── --}}
    <div class="dck-grid dck-g2">

        <div class="dck-c">
            <div class="dck-c__head">
                <div>
                    <h3>@lang('Taux de clôture')</h3>
                    <div class="dck-c__sub">Diligences terminées dans les délais</div>
                </div>
            </div>
            <div class="dck-c__body">
                <div class="dck-taux">
                    <b>{{ is_null($cabTauxCloture) ? '—' : $cabTauxCloture . ' %' }}</b>
                    <span class="dck-bar {{ ($cabTauxCloture ?? 0) >= 80 ? 'is-ok' : 'is-warn' }}">
                        <i style="width:{{ $cabTauxCloture ?? 0 }}%"></i>
                    </span>
                </div>
            </div>
        </div>

        <div class="dck-c">
            <div class="dck-c__head">
                <div>
                    <h3>@lang('Échéancier réglementaire')</h3>
                    <div class="dck-c__sub">30 prochains jours</div>
                </div>
            </div>
            <div>
                @forelse ($dashEcheances ?? [] as $e)
                    <div class="dck-dl {{ $e->urgent ? 'is-urgent' : '' }}">
                        <div class="dck-dl__when">
                            <b>{{ $e->date->format('d') }}</b>
                            <span>{{ $e->date->translatedFormat('M') }}</span>
                        </div>
                        <div class="dck-dl__what">
                            <b>{{ $e->label }}</b>
                            <span>{{ $e->detail }}</span>
                        </div>
                    </div>
                @empty
                    <div class="dck-dl">
                        <div class="dck-dl__what">
                            <b>@lang('Aucune échéance sur la période')</b>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</section>
