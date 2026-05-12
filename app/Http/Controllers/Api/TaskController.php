<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomerRequest;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // On récupère les demandes créées par l'utilisateur ou qui lui sont liées
        $requests = CustomerRequest::where('created_by', $user->id)
            ->orWhere('client_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($requests);
    }

    public function storeRequest(Request $request)
    {
        $user = auth()->user();

        $customerRequest = new CustomerRequest();
        $customerRequest->name = $request->name;
        $customerRequest->type_request = $request->type_request ?? 'demande';
        $customerRequest->request_text = $request->request_text;
        $customerRequest->client_id = $user->id; // On lie au client actuel
        $customerRequest->created_by = $user->id;
        $customerRequest->status = 'en attente';
        $customerRequest->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Demande enregistrée avec succès',
            'data' => $customerRequest
        ]);
    }
}
