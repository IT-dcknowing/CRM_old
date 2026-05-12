<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index() {
   
    // POUR LE TEST : On prend le premier utilisateur de la base
    // Plus tard, on remettra $user = auth()->user();
    $user = \App\Models\User::first(); 

    // Les vraies requêtes basées sur cet utilisateur
    $inProcessTasks = \App\Models\Task::where('added_by', $user->id)->count();
    $dueTasks = \App\Models\Task::where('due_date', '<', now())->count();
    $totalProjects = \App\Models\Project::count();

    return response()->json([
        'user' => [
            'name' => $user->name,
            'image_url' => $user->image_url,
            'designation' => $user->employeeDetail->designation->name ?? 'Responsable',
            'employee_id' => $user->employeeDetail->employee_id ?? '24',
        ],
        'stats' => [
            'pending_tasks' => $inProcessTasks,
            'overdue_tasks' => $dueTasks,
            'total_projects' => $totalProjects,
        ]
    ]);
    }
}
