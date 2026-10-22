<?php

namespace App\Traits;

use App\Models\TaskboardColumn;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Rapport hebdomadaire des taches.
 *
 * Regle de semaine, unique et appliquee partout : du lundi 00:00 au
 * dimanche 23:59:59, dans le fuseau de la societe.
 *
 * Perimetre d'une tache : elle appartient a la semaine si l'un de ces
 * faits y tombe — elle a ete achevee, son echeance arrivait, ou elle a
 * change de statut. Se limiter aux taches achevees masquerait le travail
 * en cours ; se limiter aux echeances masquerait ce qui a ete livre en
 * avance. Les trois ensemble donnent l'activite reelle de la semaine.
 *
 * Le suivi du temps existe dans l'application (project_time_logs) mais
 * n'est pas utilise par le cabinet : sept saisies en tout, toutes de
 * 2024. La mesure de reference est donc le delai de traitement, en
 * jours entre le debut et l'achevement. Le temps pointe reste lu et
 * affiche quand il existe.
 */
trait RapportHebdomadaire
{

    /**
     * Bornes de la semaine demandee.
     *
     * @param string|null $semaine au format ISO « 2026-W36 », ou null
     *                             pour la semaine courante
     */
    protected function bornesSemaine(?string $semaine = null): array
    {
        $fuseau = company()->timezone;

        if ($semaine && preg_match('/^(\d{4})-W(\d{1,2})$/', $semaine, $m)) {
            $debut = Carbon::now($fuseau)
                ->setISODate((int) $m[1], (int) $m[2])
                ->startOfWeek(Carbon::MONDAY);
        }
        else {
            $debut = Carbon::now($fuseau)->startOfWeek(Carbon::MONDAY);
        }

        return [$debut->copy()->startOfDay(), $debut->copy()->endOfWeek(Carbon::SUNDAY)];
    }

    /**
     * Identifiants des taches appartenant a la semaine.
     *
     * Une seule requete, qui ne ramene que des identifiants : le detail
     * est charge ensuite pour ces seules taches, jamais pour la base
     * entiere.
     */
    private function tachesDeLaSemaine(Carbon $debut, Carbon $fin, ?int $limiteA = null)
    {
        /* L'historique est lu a part, puis injecte comme simple liste
           d'identifiants.

           Une sous-requete correlee « EXISTS » sur task_history serait
           plus concise, mais cette table ne porte aucun index hormis sa
           clef primaire : EXPLAIN montre alors un parcours complet de
           task_history repete pour chaque tache examinee. En deux
           lectures independantes, chaque table n'est parcourue qu'une
           fois. */
        $bouges = DB::table('task_history')
            ->whereBetween('created_at', [$debut, $fin])
            ->distinct()
            ->pluck('task_id')
            ->filter()
            ->all();

        $requete = DB::table('tasks')
            ->whereNull('tasks.deleted_at')
            ->where('tasks.company_id', company()->id)
            ->where(function ($q) use ($debut, $fin, $bouges) {
                $q->whereBetween('tasks.completed_on', [$debut, $fin])
                  ->orWhereBetween('tasks.due_date', [$debut, $fin])
                  ->orWhereBetween('tasks.start_date', [$debut, $fin])
                  ->orWhereBetween('tasks.updated_at', [$debut, $fin]);

                if ($bouges) {
                    $q->orWhereIn('tasks.id', $bouges);
                }
            });

        /* Restriction a un collaborateur : le filtre porte sur la table
           d'affectation, donc une tache partagee reste visible pour
           chacune des personnes qui y travaillent. */
        if ($limiteA !== null) {
            $requete->whereExists(function ($q) use ($limiteA) {
                $q->select(DB::raw(1))
                  ->from('task_users')
                  ->whereColumn('task_users.task_id', 'tasks.id')
                  ->where('task_users.user_id', $limiteA);
            });
        }

        return $requete->pluck('tasks.id');
    }

