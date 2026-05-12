<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Stripe\Stripe;
use App\Models\Deal;
use App\Models\Lead;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use App\Helper\Files;
use App\Helper\Reply;
use App\Models\Order;
use App\Models\Ticket;
use GuzzleHttp\Client;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Project;
use App\Models\LeadNote;
use App\Models\Proposal;
use App\Models\TaskFile;
use App\Models\LeadSource;
use App\Models\TicketType;
use App\Models\CreditNotes;
use App\Models\LeadProduct;
use App\Models\TicketGroup;
use App\Models\TicketReply;
use App\Scopes\ActiveScope;
use App\Models\InvoiceItems;
use App\Models\LeadPipeline;
use App\Models\ProposalItem;
use App\Models\ProposalSign;
use App\Models\SituationFiscale;
use App\Models\SituationSociale;
use App\Scopes\CompanyScope;
use Illuminate\Http\Request;
use App\Models\ClientDetails;
use App\Models\ClientDocument;
use App\Models\GlobalSetting;
use App\Models\Notification;
use App\Models\ProjectCategory;
use App\Models\ProjectDepartment;
use App\Models\ProjectFile;
use App\Models\ProjectMember;
use App\Models\ProjectNote;
use App\Models\ProjectStatusSetting;
use App\Models\ProjectTemplate;
use App\Models\ProjectTimeLog;
use App\Models\PipelineStage;
use App\Models\LeadCustomForm;
use App\Models\TaskboardColumn;
use App\Models\TicketCustomForm;
use Froiden\RestAPI\ApiResponse;
use App\Traits\EmployeeDashboard;
use Illuminate\Support\Facades\DB;
use App\Models\ProjectTimeLogBreak;
use Illuminate\Support\Facades\App;
use Nwidart\Modules\Facades\Module;
use App\Traits\UniversalSearchTrait;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;
use App\Models\PaymentGatewayCredentials;
use App\Http\Requests\Lead\StorePublicLead;
use App\Http\Requests\ProposalAcceptRequest;
use App\Http\Requests\Stripe\StoreStripeDetail;
use App\Http\Requests\Tickets\StoreCustomTicket;
use App\Models\GanttLink;
use App\Models\CustomerRequest;
use App\Models\Service;
use App\Models\LanguageSetting;
use App\Models\ProjectMilestone;
use App\Events\NewUserEvent;
use App\Models\Contract;
use App\Models\KnowledgeBase;
use App\Models\Leave;
use App\Models\UserChat;
use App\Models\BankAccount;
use App\Models\Attendance;
use Illuminate\Support\Facades\Session;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use App\Http\Requests\ClientDocs\CreateRequest;
use App\Http\Requests\ClientDocs\UpdateRequest;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    /**
     * Méthode utilitaire pour récupérer l'utilisateur via le token (Header ou URL)
     * Très important pour le bon fonctionnement sur cPanel/Apache
     */
    public function getApiUser()
    {
        try {
            $token = request()->query('token') ?? request()->bearerToken();

            if (!$token) {
                return null;
            }

            JWTAuth::setToken($token);
            $user = JWTAuth::authenticate();

            return $user;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function login(Request $request)
    {
        return response()->json([
            'token' => 'test_token',
            'user' => ['name' => 'Test User']
        ]);
    }

   public function dashboard(Request $request)
{
    // On efface tout et on met juste ça pour tester :
    return response()->json(['userName' => 'Connexion Réussie !']);
}

    public function userInfos(Request $request){
        try {
            $user = $this->getApiUser();
            $company = ClientDetails::where('user_id', $user->id)->first();
            return response()->json([
                'name' => $company->company_name,
                'email' => $user->email,
                'logo' => $company->company_logo,
                'regime' => $company->regime,
                'centreimp' => $company->imp_centre,
                'formjurid' => $company->formjurid
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Utilisateur non connecté.'], 401);
        }
    } 

    public function honoraires($id) {
        try {   
            // Sommes du des montant non payé au cabinet associés au client
            if (is_numeric($id)) {
                // Somme des montants non payés au cabinet associés au client
                $sumAmount = Invoice::where('client_id', $id)
                                    ->where('status', 'unpaid')
                                    ->sum('total');
            
                // Vérifiez si la somme est bien calculée
                if ($sumAmount === null) {
                    $sumAmount = 0; // Si aucune facture n'est trouvée, on initialise à 0
                }
            } else {
                // Gérer le cas où l'ID du client n'est pas valide
                throw new \InvalidArgumentException('ID de client invalide.');
            }
    
            // Retourner le compte des projets
            return response()->json([
                'sum' => number_format($sumAmount, 0, '.', ' '),
            ]);
        } catch (\Exception $e) {
            // Gérer les exceptions et retourner une réponse d'erreur
            return response()->json(['error' => 'Utilisateur non connecté.'], 401);
        }
    }


    public function cptedilis($id) {
        try {   
            // Compter le nombre de projets associés au client
            $cpteproject = Project::where('client_id', $id)->where('status', 'En cours')->count();
    
            // Retourner le compte des projets
            return response()->json([
                'cpte' => $cpteproject,
            ]);
        } catch (\Exception $e) {
            // Gérer les exceptions et retourner une réponse d'erreur
            return response()->json(['error' => 'Utilisateur non connecté.'], 401);
        }
    }

    public function cptedocs($id) {
        try {   
            // Compter le nombre de projets associés au client
            $cptedocs = ClientDocument::where('user_id', $id)->count();
    
            // Retourner le compte des projets
            return response()->json([
                'cpteDocs' => $cptedocs,
            ]);
        } catch (\Exception $e) {
            // Gérer les exceptions et retourner une réponse d'erreur
            return response()->json(['error' => 'Utilisateur non connecté.'], 401);
        }
    }

    public function cptereqs($id) {
        try {   
            // Compter le nombre de requetes associés au client
            $cptereqs = CustomerRequest::where('client_id', $id)->where('status', 'pending')->count();
    
            // Retourner le compte des requetes
            return response()->json([
                'cpteReqs' => $cptereqs,
            ]);
        } catch (\Exception $e) {
            // Gérer les exceptions et retourner une réponse d'erreur
            return response()->json(['error' => 'Utilisateur non connecté.'], 401);
        }
    }

    public function getAlldiligences(Request $request)
    {
        try {
            $user = $this->getApiUser();
            
            // D'abord, récupérons les projets uniques
            $diligences = Project::select(
                    'projects.id',
                    'projects.project_name',
                    'projects.deadline',
                    'projects.status',  
                    'project_category.category_name',
                    'projects.client_id'
                )
                ->leftJoin('project_category', 'project_category.id', '=', 'projects.category_id')
                ->where('projects.client_id', $user->id)
                ->get();

            // Ensuite, pour chaque projet, récupérons les collaborateurs
            $formattedDiligences = $diligences->map(function ($project) {
                // Récupérer tous les collaborateurs pour ce projet
                $collaborators = DB::table('project_members')
                    ->join('users', 'users.id', '=', 'project_members.user_id')
                    ->where('project_members.project_id', $project->id)
                    ->pluck('users.name')
                    ->unique()
                    ->implode(', ');

                return [
                    'id' => $project->id,
                    'project_name' => $project->project_name,
                    'deadline' => \Carbon\Carbon::parse($project->deadline)->locale('fr')->isoFormat('DD MMMM YYYY'),
                    'status' => $project->status,
                    'category_name' => $project->category_name,
                    'collaborators' => $collaborators ?: 'Aucun collaborateur'
                ];
            });

            // Pour debug
            \Log::info('Diligences formatées:', ['data' => $formattedDiligences]);
            
            return response()->json($formattedDiligences);
        } catch (\Exception $e) {
            \Log::error('Erreur dans diligences: ' . $e->getMessage());
            return response()->json([
                'error' => 'Une erreur s\'est produite lors de la récupération des diligences.',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function diligencesShow($id) {
        try {
            // Récupérer les projets associés au client
            $projects = Project::where('client_id', $id)->get();
    
            // Vérifier si des projets existent
            if ($projects->isEmpty()) {
                return response()->json(['message' => 'No projects found for this client.'], 404);
            }
    
            // Retourner les détails des projets
            return response()->json($projects);
        } catch (\Exception $e) {
            // Gérer les exceptions et retourner une réponse d'erreur
            return response()->json(['error' => 'An error occurred while fetching projects.'], 500);
        }
    }

    public function situationsFiscal($id)
    {
        try {
            //$user = JWTAuth::parseToken()->authenticate();
            
            $situations = SituationFiscale::where('client_id', $id)
                ->select(
                    'id',
                    'type_impot',
                    'regime',
                    'montant',
                    'periode',
                    'date_paiement',
                    'status',
                    'file'
                )
                ->orderBy('date_paiement', 'desc')
                ->get()
                ->groupBy('type_impot')
                ->map(function ($items) {
                    $latest = $items->first();
                    return [
                        'id' => $latest->id,
                        'type' => $latest->type_impot,
                        'regime' => $latest->regime,
                        'montant' => number_format($latest->montant, 0, ',', ' ') . ' FCFA',
                        'periode' => $latest->periode,
                        'prochaine_echeance' => \Carbon\Carbon::parse($latest->date_paiement)->locale('fr')->isoFormat('DD MMMM YYYY'),
                        'status' => $latest->status,
                        'document' => $latest->file
                    ];
                })
                ->values();

            return response()->json($situations);
        } catch (\Exception $e) {
            \Log::error('Erreur situation fiscale: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la récupération de la situation fiscale'], 500);
        }
    }

    public function situationsSocial($id)
    {
        try {
            $user = $this->getApiUser();
            
            $situations = SituationSociale::where('client_id', $id)
                ->select(
                    'id',
                    'type_sociale',
                    'regime',
                    'montant',
                    'periode',
                    'date_paiement',
                    'status',
                    'file'
                )
                ->orderBy('date_paiement', 'desc')
                ->get()
                ->groupBy('type_sociale')
                ->map(function ($items) {
                    $latest = $items->first();
                    return [
                        'id' => $latest->id,
                        'type' => $latest->type_sociale,
                        'regime' => $latest->regime,
                        'montant' => number_format($latest->montant, 0, ',', ' ') . ' FCFA',
                        'periode' => $latest->periode,
                        'prochaine_echeance' => \Carbon\Carbon::parse($latest->date_paiement)->locale('fr')->isoFormat('DD MMMM YYYY'),
                        'status' => $latest->status,
                        'document' => $latest->file
                    ];
                })
                ->values();

            return response()->json($situations);
        } catch (\Exception $e) {
            \Log::error('Erreur situation sociale: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la récupération de la situation sociale'], 500);
        }
    }

    public function getFiscalSituation()
    {
        try {
            $user = $this->getApiUser();
            if (!$user) return response()->json(['error' => 'Unauthenticated'], 401);
            
            $situations = SituationFiscale::where('client_id', $user->id)
                ->orderBy('date_paiement', 'desc')
                ->get()
                ->groupBy('type_impot')
                ->map(function ($items) {
                    $latest = $items->first();
                    return [
                        'id' => (string)$latest->id,
                        'type' => $latest->type_impot,
                        'regime' => $latest->regime,
                        'montant' => (float)$latest->montant,
                        'periode' => $latest->periode,
                        'date_paiement' => $latest->date_paiement ? \Carbon\Carbon::parse($latest->date_paiement)->format('d/m/Y') : 'N/A',
                        'status' => $latest->status,
                        'document' => $latest->file
                    ];
                })
                ->values();

            return response()->json($situations);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la récupération de la situation fiscale'], 500);
        }
    }

    public function getSocialSituation()
    {
        try {
            $user = $this->getApiUser();
            if (!$user) return response()->json(['error' => 'Unauthenticated'], 401);
            
            $situations = SituationSociale::where('client_id', $user->id)
                ->orderBy('date_paiement', 'desc')
                ->get()
                ->groupBy('type_sociale')
                ->map(function ($items) {
                    $latest = $items->first();
                    return [
                        'id' => (string)$latest->id,
                        'type' => $latest->type_sociale,
                        'regime' => $latest->regime,
                        'montant' => (float)$latest->montant,
                        'periode' => $latest->periode,
                        'date_paiement' => $latest->date_paiement ? \Carbon\Carbon::parse($latest->date_paiement)->format('d/m/Y') : 'N/A',
                        'status' => $latest->status,
                        'document' => $latest->file
                    ];
                })
                ->values();

            return response()->json($situations);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la récupération de la situation sociale'], 500);
        }
    }


    public function servicesSubscribe(Request $request, $serviceId){
        try {
            // Validation des données
            $validated = $request->validate([
                'service_id' => 'required',
                'name' => 'required|string',
                'type_request' => 'required|string',
                'request_text' => 'required|string',
            ]);
    
            $user = $this->getApiUser();
            
            // Vérifier si la diligence existe
            $service = Service::findOrFail($serviceId);
            
            // Créer la requête
            $customerRequest = CustomerRequest::create([
                'client_id' => $user->id,
                'service_id' => $validated['service_id'],
                'name' => $validated['name'],
                'type_request' => $validated['type_request'],
                'request_text' => $validated['request_text'],
                'status' => 'pending',
                'created_by' => $user->id,
            ]);
    
            return response()->json($customerRequest, 201);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Service non trouvée'], 404);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la soucription à ce service: ' . $e->getMessage());
            return response()->json(['error' => 'Une erreur est survenue lors de la soucription à ce service'], 500);
        }
    }

    public function indexRequest(Request $request)
    {
        try {  

            $user = $this->getApiUser();
            $requests = CustomerRequest::where('client_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();
                
            return response()->json($requests);

        } catch (\Exception $e) {
            // Gérer les exceptions et retourner une réponse d'erreur
            return response()->json(['error' => 'Utilisateur non connecté.'], 401);
        }
    }

    public function storeRequest(Request $request)
    {
        // Validation des données
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type_request' => 'required|string',
            'request_text' => 'required|string',
        ]);

        try {
            $user = $this->getApiUser();
            
            $customerRequest = CustomerRequest::create([
                'client_id' => $user->id, // Utiliser l'ID de l'utilisateur authentifié
                'name' => $validated['name'],
                'type_request' => $validated['type_request'],
                'request_text' => $validated['request_text'],
                'status' => 'pending',
                'created_by' => $user->id,
            ]);

            return response()->json($customerRequest, 201);
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la création de la requête: ' . $e->getMessage());
            return response()->json(['error' => 'Une erreur est survenue lors de la création de la requête'], 500);
        }
    }

    public function showRequest($id)
    {
        try {
                $user = $this->getApiUser();
                $request = CustomerRequest::where('client_id', Auth::id())
                    ->findOrFail($id);
                    
                return response()->json($request);
            } catch (\Exception $e) {
                \Log::error('Erreur lors de l\'affichage de la requête: ' . $e->getMessage());
                return response()->json(['error' => 'Une erreur est survenue lors de l\'affichage de la requête'], 500);
            }
    }

    public function updateRequest(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type_request' => 'required|string',
            'request_text' => 'required|string',
        ]);
    
        try {
            $user = $this->getApiUser();
            
            $customerRequest = CustomerRequest::where('id', $id)
                ->where('client_id', $user->id)
                ->firstOrFail();
    
            if ($customerRequest->status !== 'pending') {
                return response()->json(['error' => 'Seules les requêtes en attente peuvent être modifiées'], 403);
            }
    
            $customerRequest->update($validated);
    
            return response()->json($customerRequest);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Requête non trouvée'], 404);
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la modification de la requête: ' . $e->getMessage());
            return response()->json(['error' => 'Une erreur est survenue lors de la modification de la requête'], 500);
        }
    }

    public function diligencesRequests(Request $request, $diligenceId)
    {
        try {
            // Validation des données
            $validated = $request->validate([
                'project_id' => 'required',
                'name' => 'required|string',
                'type_request' => 'required|string',
                'request_text' => 'required|string',
            ]);
    
            $user = $this->getApiUser();
            
            // Vérifier si la diligence existe
            $diligence = Project::findOrFail($diligenceId);
            
            // Créer la requête
            $customerRequest = CustomerRequest::create([
                'client_id' => $user->id,
                'project_id' => $validated['project_id'],
                'name' => $validated['name'],
                'type_request' => $validated['type_request'],
                'request_text' => $validated['request_text'],
                'status' => 'pending',
                'created_by' => $user->id,
            ]);
    
            return response()->json($customerRequest, 201);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Diligence non trouvée'], 404);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la création de la requête: ' . $e->getMessage());
            return response()->json(['error' => 'Une erreur est survenue lors de la création de la requête'], 500);
        }
    }
    



     public function indexNotification()
    {
        try { 
            $user = $this->getApiUser();

            $notifications = Notification::where('notifiable_id', $user->id)
                ->where('notifiable_type', 'App\Models\User')
                ->where('type', 'App\Notifications\NewProject')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'notifications' => $notifications
            ]);
        } catch (\Exception $e) {
            // Gérer les exceptions et retourner une réponse d'erreur
            return response()->json(['error' => 'Utilisateur non connecté.'], 401);
        }
    }

    public function unreadCount()
    {
        try { 
            $user = $this->getApiUser();

            $count = Notification::where('notifiable_id', $user->id)
                ->where('notifiable_type', 'App\Models\User')
                ->where('type', 'App\Notifications\NewProject')
                ->whereNull('read_at')
                ->count();

            return response()->json([
                'count' => $count
            ]);
        } catch (\Exception $e) {
            // Gérer les exceptions et retourner une réponse d'erreur
            return response()->json(['error' => 'Utilisateur non connecté.'], 401);
        }
    }

    public function markAsRead($id)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $notification = Notification::where('id', $id)->first();
        
        if ($notification->notifiable_id !== $user->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $notification->update(['read_at' => now()]);

        return response()->json(['message' => 'Notification marquée comme lue']);
    }

    public function markAllAsRead()
    {
        try { 
            $user = $this->getApiUser();

            Notification::where('notifiable_id', $user->id)
                ->where('notifiable_type', 'App\Models\User')
                ->where('type', 'App\Notifications\NewProject')
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            return response()->json(['message' => 'Toutes les notifications marquées comme lues']);
        } catch (\Exception $e) {
            // Gérer les exceptions et retourner une réponse d'erreur
            return response()->json(['error' => 'Utilisateur non connecté.'], 401);
        }
    }

    // --- MÉTHODES API CENTRALISÉES ---

    /**
     * MODULE RH : Historique des présences
     */
    public function getAttendances()
    {
        try {
            $user = $this->getApiUser();
            $attendances = \App\Models\Attendance::where('user_id', $user->id)
                ->orderBy('clock_in_time', 'desc')
                ->limit(30)
                ->get()
                ->map(function ($att) {
                    return [
                        'id' => $att->id,
                        'date' => $att->clock_in_time->format('d/m/Y'),
                        'clock_in' => $att->clock_in_time->format('H:i'),
                        'clock_out' => $att->clock_out_time ? $att->clock_out_time->format('H:i') : '--:--',
                        'status' => $att->clock_out_time ? 'Terminé' : 'En cours'
                    ];
                });
            return response()->json($attendances);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * MODULE RH : Pointer (In/Out)
     */
    public function clockInOut(Request $request)
    {
        try {
            $user = $this->getApiUser();
            $now = now();
            
            $attendance = \App\Models\Attendance::where('user_id', $user->id)
                ->whereNull('clock_out_time')
                ->first();

            if ($attendance) {
                $attendance->update(['clock_out_time' => $now]);
                return response()->json(['message' => 'Pointage de sortie réussi', 'type' => 'out']);
            }

            \App\Models\Attendance::create([
                'user_id' => $user->id,
                'clock_in_time' => $now,
                'clock_in_ip' => $request->ip(),
                'working_from' => 'mobile'
            ]);

            return response()->json(['message' => 'Pointage d\'entrée réussi', 'type' => 'in']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * MODULE FINANCE : Liste des dépenses
     */
    public function getExpenses()
    {
        try {
            $user = $this->getApiUser();
            $expenses = \App\Models\Expense::where('user_id', $user->id)
                ->orderBy('purchase_date', 'desc')
                ->get()
                ->map(function ($exp) {
                    return [
                        'id' => $exp->id,
                        'item' => $exp->item_name,
                        'price' => $exp->price,
                        'status' => $exp->status,
                        'date' => $exp->purchase_date ? $exp->purchase_date->format('d/m/Y') : 'N/A'
                    ];
                });
            return response()->json($expenses);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * MODULE TRAVAIL : Liste des tâches
     */
    public function getTasks()
    {
        try {
            $user = $this->getApiUser();
            $tasks = \App\Models\Task::whereHas('users', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->orderBy('due_date', 'asc')
                ->get()
                ->map(function ($task) {
                    return [
                        'id' => $task->id,
                        'heading' => $task->heading,
                        'status' => $task->status,
                        'due_date' => $task->due_date ? $task->due_date->format('d/m/Y') : 'N/A'
                    ];
                });
            return response()->json($tasks);
        } catch (\Exception $e) {
        }
    }

    /**
     * MODULE FINANCE : États Financiers (Recettes vs Dépenses par Projet)
     */
    public function getFinancialStates()
    {
        try {
            $user = $this->getApiUser();
            if (!$user) return response()->json(['error' => 'Unauthenticated'], 401);

            // On récupère les projets (diligences) du client
            $projects = Project::where('client_id', $user->id)
                ->with(['expenses', 'payments'])
                ->get();

            $financialData = $projects->map(function ($project) {
                // Somme des dépenses liées au projet
                $totalExpenses = $project->expenses->sum('amount');
                
                // Somme des paiements reçus pour ce projet
                $totalRecettes = $project->payments->sum('amount');
                
                $solde = $totalRecettes - $totalExpenses;
                
                // Détermination du statut pour l'affichage mobile
                $status = 'En cours';
                if ($project->status == 'finished' || $project->status == 'completed') {
                    $status = ($solde >= 0) ? 'Rentable' : 'Déficitaire';
                } elseif ($solde < 0 && $totalExpenses > ($project->project_budget ?? 0)) {
                    $status = 'Déficitaire';
                }

                return [
                    'id' => (string)$project->id,
                    'diligence' => $project->project_name,
                    'client' => auth()->user()->name,
                    'budget' => (float)$project->project_budget ?? 0,
                    'depenses' => (float)$totalExpenses,
                    'recettes' => (float)$totalRecettes,
                    'solde' => (float)$solde,
                    'status' => $status,
                    'progression' => ($project->completion_percent ?? 0) / 100,
                ];
            });

            // Calcul des totaux globaux pour les widgets du mobile
            $summary = [
                'totalBudget' => $financialData->sum('budget'),
                'totalDepenses' => $financialData->sum('depenses'),
                'totalRecettes' => $financialData->sum('recettes'),
                'totalSolde' => $financialData->sum('solde'),
            ];

            return response()->json([
                'data' => $financialData,
                'summary' => $summary
            ]);

        } catch (\Exception $e) {
            \Log::error('Erreur API Financial States: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur: ' . $e->getMessage()], 500);
        }
    }

    /**
     * MODULE FINANCE : Trésorerie (Comptes Bancaires & Caisses)
     */
    public function getTreasury()
    {
        try {
            $user = $this->getApiUser();
            if (!$user) return response()->json(['error' => 'Unauthenticated'], 401);

            // Récupérer tous les comptes bancaires de l'entreprise
            $accounts = BankAccount::where('company_id', $user->company_id)
                ->where('status', 1) // Uniquement les comptes actifs
                ->with('currency')
                ->get();

            $treasuryData = $accounts->map(function ($acc) {
                // Déterminer le type de paiement pour l'icône mobile
                $paymentType = 'Banque';
                if ($acc->type == 'cash') $paymentType = 'Espèces';
                
                // On peut aussi détecter si c'est du mobile money via le nom (ex: Orange Money, Wave)
                if (stripos($acc->bank_name, 'Orange') !== false || stripos($acc->bank_name, 'Wave') !== false || stripos($acc->bank_name, 'MTN') !== false) {
                    $paymentType = 'Mobile';
                }

                return [
                    'id' => (string)$acc->id,
                    'bankName' => $acc->bank_name ?? 'N/A',
                    'accountName' => $acc->account_name,
                    'accountType' => $acc->account_type ?? ($acc->type == 'cash' ? 'Caisse' : 'Courant'),
                    'paymentType' => $paymentType,
                    'currency' => $acc->currency->currency_code ?? 'XOF',
                    'balance' => (float)$acc->bank_balance,
                    'status' => 'Actif',
                ];
            });

            return response()->json([
                'data' => $treasuryData,
                'totalBalance' => $treasuryData->sum('balance')
            ]);

        } catch (\Exception $e) {
            \Log::error('Erreur API Trésorerie: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la récupération de la trésorerie'], 500);
        }
    }

    /**
     * MODULE MESSAGERIE : Liste des conversations
     */
    public function getConversations()
    {
        try {
            $user = $this->getApiUser();
            if (!$user) return response()->json(['error' => 'Unauthenticated'], 401);

            $userId = $user->id;
            
            // On utilise la logique existante du modèle UserChat
            $conversations = UserChat::with('fromUser', 'toUser')
                ->where(function ($query) use ($userId) {
                    $query->where('from', $userId)->orWhere('to', $userId);
                })
                ->whereIn('id', function ($query) {
                    $query->from('users_chat')
                        ->selectRaw('MAX(id)')
                        ->groupByRaw('LEAST(user_one, user_id), GREATEST(user_one, user_id)');
                })
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($chat) use ($userId) {
                    $otherUser = ($chat->from == $userId) ? $chat->toUser : $chat->fromUser;
                    return [
                        'id' => (string)$otherUser->id,
                        'name' => $otherUser->name,
                        'lastMessage' => $chat->message,
                        'time' => $chat->created_at->diffForHumans(),
                        'unread' => 0, // Idéalement, compter les messages non vus
                        'avatar' => $otherUser->image_url,
                        'online' => false, // Géré par une autre logique si nécessaire
                    ];
                });

            return response()->json($conversations);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * MODULE MESSAGERIE : Messages d'une conversation
     */
    public function getMessages($id)
    {
        try {
            $user = $this->getApiUser();
            if (!$user) return response()->json(['error' => 'Unauthenticated'], 401);

            $messages = UserChat::chatDetail($id, $user->id);

            $formattedMessages = $messages->map(function($msg) use ($user) {
                return [
                    'id' => (string)$msg->id,
                    'text' => $msg->message,
                    'senderId' => (string)$msg->from,
                    'time' => $msg->created_at->format('H:i'),
                    'isMine' => $msg->from == $user->id
                ];
            });

            return response()->json($formattedMessages);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * MODULE MESSAGERIE : Liste des contacts pour nouvelle discussion
     */
    public function getChatContacts()
    {
        try {
            $user = $this->getApiUser();
            if (!$user) return response()->json(['error' => 'Unauthenticated'], 401);

            $employees = User::allEmployees()->map(function($u) {
                return ['id' => (string)$u->id, 'name' => $u->name, 'avatar' => $u->image_url];
            });

            $clients = User::allClients()->map(function($u) {
                return ['id' => (string)$u->id, 'name' => $u->name, 'avatar' => $u->image_url];
            });

            return response()->json([
                'employee' => $employees,
                'client' => $clients
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * MODULE MESSAGERIE : Envoyer un message
     */
    public function sendMessage(Request $request)
    {
        try {
            $user = $this->getApiUser();
            if (!$user) return response()->json(['error' => 'Unauthenticated'], 401);

            $validated = $request->validate([
                'to' => 'required|exists:users,id',
                'message' => 'required|string'
            ]);

            $toUserId = $validated['to'];
            
            // Dans votre modèle UserChat, user_one est toujours le plus petit ID
            // et user_id est le plus grand ID pour grouper les conversations.
            $userOne = ($user->id < $toUserId) ? $user->id : $toUserId;
            $userId  = ($user->id > $toUserId) ? $user->id : $toUserId;

            $chat = UserChat::create([
                'user_one' => $userOne,
                'user_id' => $userId,
                'from' => $user->id,
                'to' => $toUserId,
                'message' => $validated['message'],
                'company_id' => $user->company_id,
                'message_seen' => 'no'
            ]);

            return response()->json([
                'id' => (string)$chat->id,
                'text' => $chat->message,
                'senderId' => (string)$chat->from,
                'time' => $chat->created_at->format('H:i'),
                'isMine' => true
            ], 201);
        } catch (\Exception $e) {
            \Log::error('Erreur envoi message API: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * MODULE CLIENTS : Liste des clients
     */
    public function getClients()
    {
        try {
            $user = $this->getApiUser();
            if (!$user) return response()->json(['error' => 'Unauthenticated'], 401);

            $clients = User::allClients($user->company_id)
                ->map(function($client) {
                    return [
                        'id' => (string)$client->id,
                        'name' => $client->name,
                        'company_name' => $client->clientDetails->company_name ?? 'Individuel',
                        'email' => $client->email,
                        'mobile' => $client->mobile,
                        'country' => $client->country->nicename ?? 'N/A',
                        'avatar' => $client->image_url
                    ];
                });

            return response()->json(['data' => $clients]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * MODULE CLIENTS : Détails d'un client
     */
    public function getClientDetails($id)
    {
        try {
            $user = $this->getApiUser();
            if (!$user) return response()->json(['error' => 'Unauthenticated'], 401);

            $client = User::with(['clientDetails', 'country'])->findOrFail($id);

            // On pourrait ajouter ici ses projets récents, factures, etc.
            $details = [
                'id' => (string)$client->id,
                'name' => $client->name,
                'company_name' => $client->clientDetails->company_name ?? 'N/A',
                'email' => $client->email,
                'mobile' => $client->mobile,
                'office_phone' => $client->clientDetails->office_phone ?? 'N/A',
                'website' => $client->clientDetails->website ?? 'N/A',
                'address' => $client->clientDetails->address ?? 'N/A',
                'city' => $client->clientDetails->city ?? 'N/A',
                'state' => $client->clientDetails->state ?? 'N/A',
                'postal_code' => $client->clientDetails->postal_code ?? 'N/A',
                'country' => $client->country->nicename ?? 'N/A',
                'avatar' => $client->image_url,
                'shipping_address' => $client->clientDetails->shipping_address ?? 'N/A',
            ];

            return response()->json($details);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Client non trouvé'], 404);
        }
    }

    /**
     * MODULE FINANCE : Produits & Services
     */
    public function services()
    {
        try {
            $user = $this->getApiUser();
            if (!$user) return response()->json(['error' => 'Unauthenticated'], 401);

            $products = Product::where('company_id', $user->company_id)
                ->with('category')
                ->get()
                ->map(function($product) {
                    return [
                        'id' => (string)$product->id,
                        'name' => $product->name,
                        'category' => $product->category->category_name ?? 'Général',
                        'price' => number_format($product->price, 0, ',', ' ') . ' FCFA',
                        'image' => $product->image_url,
                        'description' => $product->description
                    ];
                });

            return response()->json($products);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * MODULE RH : Permissions (Demandes de congés)
     */
    public function getPermissions()
    {
        try {
            $user = $this->getApiUser();
            if (!$user) return response()->json(['error' => 'Unauthenticated'], 401);

            $leaves = Leave::with('type')
                ->where('user_id', $user->id)
                ->orderBy('leave_date', 'desc')
                ->get()
                ->map(function($leave) {
                    return [
                        'id' => (string)$leave->id,
                        'type' => $leave->type->type_name,
                        'date' => $leave->leave_date->format('d/m/Y'),
                        'duration' => $leave->duration,
                        'reason' => $leave->reason,
                        'status' => $leave->status,
                        'color' => $leave->status == 'approved' ? '#2ecc71' : ($leave->status == 'pending' ? '#f39c12' : '#e74c3c')
                    ];
                });

            return response()->json($leaves);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * MODULE TRAVAIL : Contrats
     */
    public function getContracts()
    {
        try {
            $user = $this->getApiUser();
            if (!$user) return response()->json(['error' => 'Unauthenticated'], 401);

            // Pour un client, on filtre par client_id. Pour un employé, on voit tout l'entreprise ou rien selon les droits (ici on simplifie).
            $query = Contract::with(['contractType', 'client', 'currency'])
                ->where('company_id', $user->company_id);
            
            if ($user->hasRole('client')) {
                $query->where('client_id', $user->id);
            }

            $contracts = $query->orderBy('start_date', 'desc')
                ->get()
                ->map(function($contract) {
                    return [
                        'id' => (string)$contract->id,
                        'subject' => $contract->subject,
                        'amount' => number_format($contract->amount, 0, ',', ' ') . ' ' . ($contract->currency->currency_code ?? 'XOF'),
                        'start_date' => $contract->start_date->format('d/m/Y'),
                        'end_date' => $contract->end_date ? $contract->end_date->format('d/m/Y') : 'Indéfini',
                        'type' => $contract->contractType->name ?? 'N/A',
                        'status' => $contract->end_date && $contract->end_date->isPast() ? 'Expiré' : 'Actif'
                    ];
                });

            return response()->json($contracts);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * MODULE TRAVAIL : Base de Connaissances
     */
    public function getKnowledgeBase()
    {
        try {
            $user = $this->getApiUser();
            if (!$user) return response()->json(['error' => 'Unauthenticated'], 401);

            $articles = KnowledgeBase::with('category')
                ->where('company_id', $user->company_id)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($article) {
                    return [
                        'id' => (string)$article->id,
                        'title' => $article->heading,
                        'category' => $article->category->category_name ?? 'Général',
                        'content' => strip_tags($article->description),
                        'date' => $article->created_at->format('d/m/Y')
                    ];
                });

            return response()->json($articles);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * CHANGEMENT DE MOT DE PASSE
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        try {
            $user = $this->getApiUser();
            if (!$user) return response()->json(['error' => 'Unauthenticated'], 401);

            if (!\Hash::check($request->current_password, $user->password)) {
                return response()->json(['error' => 'L\'ancien mot de passe est incorrect.'], 422);
            }

            $user->password = \Hash::make($request->new_password);
            $user->save();

            return response()->json(['message' => 'Mot de passe mis à jour avec succès !']);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
