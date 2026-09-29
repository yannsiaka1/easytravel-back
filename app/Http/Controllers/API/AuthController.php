<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Connexion;
use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Inscription publique. Un compte créé depuis le site est toujours un client.
     * Les comptes agents sont créés depuis l'espace agent.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'carte_identite' => ['required', 'string', 'max:100', 'unique:utilisateurs,carte_identite'],
            'telephone' => ['required', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:255', 'unique:connexions,email'],
            'password' => ['required', 'string', 'min:6', 'max:255'],
        ]);

        $utilisateur = DB::transaction(function () use ($validated) {
            $utilisateur = Utilisateur::create([
                'nom' => $validated['nom'],
                'prenom' => $validated['prenom'],
                'carte_identite' => $validated['carte_identite'],
                'telephone' => $validated['telephone'],
                'role' => 'client',
            ]);

            Connexion::create([
                'utilisateur_id' => $utilisateur->id,
                'email' => $validated['email'],
                'mot_de_passe' => Hash::make($validated['password']),
            ]);

            return $utilisateur;
        });

        return response()->json([
            'message' => 'Utilisateur enregistré avec succès.',
            'user' => $utilisateur->load('connexion'),
        ], 201);
    }

    /** Connexion via l'email stocké dans la table connexions. */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $connexion = Connexion::with('utilisateur')
            ->where('email', $validated['email'])
            ->first();

        if (! $connexion || ! Hash::check($validated['password'], $connexion->mot_de_passe)) {
            throw ValidationException::withMessages([
                'email' => ['Les identifiants sont incorrects.'],
            ]);
        }

        $utilisateur = $connexion->utilisateur;
        $token = $utilisateur->createToken('easytravel-web')->plainTextToken;

        return response()->json([
            'message' => 'Connexion réussie.',
            'user' => $utilisateur->load('connexion'),
            'token' => $token,
        ]);
    }


    /** Retourne le profil associé au token courant. */
    public function user(Request $request)
    {
        return response()->json($request->user()->load('connexion'));
    }

    /** Supprime uniquement le token utilisé pour la requête courante. */
    public function logout(Request $request)
    {
        $request->user()?->currentAccessToken()?->delete();

        return response()->json(['message' => 'Déconnexion réussie.']);
    }
}