    /**
     * Le rapport complet : liste, synthese et activite par collaborateur.
     *
     * @param string|null $semaine semaine ISO, ou null pour la courante
     * @param int|null    $limiteA identifiant du collaborateur auquel
     *                             restreindre le rapport ; null pour
     *                             tout le cabinet
     */
    protected function rapportHebdomadaire(?string $semaine = null, ?int $limiteA = null): object
    {
        [$debut, $fin] = $this->bornesSemaine($semaine);

        $ids = $this->tachesDeLaSemaine($debut, $fin, $limiteA);

        $vide = (object) [
            'debut'                   => $debut,
            'fin'                     => $fin,
            'limiteA'                 => $limiteA,
            'semaineIso'              => $debut->format('o-\WW'),
            'taches'                  => collect(),
            'collaborateurs'          => collect(),
            'collaborateursStandards' => collect(),
            'stagiaires'              => collect(),
            'services'                => collect(),
            'clients'                 => collect(),
            'echeancesSuivantes'      => collect(),
            'synthese'                => (object) [
                'total' => 0, 'termines' => 0, 'enCours' => 0, 'aFaire' => 0,
                'incompletes' => 0, 'attente' => 0, 'retards' => 0,
                'delaiMoyen' => null, 'delaisJuges' => 0,
                'pointeHeures' => 0, 'pointeTaches' => 0, 'gens' => 0,
            ],
        ];

        if ($ids->isEmpty()) {
            return $vide;
        }

        /* Liste : jointures a gauche, car une tache peut n'avoir ni
           projet ni client sans devoir disparaitre du rapport. */
        $taches = DB::table('tasks')
            ->leftJoin('taskboard_columns', 'taskboard_columns.id', '=', 'tasks.board_column_id')
            ->leftJoin('projects', 'projects.id', '=', 'tasks.project_id')
            ->leftJoin('users AS cli', 'cli.id', '=', 'projects.client_id')
            ->whereIn('tasks.id', $ids)
            ->select(
                'tasks.id', 'tasks.task_short_code', 'tasks.heading', 'tasks.priority',
                'tasks.start_date', 'tasks.due_date', 'tasks.completed_on', 'tasks.updated_at',
                'tasks.estimate_hours', 'tasks.estimate_minutes',
                'taskboard_columns.column_name', 'taskboard_columns.slug', 'taskboard_columns.label_color',
                'projects.id AS projet_id', 'projects.project_name', 'cli.name AS client'
            )
            ->orderByDesc('tasks.updated_at')
            ->get();

        /* Collaborateurs de toutes les taches en une requete : sans cela,
           une lecture par ligne produirait autant de requetes que de
           taches affichees. */
        $affectations = DB::table('task_users')
            ->join('users', 'users.id', '=', 'task_users.user_id')
            ->whereIn('task_users.task_id', $ids)
            ->select('task_users.task_id', 'users.id', 'users.name')
            ->get()
            ->groupBy('task_id');

        /* Temps pointe, agrege en base plutot qu'en PHP. */
        $pointes = DB::table('project_time_logs')
            ->whereIn('task_id', $ids)
            ->selectRaw('task_id, SUM(total_minutes) AS minutes')
            ->groupBy('task_id')
            ->pluck('minutes', 'task_id');

        $aujourdhui = Carbon::now(company()->timezone);

        $lignes = $taches->map(function ($t) use ($affectations, $pointes, $aujourdhui) {
            $gens = ($affectations[$t->id] ?? collect())
                ->map(fn($u) => (object) ['id' => $u->id, 'nom' => $u->name])
                ->values();

            $delai = null;

            if ($t->completed_on && $t->start_date) {
                $delai = Carbon::parse($t->start_date)
                    ->startOfDay()
                    ->diffInDays(Carbon::parse($t->completed_on)->startOfDay());
            }

            return (object) [
                'id'        => $t->id,
                'reference' => $t->task_short_code,
                'titre'     => $t->heading,
                'client'    => $t->client,
                'projet'    => $t->project_name,
                'projetId'  => $t->projet_id,
                'debut'     => $t->start_date ? Carbon::parse($t->start_date) : null,
                'echeance'  => $t->due_date ? Carbon::parse($t->due_date) : null,
                'faitLe'    => $t->completed_on ? Carbon::parse($t->completed_on) : null,
                'statut'    => $t->column_name,
                'slug'      => $t->slug,
                'couleur'   => $t->label_color,
                'priorite'  => $t->priority,
                'prevu'     => $t->estimate_hours || $t->estimate_minutes
                    ? trim(($t->estimate_hours ? $t->estimate_hours . ' h ' : '')
                         . ($t->estimate_minutes ? $t->estimate_minutes . ' min' : ''))
                    : null,
                'pointe'    => isset($pointes[$t->id]) ? round($pointes[$t->id] / 60, 1) : null,
                'delai'     => $delai,
                'gens'      => $gens,
                'maj'       => Carbon::parse($t->updated_at),
                /* « En retard » n'est pas un statut du tableau : c'est un
                   indicateur pour une tache non achevee dont l'echeance est depassee. */
                'retard'    => $t->slug !== 'completed'
                               && $t->due_date
                               && Carbon::parse($t->due_date)->endOfDay()->lt($aujourdhui),
            ];
        });

        $collaborateurs = $this->activiteParCollaborateur($lignes, $limiteA);
        [$collaborateursStandards, $stagiaires] = $this->separationStagiaires($collaborateurs);

        return (object) [
            'debut'                   => $debut,
            'fin'                     => $fin,
            'limiteA'                 => $limiteA,
            'semaineIso'              => $debut->format('o-\WW'),
            'taches'                  => $lignes,
            'synthese'                => $this->syntheseHebdomadaire($lignes),
            'collaborateurs'          => $collaborateurs,
            'collaborateursStandards' => $collaborateursStandards,
            'stagiaires'              => $stagiaires,
            'services'                => $this->activiteParService($lignes),
            'clients'                 => $this->activiteParClient($lignes),
            'echeancesSuivantes'      => $this->echeancesSemaineSuivante($fin, $limiteA),
        ];
    }

