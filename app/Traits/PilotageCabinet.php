<?php

namespace App\Traits;

use App\Models\Project;
use App\Models\TaskboardColumn;
use Illuminate\Support\Facades\DB;

/**
 * Donnees de l'ecran « Tableau de bord » du cabinet, tel que
 * PAGES.dashboard le decrit dans la maquette dck-flow-task :
 *
 *   quatre indicateurs — taches en cours, diligences actives,
 *   contrats en vigueur, collaborateurs ;
 *   la charge de travail sur six mois, creees contre terminees ;
 *   la repartition des taches par colonne du tableau ;
 *   les diligences dont l'echeance approche ;
 *   le taux de cloture dans les delais.
 *
 * OverviewDashboard fournit deja $counts et $projectActivities : ils ne
 * sont pas recalcules ici.
 */
trait PilotageCabinet
{
    /** Les quatre indicateurs de tete. */
    protected function cabinetIndicateurs(): object
    {
        $id = company()->id;
        $termine = TaskboardColumn::completeColumn();
        $moisDernier = now()->copy()->subDays(30);

        $taches = DB::table('tasks')
            ->selectRaw('COUNT(*) as ouvertes')
            ->selectRaw('SUM(created_at >= ?) as recentes', [$moisDernier])
            ->where('company_id', $id)
            ->whereNull('deleted_at')
            ->where('board_column_id', '<>', $termine->id)
            ->where('is_private', 0)
            ->first();

        $diligences = DB::table('projects')
            ->selectRaw("SUM(status <> 'terminée') as actives")
            ->selectRaw("SUM(status = 'terminée' AND updated_at >= ?) as clotureesMois", [now()->startOfMonth()])
            ->where('company_id', $id)
            ->whereNull('deleted_at')
            ->first();

        $contrats = DB::table('contracts')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(end_date IS NOT NULL AND end_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 45 DAY)) as bientot')
            // La table contracts ne porte pas de suppression douce.
            ->where('company_id', $id)
            ->first();

        $collaborateurs = DB::table('users')
            ->join('role_user', 'role_user.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->where('roles.name', 'employee')
            ->where('users.status', 'active')
            ->where('users.company_id', $id)
            ->distinct()
            ->count('users.id');

        return (object) [
            'tachesOuvertes'  => (int) $taches->ouvertes,
            'tachesRecentes'  => (int) $taches->recentes,
            'diligences'      => (int) $diligences->actives,
            'diligencesMois'  => (int) $diligences->clotureesMois,
            'contrats'        => (int) $contrats->total,
            'contratsBientot' => (int) $contrats->bientot,
            'collaborateurs'  => $collaborateurs,
        ];
    }

    /**
     * Charge de travail sur six mois : taches creees et taches
     * terminees, mois par mois. Deux requetes groupees, pas de boucle
     * de requetes.
     *
     * @return array{labels: string[], creees: int[], terminees: int[]}
     */
    protected function cabinetChargeTravail(): array
    {
        $id = company()->id;
        $termine = TaskboardColumn::completeColumn();
        $debut = now()->startOfMonth()->subMonths(5);

        $creees = DB::table('tasks')
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as m, COUNT(*) as n")
            ->where('company_id', $id)->whereNull('deleted_at')
            ->where('created_at', '>=', $debut)
            ->groupBy('m')->pluck('n', 'm');

        $finies = DB::table('tasks')
            ->selectRaw("DATE_FORMAT(updated_at, '%Y-%m') as m, COUNT(*) as n")
            ->where('company_id', $id)->whereNull('deleted_at')
            ->where('board_column_id', $termine->id)
            ->where('updated_at', '>=', $debut)
            ->groupBy('m')->pluck('n', 'm');

        $labels = [];
        $a = [];
        $b = [];
        $curseur = $debut->copy();

        for ($i = 0; $i < 6; $i++) {
            $cle = $curseur->format('Y-m');
            $labels[] = ucfirst($curseur->translatedFormat('M'));
            $a[] = (int) ($creees[$cle] ?? 0);
            $b[] = (int) ($finies[$cle] ?? 0);
            $curseur->addMonth();
        }

        return ['labels' => $labels, 'creees' => $a, 'terminees' => $b];
    }

    /**
     * Repartition des taches par colonne du tableau, avec la couleur
     * definie sur la colonne elle-meme.
     */
    protected function cabinetRepartitionTaches()
    {
        return TaskboardColumn::query()
            ->select('id', 'column_name', 'label_color', 'slug')
            ->where('company_id', company()->id)
            ->withCount(['tasks as n' => function ($q) {
                $q->whereNull('tasks.deleted_at')->where('tasks.is_private', 0);
            }])
            ->orderBy('priority')
            ->get();
    }

    /** Diligences ouvertes dont l'echeance tombe dans moins de 45 jours. */
    protected function cabinetDiligencesASurveiller(int $limite = 5)
    {
        return Project::with('client:id,name')
            ->select('id', 'project_name', 'client_id', 'deadline', 'completion_percent')
            ->where('company_id', company()->id)
            ->whereNull('deleted_at')
            ->where('status', '<>', 'terminée')
            ->whereNotNull('deadline')
            ->where('deadline', '<=', now()->addDays(45))
            ->orderBy('deadline')
            ->limit($limite)
            ->get();
    }

    /**
     * Part des diligences terminees livrees avant leur echeance.
     *
     * Meme reserve que sur les autres indicateurs de delai : la table
     * ne conserve pas de date de cloture, updated_at en tient lieu.
     */
    protected function cabinetTauxCloture(): ?int
    {
        $r = DB::table('projects')
            ->selectRaw("SUM(status = 'terminée' AND deadline IS NOT NULL) as base")
            ->selectRaw("SUM(status = 'terminée' AND deadline IS NOT NULL AND DATE(updated_at) <= deadline) as dansLesDelais")
            ->where('company_id', company()->id)
            ->whereNull('deleted_at')
            ->first();

        return $r->base ? (int) round(100 * $r->dansLesDelais / $r->base) : null;
    }

    /** Renseigne les variables attendues par la vue de pilotage cabinet. */
    protected function chargerPilotageCabinet(): void
    {
        $this->cabIndicateurs  = $this->cabinetIndicateurs();
        $this->cabCharge       = $this->cabinetChargeTravail();
        $this->cabRepartition  = $this->cabinetRepartitionTaches();
        $this->cabASurveiller  = $this->cabinetDiligencesASurveiller();
        $this->cabTauxCloture  = $this->cabinetTauxCloture();
    }
}
