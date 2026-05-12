<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmployeeDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class DocumentController extends Controller
{
    public function index()
    {
        // On récupère les documents de l'utilisateur connecté
        $user = auth()->user();
        
        $documents = EmployeeDocument::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $documents
        ]);
    }

    public function download($id)
    {
        $user = auth()->user();
        $file = EmployeeDocument::where('user_id', $user->id)->findOrFail($id);

        $path = storage_path('app/' . EmployeeDocument::FILE_PATH . '/' . $user->id . '/' . $file->hashname);
        
        if (!file_exists($path)) {
            return response()->json(['message' => 'Fichier introuvable sur le serveur.'], 404);
        }

        return Response::download($path, $file->filename);
    }
}
