<?php

namespace App\Http\Controllers;

use App\DataTables\TaskReportDataTable;
use App\Helper\Reply;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskboardColumn;
use App\Models\TaskCategory;
use App\Models\TaskLabelList;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RapportHebdomadaireExport;
use App\Traits\RapportHebdomadaire;
use Illuminate\Support\Facades\DB;

class TaskReportController extends AccountBaseController
{

    use RapportHebdomadaire;

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.taskReport';
    }

    public function index(TaskReportDataTable $dataTable)
    {
        abort_403(user()->permission('view_task_report') != 'all');

        if (!request()->ajax()) {
            $this->projects = Project::allProjects();
            $this->clients = User::allClients();
            $this->employees = User::allEmployees();
            $this->taskBoardStatus = TaskboardColumn::all();
            $this->taskCategories = TaskCategory::all();
            $this->taskLabels = TaskLabelList::all();
        }

        return $dataTable->render('reports.tasks.index', $this->data);
    }

    /**
     * Controle d'acces au rapport, en un seul endroit.
     *
     * Le rapport porte sur tout le cabinet et n'est ouvert qu'aux droits
     * complets. Le retour null vaut « aucune restriction » : la signature
     * reste celle attendue par rapportHebdomadaire(), qui sait par
     * ailleurs se limiter a une personne si le besoin revient.
     *
     * @return int|null toujours null : aucun perimetre restreint
     */
    private function perimetreRapport(): ?int
    {
        abort_403(user()->permission('view_task_report') != 'all');

        return null;
    }

    /**
     * Rapport hebdomadaire : synthese de la semaine, activite par
     * collaborateur et detail des taches.
     *
     * Deuxieme regard sur les memes donnees que index(), d'ou l'onglet
     * plutot qu'une entree de menu. Meme permission, meme controleur :
     * aucune regle d'acces nouvelle.
     */
    public function hebdomadaire(Request $request)
    {
        $this->rapport = $this->rapportHebdomadaire(
            $request->query('semaine'),
            $this->perimetreRapport()
        );

        /* Semaines voisines, calculees ici pour que la vue n'ait aucune
           date a manipuler. */
        $this->semainePrecedente = $this->rapport->debut->copy()->subWeek()->format('o-\WW');
        $this->semaineSuivante = $this->rapport->debut->copy()->addWeek()->format('o-\WW');
        $this->semaineCourante = now(company()->timezone)->format('o-\WW');

        $this->pageTitle = __('Rapport hebdomadaire');

        return view('reports.tasks.hebdomadaire', $this->data);
    }

    /**
     * Export Excel du rapport hebdomadaire.
     *
     * Reutilise maatwebsite/excel, deja present, et le meme calcul que
     * l'ecran : l'export ne peut pas diverger de ce qui est affiche.
     */
    public function exportHebdomadaire(Request $request)
    {
        $rapport = $this->rapportHebdomadaire(
            $request->query('semaine'),
            $this->perimetreRapport()
        );

        $nom = 'rapport-hebdomadaire-' . $rapport->debut->format('Y-m-d') . '.xlsx';

        return Excel::download(new RapportHebdomadaireExport($rapport), $nom);
    }

    /**
     * Export PDF du rapport hebdomadaire.
     *
     * Reutilise dompdf, deja present, et le meme calcul que l'ecran.
     * La vue PDF est distincte car dompdf ne connait ni flexbox ni
     * grid : sa mise en page repose sur des tableaux.
     */
    public function pdfHebdomadaire(Request $request)
    {
        $limiteA = $this->perimetreRapport();

        $this->rapport = $this->rapportHebdomadaire($request->query('semaine'), $limiteA);
        $this->societe = company();
        $this->pourQui = $limiteA ? user()->name : null;

        /* Le logo du cabinet, tel que le reste de l'application le sert.
           dompdf lit les fichiers locaux, pas les URL : on lui donne donc
           le chemin sur le disque lorsqu'on le connait. Tant qu'aucun
           logo n'est televerse, logo_url renvoie celui de l'editeur du
           CRM : on prefere alors n'en afficher aucun. */
        $this->logo = null;
        $url = companyOrGlobalSetting()->logo_url;

        if ($url && !str_contains($url, 'worksuite-logo')) {
            $chemin = public_path(parse_url($url, PHP_URL_PATH) ?? '');
            $this->logo = is_file($chemin) ? $chemin : $url;
        }

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('reports.tasks.pdf.hebdomadaire', $this->data);
        $pdf->setPaper('a4', 'portrait');

        $nom = 'rapport-hebdomadaire-' . $this->rapport->debut->format('Y-m-d') . '.pdf';

        return $pdf->download($nom);
    }

    public function taskChartData(Request $request)
    {
        $taskStatus = TaskboardColumn::all();
        $data['labels'] = $taskStatus->pluck('column_name');
        $data['colors'] = $taskStatus->pluck('label_color');
        $data['values'] = [];

        $startDate = $endDate = null;

        if ($request->startDate !== null && $request->startDate != 'null' && $request->startDate != '') {
            $startDate = companyToDateString($request->startDate);
        }

        if ($request->endDate !== null && $request->endDate != 'null' && $request->endDate != '') {
            $endDate = companyToDateString($request->endDate);
        }

        $projectId = $request->projectId;
        $taskBoardColumn = TaskboardColumn::completeColumn();

        $model = Task::leftJoin('projects', 'projects.id', '=', 'tasks.project_id')
            ->leftJoin('users as creator_user', 'creator_user.id', '=', 'tasks.created_by')
            ->leftJoin('task_labels', 'task_labels.task_id', '=', 'tasks.id')
            ->leftJoin('task_users', 'task_users.task_id', '=', 'tasks.id');

        if ($startDate !== null && $endDate !== null) {
            $model->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween(DB::raw('DATE(tasks.`due_date`)'), [$startDate, $endDate]);

                $q->orWhereBetween(DB::raw('DATE(tasks.`start_date`)'), [$startDate, $endDate]);
            });
        }

        if ($projectId != 0 && $projectId != null && $projectId != 'all') {
            $model->where('tasks.project_id', '=', $projectId);
        }

        if ($request->clientID != '' && $request->clientID != null && $request->clientID != 'all') {
            $model->where('projects.client_id', '=', $request->clientID);
        }

        if ($request->assignedTo != '' && $request->assignedTo != null && $request->assignedTo != 'all') {
            $model->where('task_users.user_id', '=', $request->assignedTo);
        }

        if ($request->assignedBY != '' && $request->assignedBY != null && $request->assignedBY != 'all') {
            $model->where('creator_user.id', '=', $request->assignedBY);
        }

        if ($request->status != '' && $request->status != null && $request->status != 'all') {
            if ($request->status == 'not finished') {
                $model->where('tasks.board_column_id', '<>', $taskBoardColumn->id);
            }
            else {
                $model->where('tasks.board_column_id', '=', $request->status);
            }
        }

        if ($request->label != '' && $request->label != null && $request->label != 'all') {
            $model->where('task_labels.label_id', '=', $request->label);
        }

        if ($request->category_id != '' && $request->category_id != null && $request->category_id != 'all') {
            $model->where('tasks.task_category_id', '=', $request->category_id);
        }

        if ($request->billable != '' && $request->billable != null && $request->billable != 'all') {
            $model->where('tasks.billable', '=', $request->billable);
        }

        if ($request->searchText != '') {
            $model->where(function ($query) {
                $query->where('tasks.heading', 'like', '%' . request('searchText') . '%')
                    ->orWhere('member.name', 'like', '%' . request('searchText') . '%')
                    ->orWhere('projects.project_name', 'like', '%' . request('searchText') . '%');
            });
        }

        $countsByColumn = $model->select('tasks.board_column_id', DB::raw('count(*) as aggregate'))
            ->groupBy('tasks.board_column_id')
            ->pluck('aggregate', 'tasks.board_column_id');

        foreach ($taskStatus as $label) {
            $data['values'][] = (int) ($countsByColumn[$label->id] ?? 0);
        }

        $this->chartData = $data;
        $html = view('reports.tasks.chart', $this->data)->render();
        return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
    }

}
