<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {
        // On récupère tous les utilisateurs qui sont des clients
        // Dans votre système, ils semblent être identifiés par le rôle 'client'
        // ou simplement être dans la liste des clients.
        
        $clients = User::allClients(); 

        return response()->json([
            'status' => 'success',
            'data' => $clients
        ]);
    }
}
