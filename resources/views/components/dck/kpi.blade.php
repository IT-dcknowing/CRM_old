{{--
    Carte d'indicateur du nouveau front.

    Usage :
        <x-dck.kpi icone="bi-file-earmark-text" libelle="Contrats actifs"
                   :valeur="$n" sous="en cours de validite" ton="ok" />

    « ton » accepte : ok, warn, err, info. Sans ton, la carte reste neutre.
    Les valeurs proviennent des variables deja calculees par le controleur :
    ce composant n'execute aucune requete.
--}}
@props([
    'icone'   => 'bi-graph-up',
    'libelle' => '',
    'valeur'  => '0',
    'sous'    => null,
    'ton'     => null,
])

<div class="dck-kpi">
    <div class="dck-kpi__top">
        <span class="dck-kpi__ico @if ($ton) is-{{ $ton }} @endif">
            <i class="bi {{ $icone }}"></i>
        </span>
        <span class="dck-kpi__lab">{{ $libelle }}</span>
    </div>

    <div class="dck-kpi__val">{{ $valeur }}</div>

    @if ($sous)
        <div class="dck-kpi__sub">{{ $sous }}</div>
    @endif
</div>