    private function syntheseHebdomadaire($lignes): object
    {
        $delais  = $lignes->pluck('delai')->filter(fn($x) => $x !== null);
        $pointes = $lignes->pluck('pointe')->filter(fn($x) => $x !== null);

        $gens = $lignes->flatMap(fn($t) => $t->gens->pluck('id'))->unique();

        $termines    = $lignes->filter(fn($t) => in_array($t->slug, ['completed', 'terminee', 'terminée']) || mb_strtolower($t->statut ?? '') === 'terminée')->count();
        $enCours     = $lignes->filter(fn($t) => in_array($t->slug, ['doing', 'in_progress', 'en_cours']) || mb_strtolower($t->statut ?? '') === 'en cours')->count();
        $aFaire      = $lignes->filter(fn($t) => in_array($t->slug, ['to_do', 'a_faire', 'a-faire', 'todo']) || mb_strtolower($t->statut ?? '') === 'à faire')->count();
        $incompletes = $lignes->filter(fn($t) => in_array($t->slug, ['incomplete', 'incomplète']) || mb_strtolower($t->statut ?? '') === 'incomplète')->count();
        $attente     = $lignes->filter(fn($t) => in_array($t->slug, ['waiting_approval', 'en_attente']) || str_contains(mb_strtolower($t->statut ?? ''), 'attente'))->count();

        // Le retard est un indicateur distinct et ne s'additionne JAMAIS une seconde fois au total des tâches
        $retards     = $lignes->where('retard', true)->count();

        return (object) [
            'total'        => $lignes->count(),
            'termines'     => $termines,
            'enCours'      => $enCours,
            'aFaire'       => $aFaire,
            'incompletes'  => $incompletes,
            'attente'      => $attente,
            'retards'      => $retards,
            'delaiMoyen'   => $delais->isNotEmpty() ? round($delais->avg(), 1) : null,
            'delaisJuges'  => $delais->count(),
            'pointeHeures' => $pointes->isNotEmpty() ? round($pointes->sum(), 1) : 0,
            'pointeTaches' => $pointes->count(),
            'gens'         => $gens->count(),
        ];
    }

