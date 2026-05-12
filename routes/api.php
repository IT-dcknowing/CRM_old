<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController; 

// Route pour vider le cache (Crucial pour cPanel)
Route::get('/clear-cache', function() {
    try {
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('route:clear');
        return response()->json(['message' => 'Cache vidé avec succès !']);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Cache déjà propre ou erreur ignorée', 'details' => $e->getMessage()]);
    }
});

// --- ROUTES PUBLIQUES ---
Route::post('/login', [AuthController::class, 'login']);

// --- ROUTES API MOBILE (Centralisées dans AuthController pour cPanel) ---
Route::get('/dashboard', [AuthController::class, 'dashboard']);
Route::get('/dashboard/honoraires/{id}', [AuthController::class, 'honoraires']);
Route::get('/user-infos', [AuthController::class, 'userInfos']);
Route::get('/notifications', [AuthController::class, 'indexNotification']);
Route::get('/notifications/unread-count', [AuthController::class, 'unreadCount']);
Route::get('/clients', [AuthController::class, 'getClients']);
Route::get('/clients/{id}', [AuthController::class, 'getClientDetails']);

// MODULE TRAVAIL
Route::get('/getAlldiligences', [AuthController::class, 'getAlldiligences']);
Route::get('/tasks', [AuthController::class, 'getTasks']);
Route::get('/contracts', [AuthController::class, 'getContracts']);
Route::get('/knowledge-base', [AuthController::class, 'getKnowledgeBase']);

// MODULE RH
Route::get('/attendances', [AuthController::class, 'getAttendances']);
Route::post('/attendances/clock-in-out', [AuthController::class, 'clockInOut']);
Route::get('/permissions', [AuthController::class, 'getPermissions']);

// MODULE FINANCE
Route::get('/expenses', [AuthController::class, 'getExpenses']);
Route::get('/invoices', [AuthController::class, 'getInvoices']);
Route::get('/financial-states', [AuthController::class, 'getFinancialStates']); // NOUVEAU
Route::get('/treasury', [AuthController::class, 'getTreasury']); // NOUVEAU

// MODULE TAX & SOCIAL
Route::get('/situations-fiscal', [AuthController::class, 'getFiscalSituation']);
Route::get('/situations-social', [AuthController::class, 'getSocialSituation']);

// SERVICES & REQUÊTES
Route::get('/services', [AuthController::class, 'services']);
Route::get('/requests', [AuthController::class, 'indexRequest']);
Route::post('/requests', [AuthController::class, 'storeRequest']);

// MESSAGERIE
Route::get('/conversations', [AuthController::class, 'getConversations']);
Route::get('/messages/{id}', [AuthController::class, 'getMessages']);
Route::post('/messages', [AuthController::class, 'sendMessage']); // NOUVEAU
Route::get('/chat-contacts', [AuthController::class, 'getChatContacts']);

// DOCUMENTS
Route::get('/documents', [AuthController::class, 'documents']);
Route::get('/documents/{id}/download', [AuthController::class, 'documentsDownload']);

// PROFIL
Route::post('/change-password', [AuthController::class, 'changePassword']);


