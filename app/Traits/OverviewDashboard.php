<?php

namespace App\Traits;

use App\Models\DashboardWidget;
use App\Models\Deal;
use App\Models\Leave;
use App\Models\Payment;
use App\Models\Project;
use App\Models\ProjectActivity;
use App\Models\ProjectTimeLog;
use App\Models\Task;
use App\Models\TaskboardColumn;
use App\Models\RegulatoryDeadline;
use App\Models\Ticket;
use App\Models\UserActivity;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 *
 */
trait OverviewDashboard
{

    /**
     *
     * @return void
     */
    public function overviewDashboard()
    {
        abort_403($this->viewOverviewDashboard !== 'all');

        $this->startDate = (request('startDate') != '') ? Carbon::createFromFormat($this->company->date_format, request('startDate')) : now($this->company->timezone)->startOfMonth();
        $this->endDate = (request('endDate') != '') ? Carbon::createFromFormat($this->company->date_format, request('endDate')) : now($this->company->timezone);
        $startDate = $this->startDate->toDateString();
        $endDate = $this->endDate->toDateString();

        $completedTaskColumn = TaskboardColumn::completeColumn();

        $this->counts = DB::table('users')
            ->select(
                DB::raw('(select count(users.id) from `users` inner join role_user on role_user.user_id=users.id inner join roles on roles.id=role_user.role_id WHERE roles.name = "client" AND users.company_id = ' . company()->id . ') as totalClients'),
                DB::raw('(select count(users.id) from `users` inner join role_user on role_user.user_id=users.id inner join roles on roles.id=role_user.role_id WHERE roles.name = "employee" and users.status = "active" AND users.company_id = ' . company()->id . ') as totalEmployees'),
                DB::raw('(select count(projects.id) from `projects` WHERE projects.company_id = ' . company()->id . ') as totalProjects'),
                DB::raw('(select count(invoices.id) from `invoices` where (status = "unpaid" or status = "partial") AND invoices.company_id = ' . company()->id . ') as totalUnpaidInvoices'),
                DB::raw('(select sum(project_time_logs.total_minutes) from `project_time_logs` where approved = "1" AND project_time_logs.company_id = ' . company()->id . ') as totalHoursLogged'),
                DB::raw('(select sum(project_time_log_breaks.total_minutes) from `project_time_log_breaks` WHERE project_time_log_breaks.company_id = ' . company()->id . ') as totalBreakMinutes'),
                DB::raw('(select count(tasks.id) from `tasks` where tasks.board_column_id=' . $completedTaskColumn->id . ' and is_private = "0" AND tasks.company_id = ' . company()->id . ') as totalCompletedTasks'),
                DB::raw('(select count(tasks.id) from `tasks` where tasks.board_column_id != ' . $completedTaskColumn->id . ' and is_private = "0" and tasks.deleted_at IS NULL AND tasks.company_id = ' . company()->id . ') as totalPendingTasks'),
                DB::raw('(select count(distinct(attendances.user_id)) from `attendances` inner join users as atd_user on atd_user.id=attendances.user_id inner join role_user on role_user.user_id=atd_user.id inner join roles on roles.id=role_user.role_id WHERE roles.name = "employee" and attendances.clock_in_time >= "' . today(company()->timezone)->setTimezone('UTC')->toDateTimeString() . '" and atd_user.status = "active" AND attendances.company_id = ' . company()->id . ') as totalTodayAttendance'),
                DB::raw('(select count(tickets.id) from `tickets` where (status="open") and deleted_at IS NULL AND tickets.company_id = ' . company()->id . ') as totalOpenTickets'),
                DB::raw('(select count(tickets.id) from `tickets` where (status="resolved" or status="closed") and deleted_at IS NULL AND tickets.company_id = ' . company()->id . ') as totalResolvedTickets')
            )
            ->first();

        $this->diligenceKpis   = $this->calculerDiligenceKpis();
        $this->dashLivraisons  = $this->livraisonsParMois();
        $this->dashRepartition = $this->repartitionPortefeuille();
        $this->dashATraiter    = $this->diligencesATraiter();
        $this->dashEcheances   = RegulatoryDeadline::prochaines(30, company()->id);

        $minutes = $this->counts->totalHoursLogged - $this->counts->totalBreakMinutes;
        $hours = intdiv($minutes, 60);
        $remainingMinutes = $minutes % 60;

        $timeLog = $hours . ' ' . __('app.hrs');

        if ($remainingMinutes > 0) {
            $timeLog .= ' ' . $remainingMinutes . ' ' . __('app.mins');
        }

        $this->counts->totalHoursLogged = $timeLog;
        $this->widgets = DashboardWidget::where('dashboard_type', 'admin-dashboard')->get();

        $this->activeWidgets = $this->widgets->filter(function ($value, $key) {
            return $value->status == '1';
        })->pluck('widget_name')->toArray();

        $this->earningChartData = $this->earningChart($startDate, $endDate);
        $this->timlogChartData = $this->timelogChart($startDate, $endDate);

        $this->leaves = Leave::with('user', 'type')
            ->where('status', 'pending')
            ->whereBetween('leave_date', [$startDate, $endDate])
            ->get();

        $this->newTickets = Ticket::with('requester')->where('status', 'open')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->orderByDesc('updated_at')->get();

        $this->pendingTasks = Task::with('project', 'users', 'boardColumn')
            ->where('tasks.board_column_id', '<>', $completedTaskColumn->id)
            ->where('tasks.is_private', 0)
            ->orderByDesc('due_date')
            ->whereBetween('due_date', [$startDate, $endDate])
            ->limit(15)
            ->get();


        $currentDate = now()->timezone($this->company->timezone)->toDateTimeString();

        $this->pendingLeadFollowUps = Deal::with('followup', 'leadAgent', 'leadAgent.user', 'leadAgent.user.employeeDetail', 'leadAgent.user.employeeDetail.designation')
            ->selectRaw('deals.id,leads.company_name, leads.client_name as client_name, deals.agent_id, ( select lead_follow_up.next_follow_up_date from lead_follow_up where lead_follow_up.deal_id = deals.id and DATE(lead_follow_up.next_follow_up_date) < "' . $currentDate . '" ORDER BY lead_follow_up.created_at DESC Limit 1) as follow_up_date_past,
            ( select lead_follow.next_follow_up_date from lead_follow_up as lead_follow where lead_follow.deal_id = deals.id and status = "incomplete" ORDER BY lead_follow.created_at DESC Limit 1) as follow_up_date_next'
            )
            ->leftJoin('leads', 'leads.id', 'deals.lead_id')
            ->where('deals.next_follow_up', 'yes')
            ->groupBy('deals.id')
            ->get();

        $this->pendingLeadFollowUps = $this->pendingLeadFollowUps->filter(function ($value, $key) {
            return $value->follow_up_date_past != null && $value->follow_up_date_next == null && $value->followup->status != 'completed';
        });

        $this->projectActivities = ProjectActivity::with('project')
            ->join('projects', 'projects.id', '=', 'project_activity.project_id')
            ->where('projects.company_id', company()->id)
            ->whereNull('projects.deleted_at')
            ->select('project_activity.*')
            ->limit(15)
            ->whereBetween('project_activity.created_at', [$startDate, $endDate])
            ->orderBy('project_activity.id', 'desc')
            ->groupBy('project_activity.id')
            ->get();

        $this->userActivities = UserActivity::with('user')->limit(15)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderByDesc('id')->get();

        $this->view = 'dashboard.ajax.overview';
    }

