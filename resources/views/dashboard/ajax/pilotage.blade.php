{{--
    Bloc de pilotage du tableau de bord, conforme a la maquette
    pilotage-dc-knowing : quatre indicateurs, deux graphiques, la liste
    des diligences a traiter et l'echeancier reglementaire.

    Toutes les valeurs sont preparees par App\Traits\OverviewDashboard :
    $diligenceKpis, $dashLivraisons, $dashRepartition, $dashATraiter et
    $dashEcheances. Cette vue n'execute aucune requete.

    Le graphique est un SVG ecrit a la main, comme dans la maquette :
    pas de librairie a charger, et le rendu suit les jetons de couleur
    de la feuille dck-flow.
--}}

<section class="dck-pilotage">

    {{-- ── Indicateurs ──────────────────────────────────────────── --}}
    <div class="dck-kpis">
        <a class="dck-k" href="{{ route('projects.index') . '?projects=all' }}">
            <div class="dck-k__lbl">@lang('Diligences suivies')</div>
            <div class="dck-k__val">{{ $diligenceKpis->suivies }}</div>
            <div class="dck-k__foot">
                <span class="dck-k__delta {{ $diligenceKpis->creees30j > 0 ? 'is-up' : 'is-flat' }}">
                    {!! $diligenceKpis->creees30j > 0 ? '&#9650;' : '&#9644;' !!}
                    {{ $diligenceKpis->creees30j > 0 ? '+' . $diligenceKpis->creees30j . ' sur 30 j' : 'stable sur 30 j' }}
                </span>
            </div>
        </a>

        <div class="dck-k">
            <div class="dck-k__lbl">@lang('En cours')</div>
            <div class="dck-k__val">{{ $diligenceKpis->enCours }}</div>
            <div class="dck-k__foot">
                <span class="dck-k__delta is-flat">{{ $diligenceKpis->pctEnCours }} % du portefeuille</span>
            </div>
        </div>

        <div class="dck-k">
            <div class="dck-k__lbl">@lang('En retard')</div>
            <div class="dck-k__val {{ $diligenceKpis->enRetard > 0 ? 'is-alerte' : '' }}">{{ $diligenceKpis->enRetard }}</div>
            <div class="dck-k__foot">
                <span class="dck-k__delta {{ $diligenceKpis->enRetard > 0 ? 'is-down' : 'is-up' }}">
                    {{ $diligenceKpis->pctRetard }} % du portefeuille
                </span>
            </div>
        </div>

        <div class="dck-k">
            <div class="dck-k__lbl">@lang('Livrées dans les délais')</div>
            <div class="dck-k__val">{{ is_null($diligenceKpis->pctDelais) ? '—' : $diligenceKpis->pctDelais . ' %' }}</div>
            <div class="dck-k__foot">
                <span class="dck-k__delta is-flat">{{ $diligenceKpis->termineesAvecEcheance }} terminées avec échéance</span>
            </div>
        </div>
    </div>

    {{-- ── Graphique et repartition ─────────────────────────────── --}}
    <div class="dck-grid2">

        <div class="dck-c">
            <div class="dck-c__head">
                <div>
                    <h3>@lang('Diligences livrées par mois')</h3>
                    <div class="dck-c__sub">Douze derniers mois — {{ $dashLivraisons['total'] }} au total</div>
                </div>
            </div>
            <div class="dck-c__body">
                @php
                    // Geometrie du graphique. Le maximum est arrondi au pas
                    // superieur pour que l'axe tombe sur des valeurs rondes.
                    $vals = $dashLivraisons['valeurs'];
                    $brut = max(1, max($vals));
                    $pas  = max(1, (int) ceil($brut / 4));
                    $haut = $pas * 4;

                    $L = 560; $H = 190;            // repere interne du SVG
                    $mg = ['t' => 10, 'r' => 6, 'b' => 22, 'l' => 26];
                    $pl = $L - $mg['l'] - $mg['r'];
                    $ph = $H - $mg['t'] - $mg['b'];
                    $pasX = $pl / max(1, count($vals));
                    $lb = min(26, $pasX * 0.56);
                @endphp
                <div class="dck-chart">
                    <svg class="dck-bars" viewBox="0 0 {{ $L }} {{ $H }}" role="img"
                         aria-label="Diligences terminées par mois sur douze mois, {{ $dashLivraisons['total'] }} au total">
                        {{-- Graduations --}}
                        @for ($i = 0; $i <= 4; $i++)
                            @php $y = $mg['t'] + $ph - ($ph * $i / 4); @endphp
                            <line class="{{ $i === 0 ? 'dck-zero' : 'dck-grid' }}"
                                  x1="{{ $mg['l'] }}" y1="{{ round($y, 1) }}"
                                  x2="{{ $L - $mg['r'] }}" y2="{{ round($y, 1) }}"/>
                            <text class="dck-axe" x="{{ $mg['l'] - 6 }}" y="{{ round($y + 3, 1) }}"
                                  text-anchor="end">{{ $pas * $i }}</text>
                        @endfor

                        {{-- Barres --}}
                        @foreach ($vals as $i => $v)
                            @php
                                $hb = $haut ? ($ph * $v / $haut) : 0;
                                $x  = $mg['l'] + $pasX * $i + ($pasX - $lb) / 2;
                                $y  = $mg['t'] + $ph - $hb;
                            @endphp
                            <g class="dck-bar">
                                <title>{{ $dashLivraisons['labels'][$i] }} : {{ $v }}</title>
                                @if ($v > 0)
                                    <rect class="dck-bar__v" x="{{ round($x, 1) }}" y="{{ round($y, 1) }}"
                                          width="{{ round($lb, 1) }}" height="{{ round($hb, 1) }}" rx="3"/>
                                @endif
                                <rect class="dck-bar__hit" x="{{ round($x, 1) }}" y="{{ $mg['t'] }}"
                                      width="{{ round($lb, 1) }}" height="{{ $ph }}"/>
                            </g>
                            <text class="dck-axe" x="{{ round($mg['l'] + $pasX * ($i + 0.5), 1) }}"
                                  y="{{ $H - 6 }}" text-anchor="middle">{{ $dashLivraisons['labels'][$i] }}</text>
                        @endforeach
                    </svg>
                </div>
            </div>
        </div>

        <div class="dck-c">
            <div class="dck-c__head">
                <div>
                    <h3>@lang('Répartition par statut')</h3>
                    <div class="dck-c__sub">{{ $diligenceKpis->suivies }} diligences</div>
                </div>
            </div>
            <div class="dck-c__body">
                <div class="dck-stack" role="img" aria-label="Répartition du portefeuille par statut">
                    @foreach ($dashRepartition as $e)
                        @if ($e['n'] > 0)
                            <span style="width:{{ $e['pct'] }}%;background:{{ $e['couleur'] }}"></span>
                        @endif
                    @endforeach
                </div>

                {{-- La legende porte le libelle et le nombre : la couleur
                     ne transporte jamais l'information a elle seule. --}}
                <div class="dck-legend">
                    @foreach ($dashRepartition as $e)
                        <div class="dck-legend__row">
                            <i class="dck-legend__key" style="background:{{ $e['couleur'] }}"></i>
                            <span class="dck-legend__nm">{{ $e['libelle'] }}</span>
                            <span class="dck-legend__n">{{ $e['n'] }}</span>
                            <span class="dck-legend__p">{{ number_format($e['pct'], 1, ',', ' ') }} %</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- ── Liste et echeancier ──────────────────────────────────── --}}
    <div class="dck-grid2">

        <div class="dck-c">
            <div class="dck-c__head">
                <div>
                    <h3>@lang('Diligences à traiter')</h3>
                    <div class="dck-c__sub">Triées par échéance</div>
                </div>
                <div class="dck-c__right">
                    <a class="dck-lien" href="{{ route('projects.index') . '?projects=all' }}">Tout voir</a>
                </div>
            </div>
            <div class="dck-tbl">
                <table>
                    <thead>
                        <tr>
                            <th>@lang('Référence')</th>
                            <th>@lang('Diligence')</th>
                            <th>@lang('Client')</th>
                            <th>@lang('Échéance')</th>
                            <th>@lang('Statut')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($dashATraiter as $p)
                            @php
                                $enRetard = $p->deadline && $p->deadline->isPast();
                            @endphp
                            <tr>
                                <td class="dck-ref">{{ $p->project_short_code ?: '—' }}</td>
                                <td>
                                    <a class="dck-ttl" href="{{ route('projects.show', $p->id) }}">{{ $p->project_name }}</a>
                                </td>
                                <td class="dck-cl">{{ optional($p->client)->name ?: '—' }}</td>
                                <td class="dck-date">
                                    {{ $p->deadline ? $p->deadline->translatedFormat('d/m/Y') : '—' }}
                                </td>
                                <td>
                                    <span class="dck-pill {{ $enRetard ? 'is-late' : 'is-doing' }}">
                                        {{ $enRetard ? __('En retard') : $p->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="dck-vide">@lang('Aucune diligence en attente')</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
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
                @forelse ($dashEcheances as $e)
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
                            <span>@lang('Les prochaines obligations arrivent plus tard.')</span>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</section>