    /**
     * Tous les collaborateurs actifs du cabinet, meme sans tache sur la
     * semaine : sans eux, la liste « Collaborateurs » du rapport ne
     * proposait que les personnes ayant eu une tache dans la periode.
     */
    private function collaborateursActifs()
    {
        return DB::table('users')
            ->join('employee_details', 'employee_details.user_id', '=', 'users.id')
            ->join('role_user', 'role_user.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->where('roles.name', 'employee')
            ->where('users.company_id', company()->id)
            ->where('users.status', 'active')
            ->select('users.id', 'users.name')
            ->distinct()
            ->orderBy('users.name')
            ->get();
    }

    private function activiteParCollaborateur($lignes, ?int $limiteA = null)
    {
        $par = [];

        /* Rapport de tout le cabinet : chacun part d'une ligne a zero, les
           taches de la semaine viennent ensuite la remplir. Un rapport
           limite a une personne reste limite a elle. */
        foreach ($limiteA ? [] : $this->collaborateursActifs() as $u) {
            $par[$u->id] = (object) [
                'id'          => $u->id,
                'nom'         => $u->name,
                'total'       => 0,
                'termines'    => 0,
                'enCours'     => 0,
                'aFaire'      => 0,
                'incompletes' => 0,
                'attente'     => 0,
                'retards'     => 0,
                'delais'      => [],
                'pointe'      => 0,
            ];
        }

        foreach ($lignes as $t) {
            foreach ($t->gens as $g) {
                $par[$g->id] ??= (object) [
                    'id'          => $g->id,
                    'nom'         => $g->nom,
                    'total'       => 0,
                    'termines'    => 0,
                    'enCours'     => 0,
                    'aFaire'      => 0,
                    'incompletes' => 0,
                    'attente'     => 0,
                    'retards'     => 0,
                    'delais'      => [],
                    'pointe'      => 0,
                ];

                $l = $par[$g->id];
                // Chaque tâche n'est comptabilisée qu'une seule fois dans le total du collaborateur
                $l->total++;

                $isDoing      = in_array($t->slug, ['doing', 'in_progress', 'en_cours']) || mb_strtolower($t->statut ?? '') === 'en cours';
                $isCompleted  = in_array($t->slug, ['completed', 'terminee', 'terminée']) || mb_strtolower($t->statut ?? '') === 'terminée';
                $isToDo       = in_array($t->slug, ['to_do', 'a_faire', 'a-faire', 'todo']) || mb_strtolower($t->statut ?? '') === 'à faire';
                $isIncomplete = in_array($t->slug, ['incomplete', 'incomplète']) || mb_strtolower($t->statut ?? '') === 'incomplète';
                $isWaiting    = in_array($t->slug, ['waiting_approval', 'en_attente']) || str_contains(mb_strtolower($t->statut ?? ''), 'attente');

                if ($isCompleted)       { $l->termines++; }
                elseif ($isDoing)       { $l->enCours++; }
                elseif ($isToDo)        { $l->aFaire++; }
                elseif ($isIncomplete)  { $l->incompletes++; }
                elseif ($isWaiting)     { $l->attente++; }
                else                    { $l->aFaire++; }

                // Indicateur de retard distinct (ne modifie pas le total)
                if ($t->retard)         { $l->retards++; }
                if ($t->delai !== null) { $l->delais[] = $t->delai; }
                if ($t->pointe !== null){ $l->pointe += $t->pointe; }
            }
        }

        return collect($par)
            ->map(function ($l) {
                $l->delaiMoyen = $l->delais
                    ? round(array_sum($l->delais) / count($l->delais), 1)
                    : null;
                unset($l->delais);

                return $l;
            })
            // Les plus actifs d'abord, puis par ordre alphabetique.
            ->sortBy([['total', 'desc'], ['nom', 'asc']])
            ->values();
    }

    /**
     * Amélioration version suivante : Regroupement des tâches et résultats par service.
     */
    protected function activiteParService($lignes)
    {
        $services = DB::table('teams')
            ->where('company_id', company()->id)
            ->select('id', 'team_name')
            ->get();

        // Récupérer le service de chaque employé
        $employesServices = DB::table('employee_details')
            ->whereNotNull('department_id')
            ->pluck('department_id', 'user_id');

        $parService = [];
        foreach ($services as $srv) {
            $parService[$srv->id] = (object) [
                'id'          => $srv->id,
                'nom'         => $srv->team_name,
                'total'       => 0,
                'termines'    => 0,
                'enCours'     => 0,
                'aFaire'      => 0,
                'incompletes' => 0,
                'retards'     => 0,
                'delais'      => [],
            ];
        }

        $sansService = (object) [
            'id'          => null,
            'nom'         => __('Non rattaché'),
            'total'       => 0,
            'termines'    => 0,
            'enCours'     => 0,
            'aFaire'      => 0,
            'incompletes' => 0,
            'retards'     => 0,
            'delais'      => [],
        ];

        foreach ($lignes as $t) {
            $servicesTouches = [];
            foreach ($t->gens as $g) {
                $srvId = $employesServices[$g->id] ?? null;
                if ($srvId && isset($parService[$srvId])) {
                    $servicesTouches[$srvId] = $parService[$srvId];
                }
            }

            if (empty($servicesTouches)) {
                $servicesTouches['sans'] = $sansService;
            }

            foreach ($servicesTouches as $s) {
                $s->total++;
                if (in_array($t->slug, ['completed', 'terminee', 'terminée']))   { $s->termines++; }
                elseif (in_array($t->slug, ['doing', 'in_progress', 'en_cours'])) { $s->enCours++; }
                elseif (in_array($t->slug, ['to_do', 'a_faire', 'a-faire']))      { $s->aFaire++; }
                elseif (in_array($t->slug, ['incomplete', 'incomplète']))         { $s->incompletes++; }
                else                                                              { $s->aFaire++; }

                if ($t->retard) { $s->retards++; }
                if ($t->delai !== null) { $s->delais[] = $t->delai; }
            }
        }

        $liste = collect(array_values($parService));
        if ($sansService->total > 0) {
            $liste->push($sansService);
        }

        return $liste->map(function ($s) {
            $s->delaiMoyen = !empty($s->delais) ? round(array_sum($s->delais) / count($s->delais), 1) : null;
            unset($s->delais);
            return $s;
        })->sortByDesc('total')->values();
    }

    /**
     * Amélioration version suivante : Regroupement des tâches et de leur avancement par client.
     */
    protected function activiteParClient($lignes)
    {
        $parClient = [];

        foreach ($lignes as $t) {
            $nomClient = $t->client ?: __('Cabinet / Sans client');

            if (!isset($parClient[$nomClient])) {
                $parClient[$nomClient] = (object) [
                    'nom'         => $nomClient,
                    'total'       => 0,
                    'termines'    => 0,
                    'enCours'     => 0,
                    'aFaire'      => 0,
                    'incompletes' => 0,
                    'retards'     => 0,
                ];
            }

            $c = $parClient[$nomClient];
            $c->total++;

            if (in_array($t->slug, ['completed', 'terminee', 'terminée']))   { $c->termines++; }
            elseif (in_array($t->slug, ['doing', 'in_progress', 'en_cours'])) { $c->enCours++; }
            elseif (in_array($t->slug, ['to_do', 'a_faire', 'a-faire']))      { $c->aFaire++; }
            elseif (in_array($t->slug, ['incomplete', 'incomplète']))         { $c->incompletes++; }
            else                                                              { $c->aFaire++; }

            if ($t->retard) { $c->retards++; }
        }

        return collect(array_values($parClient))->sortByDesc('total')->values();
    }

    /**
     * Amélioration version suivante : Séparation des stagiaires des autres collaborateurs.
     */
    protected function separationStagiaires($collaborateurs): array
    {
        if ($collaborateurs->isEmpty()) {
            return [collect(), collect()];
        }

        $collabIds = $collaborateurs->pluck('id')->all();

        // Stagiaires identifiés par leur désignation ou leurs rôles
        $designationsStagiaires = DB::table('designations')
            ->where('company_id', company()->id)
            ->whereRaw('LOWER(name) LIKE ?', ['%stagiaire%'])
            ->pluck('id');

        $usersParDesignation = DB::table('employee_details')
            ->whereIn('user_id', $collabIds)
            ->whereIn('designation_id', $designationsStagiaires)
            ->pluck('user_id')
            ->all();

        $usersParRole = DB::table('role_user')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->whereIn('role_user.user_id', $collabIds)
            ->whereRaw('LOWER(roles.name) LIKE ?', ['%stagiaire%'])
            ->pluck('role_user.user_id')
            ->all();

        $stagiairesIds = array_unique(array_merge($usersParDesignation, $usersParRole));

        $stagiaires = $collaborateurs->filter(fn($c) => in_array($c->id, $stagiairesIds))->values();
        $standards  = $collaborateurs->filter(fn($c) => !in_array($c->id, $stagiairesIds))->values();

        return [
            'employes'   => $standards,
            'stagiaires' => $stagiaires,
            0            => $standards,
            1            => $stagiaires,
        ];
    }

    /**
     * Amélioration version suivante : Échéances prévues pendant la semaine suivante.
     */
    protected function echeancesSemaineSuivante(Carbon $finSemaine, ?int $limiteA = null)
    {
        $debutSuivante = $finSemaine->copy()->addSecond()->startOfDay();
        $finSuivante   = $debutSuivante->copy()->endOfWeek(Carbon::SUNDAY);

        $requete = DB::table('tasks')
            ->leftJoin('taskboard_columns', 'taskboard_columns.id', '=', 'tasks.board_column_id')
            ->leftJoin('projects', 'projects.id', '=', 'tasks.project_id')
            ->leftJoin('users AS cli', 'cli.id', '=', 'projects.client_id')
            ->whereNull('tasks.deleted_at')
            ->where('tasks.company_id', company()->id)
            ->whereBetween('tasks.due_date', [$debutSuivante, $finSuivante]);

        if ($limiteA !== null) {
            $requete->whereExists(function ($q) use ($limiteA) {
                $q->select(DB::raw(1))
                  ->from('task_users')
                  ->whereColumn('task_users.task_id', 'tasks.id')
                  ->where('task_users.user_id', $limiteA);
            });
        }

        $taches = $requete->select(
            'tasks.id', 'tasks.task_short_code AS reference', 'tasks.heading AS titre',
            'tasks.due_date AS echeance', 'tasks.priority AS priorite',
            'taskboard_columns.column_name AS statut', 'taskboard_columns.slug', 'taskboard_columns.label_color AS couleur',
            'projects.project_name AS projet', 'cli.name AS client'
        )->orderBy('tasks.due_date')->get();

        $ids = $taches->pluck('id')->all();
        $affectations = DB::table('task_users')
            ->join('users', 'users.id', '=', 'task_users.user_id')
            ->whereIn('task_users.task_id', $ids)
            ->select('task_users.task_id', 'users.id', 'users.name')
            ->get()
            ->groupBy('task_id');

        return $taches->map(function ($t) use ($affectations) {
            $t->gens = ($affectations[$t->id] ?? collect())
                ->map(fn($u) => (object) ['id' => $u->id, 'nom' => $u->name])
                ->values();
            $t->echeance = $t->echeance ? Carbon::parse($t->echeance) : null;
            return $t;
        });
    }

}