    /**
     * XXXXXXXXXXX
     *
     * @return \Illuminate\Http\Response
     */
    public function earningChart($startDate, $endDate)
    {
        $payments = Payment::join('currencies', 'currencies.id', '=', 'payments.currency_id')->where('status', 'complete');

        $payments = $payments->whereBetween('payments.paid_on', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()]);

        $payments = $payments->orderBy('paid_on', 'ASC')
            ->get([
                DB::raw('DATE_FORMAT(paid_on,"%d-%M-%y") as date'),
                DB::raw('YEAR(paid_on) year, MONTH(paid_on) month'),
                DB::raw('amount as total'),
                'currencies.id as currency_id',
                'currencies.exchange_rate'
            ]);

        $incomes = [];

        foreach ($payments as $invoice) {

            if (!isset($incomes[$invoice->date])) {
                $incomes[$invoice->date] = 0;
            }

            if ($invoice->currency_id != $this->company->currency_id && $invoice->exchange_rate != 0) {
                $incomes[$invoice->date] += floor((float)$invoice->total / (float)$invoice->exchange_rate);
            }
            else {
                $incomes[$invoice->date] += round($invoice->total, 2);
            }

        }

        $dates = array_keys($incomes);
        $graphData = [];

        foreach ($dates as $date) {
            $graphData[] = [
                'date' => $date,
                'total' => isset($incomes[$date]) ? round($incomes[$date], 2) : 0,
            ];
        }

