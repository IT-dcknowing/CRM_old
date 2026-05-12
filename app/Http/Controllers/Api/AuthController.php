<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // On vérifie que les infos sont là
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required',
        ]);

        // On cherche l'utilisateur par son email
        $user = User::where('email', $request->email)->first();

        // Si l'utilisateur n'existe pas ou le mot de passe est faux
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Les identifiants sont incorrects.'
            ], 401);
        }

        // On crée le "Token" (la clé magique)
        $token = $user->createToken($request->device_name)->plainTextToken;

        // On renvoie le token et les infos au format JSON pour le mobile
        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'userName' => $user->name // Pour afficher son nom sur l'accueil
        ]);
    }
}