        usort($graphData, function ($a, $b) {
            $t1 = strtotime($a['date']);
            $t2 = strtotime($b['date']);

            return $t1 - $t2;
        });

        // return $graphData;
        $graphData = collect($graphData);

        $data['labels'] = $graphData->pluck('date');
        $data['values'] = $graphData->pluck('total')->toArray();
        $data['colors'] = [$this->appTheme->header_color];
        $data['name'] = __('app.earnings');

        return $data;
    }

    /**
     * XXXXXXXXXXX
     *
     * @return \Illuminate\Http\Response
     */
    public function timelogChart($startDate, $endDate)
    {
        $timelogs = ProjectTimeLog::whereBetween('start_time', [$startDate, $endDate]);
        $timelogs = $timelogs->where('project_time_logs.approved', 1);
        $timelogs = $timelogs->groupBy('date')
            ->orderBy('start_time', 'ASC')
            ->get([
                DB::raw('DATE_FORMAT(start_time,\'%d-%M-%y\') as date'),
                DB::raw('FLOOR(sum(total_minutes/60)) as total_hours')
            ]);
        $data['labels'] = $timelogs->pluck('date');
        $data['values'] = $timelogs->pluck('total_hours')->toArray();
        $data['colors'] = [$this->appTheme->header_color];
        $data['name'] = __('modules.dashboard.totalHoursLogged');

        return $data;
    }


    /**
     * Indicateurs de tete du tableau de bord, conformes a la maquette
     * pilotage-dc-knowing : diligences suivies, en cours, en retard et
     * part livree dans les delais.
     *
     * Une seule requete, en sous-selects, sur le modele de $this->counts :
     * le tableau de bord ne gagne donc qu'un aller-retour au total.
     *
     * Reserve sur le dernier indicateur : la table projects ne conserve
     * aucune date de livraison. On approche la date de cloture par
     * updated_at, qui bouge a chaque modification — le pourcentage est
     * donc indicatif et peut etre sous-estime. A remplacer le jour ou
     * une colonne de date de cloture existera.
     */
    private function calculerDiligenceKpis()
    {
        $id = company()->id;

        $k = DB::table('projects')
            ->selectRaw('COUNT(*) as suivies')
            ->selectRaw("SUM(status = 'En cours') as enCours")
            ->selectRaw("SUM(deadline IS NOT NULL AND deadline < CURDATE() AND status <> 'terminée') as enRetard")
            ->selectRaw("SUM(status = 'terminée' AND deadline IS NOT NULL) as termineesAvecEcheance")
            ->selectRaw("SUM(status = 'terminée' AND deadline IS NOT NULL AND DATE(updated_at) <= deadline) as dansLesDelais")
            ->selectRaw('SUM(created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)) as creees30j')
            ->where('company_id', $id)
            ->whereNull('deleted_at')
            ->first();

        $suivies = (int) $k->suivies;
        $avecEcheance = (int) $k->termineesAvecEcheance;

        return (object) [
            'suivies'    => $suivies,
            'enCours'    => (int) $k->enCours,
            'enRetard'   => (int) $k->enRetard,
            'creees30j'  => (int) $k->creees30j,
            'pctEnCours' => $suivies ? round(100 * $k->enCours / $suivies) : 0,
            'pctRetard'  => $suivies ? round(100 * $k->enRetard / $suivies) : 0,
            'pctDelais'  => $avecEcheance ? round(100 * $k->dansLesDelais / $avecEcheance) : null,
            'termineesAvecEcheance' => $avecEcheance,
        ];
    }


    /**
     * Diligences terminees par mois sur douze mois glissants.
     *
     * Reserve : la table projects ne conserve pas de date de livraison.
     * On se rabat sur updated_at, qui bouge a chaque modification — la
     * repartition mensuelle est donc approximative. A remplacer par une
     * vraie colonne de cloture le jour ou elle existera.
     *
     * @return array{labels: string[], valeurs: int[], total: int}
     */
    private function livraisonsParMois()
    {
        $debut = now()->startOfMonth()->subMonths(11);

        $lignes = DB::table('projects')
            ->selectRaw("DATE_FORMAT(updated_at, '%Y-%m') as mois, COUNT(*) as n")
            ->where('company_id', company()->id)
            ->whereNull('deleted_at')
            ->where('status', 'terminée')
            ->where('updated_at', '>=', $debut)
            ->groupBy('mois')
            ->pluck('n', 'mois');

        $labels = [];
        $valeurs = [];
        $curseur = $debut->copy();

        for ($i = 0; $i < 12; $i++) {
            $cle = $curseur->format('Y-m');
            $labels[] = ucfirst($curseur->translatedFormat('M'));
            $valeurs[] = (int) ($lignes[$cle] ?? 0);
            $curseur->addMonth();
        }

        return ['labels' => $labels, 'valeurs' => $valeurs, 'total' => array_sum($valeurs)];
    }

    /**
     * Repartition du portefeuille en quatre etats, comme la maquette.
     *
     * « En retard » n'est pas un statut stocke : il se deduit d'une
     * echeance depassee sur une diligence ni terminee ni annulee. Il
     * prime sur le statut d'origine, sinon une diligence en retard
     * serait comptee deux fois.
     *
     * @return array<int, array{cle: string, libelle: string, n: int, pct: float, couleur: string}>
     */
    private function repartitionPortefeuille()
    {
        $r = DB::table('projects')
            ->selectRaw("SUM(status = 'terminée') as terminees")
            ->selectRaw("SUM(deadline IS NOT NULL AND deadline < CURDATE() AND status NOT IN ('terminée','annulée')) as enRetard")
            ->selectRaw("SUM(status = 'En cours' AND NOT (deadline IS NOT NULL AND deadline < CURDATE())) as enCours")
            ->selectRaw("SUM(status NOT IN ('terminée','En cours','annulée') AND NOT (deadline IS NOT NULL AND deadline < CURDATE())) as aFaire")
            ->where('company_id', company()->id)
            ->whereNull('deleted_at')
            ->first();

        $etats = [
            ['terminees', __('Terminées'), (int) $r->terminees, '#0E7C5A'],
            ['enCours',   __('En cours'),  (int) $r->enCours,   '#175CD3'],
            ['aFaire',    __('À faire'),   (int) $r->aFaire,    '#667085'],
            ['enRetard',  __('En retard'), (int) $r->enRetard,  '#B42318'],
        ];

        $total = array_sum(array_column($etats, 2));

        return array_map(fn ($e) => [
            'cle'     => $e[0],
            'libelle' => $e[1],
            'n'       => $e[2],
            'pct'     => $total ? round(100 * $e[2] / $total, 1) : 0,
            'couleur' => $e[3],
        ], $etats);
    }

    /**
     * Diligences ouvertes les plus proches de leur echeance.
     * Les relations sont chargees d'avance : pas de requete par ligne.
     */
    private function diligencesATraiter(int $limite = 6)
    {
        return Project::with(['client:id,name,image', 'members.user:id,name,image'])
            ->select('id', 'project_name', 'project_short_code', 'client_id', 'deadline', 'status', 'completion_percent')
            ->where('company_id', company()->id)
            ->whereNull('deleted_at')
            ->where('status', '<>', 'terminée')
            ->orderByRaw('deadline IS NULL, deadline ASC')
            ->limit($limite)
            ->get();
    }

}
