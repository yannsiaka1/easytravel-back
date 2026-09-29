<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Connexion;
use App\Models\Paiement;
use App\Models\Reservation;
use App\Models\Ticket;
use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AgentController extends Controller
{
    public function statistiquesGlobales()
    {
        return response()->json([
            'total_reservations' => Reservation::count(),
            'reservations_payees' => Reservation::where('statut_paiement', 'paye')->count(),
            'reservations_en_attente' => Reservation::where('statut_paiement', 'en_attente')->count(),
            'total_paiements' => Paiement::where('statut', 'valide')->sum('montant'),
            'nombre_utilisateurs' => Utilisateur::count(),
        ]);
    }

    /** Agrégation faite en PHP pour rester compatible SQLite, MySQL et PostgreSQL. */
    public function graphiquesReservationPaiement()
    {
        $reservations = Reservation::orderBy('created_at')->get(['created_at'])
            ->groupBy(fn ($item) => $item->created_at->format('Y-m'))
            ->map(fn ($items, $month) => ['mois' => $month, 'total' => $items->count()])
            ->values();

        $paiements = Paiement::orderBy('created_at')->get(['created_at', 'montant'])
            ->groupBy(fn ($item) => $item->created_at->format('Y-m'))
            ->map(fn ($items, $month) => ['mois' => $month, 'total' => (float) $items->sum('montant')])
            ->values();

        return response()->json([
            'reservations_par_mois' => $reservations,
            'paiements_par_mois' => $paiements,
        ]);
    }

    public function repartitionParClasse()
    {
        return response()->json(
            Reservation::selectRaw('classe, COUNT(*) as total')->groupBy('classe')->get()
        );
    }

    public function reservationsParStatut(Request $request)
    {
        $request->validate(['statut' => ['nullable', Rule::in(['en_attente', 'paye'])]]);

        $query = Reservation::with(['voyage', 'utilisateur:id,nom,prenom']);
        if ($request->filled('statut')) {
            $query->where('statut_paiement', $request->string('statut'));
        }

        return response()->json($query->latest()->get());
    }

    public function tousLesUtilisateurs()
    {
        return response()->json([
            'success' => true,
            'utilisateurs' => Utilisateur::with('connexion')->latest()->get(),
        ]);
    }

    public function utilisateurParId(Utilisateur $utilisateur)
    {
        return response()->json(['utilisateur' => $utilisateur->load('connexion')]);
    }

    public function creerUtilisateur(Request $request)
    {
        $data = $this->validateUtilisateur($request);

        $utilisateur = DB::transaction(function () use ($data) {
            $utilisateur = Utilisateur::create([
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'carte_identite' => $data['carte_identite'],
                'telephone' => $data['telephone'],
                'role' => $data['role'],
            ]);

            Connexion::create([
                'utilisateur_id' => $utilisateur->id,
                'email' => $data['email'],
                'mot_de_passe' => Hash::make($data['password']),
            ]);

            return $utilisateur;
        });

        return response()->json([
            'message' => 'Utilisateur créé avec succès.',
            'utilisateur' => $utilisateur->load('connexion'),
        ], 201);
    }

    public function modifierUtilisateur(Request $request, Utilisateur $utilisateur)
    {
        $data = $this->validateUtilisateur($request, $utilisateur, true);

        if (($data['role'] ?? null) === 'client' && $utilisateur->role === 'agent' && $utilisateur->voyages()->exists()) {
            return response()->json([
                'message' => 'Cet agent gère encore des voyages et ne peut pas être converti en client.',
            ], 409);
        }

        DB::transaction(function () use ($data, $utilisateur) {
            $utilisateur->update(array_filter([
                'nom' => $data['nom'] ?? null,
                'prenom' => $data['prenom'] ?? null,
                'carte_identite' => $data['carte_identite'] ?? null,
                'telephone' => $data['telephone'] ?? null,
                'role' => $data['role'] ?? null,
            ], fn ($value) => $value !== null));

            $connexion = $utilisateur->connexion;
            if ($connexion) {
                if (isset($data['email'])) {
                    $connexion->email = $data['email'];
                }
                if (! empty($data['password'])) {
                    $connexion->mot_de_passe = Hash::make($data['password']);
                }
                $connexion->save();
            }
        });

        return response()->json([
            'message' => 'Utilisateur modifié avec succès.',
            'utilisateur' => $utilisateur->fresh('connexion'),
        ]);
    }

    public function supprimerUtilisateur(Utilisateur $utilisateur)
    {
        if ($utilisateur->id === Auth::id()) {
            return response()->json(['message' => 'Vous ne pouvez pas supprimer votre propre compte.'], 422);
        }

        if ($utilisateur->voyages()->exists()) {
            return response()->json([
                'message' => 'Cet agent gère encore des voyages et ne peut pas être supprimé.',
            ], 409);
        }

        if ($utilisateur->reservations()->exists()) {
            return response()->json([
                'message' => 'Cet utilisateur possède un historique de réservation et ne peut pas être supprimé.',
            ], 409);
        }

        $utilisateur->delete();

        return response()->json(['message' => 'Utilisateur supprimé.']);
    }

    public function tousLesTickets()
    {
        return response()->json(Ticket::with('reservation.voyage')->latest()->get());
    }

    public function tousLesPaiements()
    {
        return response()->json(Paiement::with('reservation.voyage')->latest()->get());
    }

    public function profilAgent()
    {
        return response()->json(Auth::user()->load('connexion'));
    }

    public function modifierProfilAgent(Request $request)
    {
        $user = Auth::user();
        $connexion = $user->connexion;

        $data = $request->validate([
            'nom' => ['sometimes', 'string', 'max:255'],
            'prenom' => ['sometimes', 'string', 'max:255'],
            'telephone' => ['sometimes', 'string', 'max:30'],
            'email' => ['sometimes', 'email', Rule::unique('connexions', 'email')->ignore($connexion?->id)],
        ]);

        $user->update(collect($data)->only(['nom', 'prenom', 'telephone'])->all());
        if ($connexion && isset($data['email'])) {
            $connexion->update(['email' => $data['email']]);
        }

        return response()->json(['message' => 'Profil mis à jour.', 'user' => $user->fresh('connexion')]);
    }

    private function validateUtilisateur(Request $request, ?Utilisateur $utilisateur = null, bool $partial = false): array
    {
        $required = $partial ? 'sometimes' : 'required';
        $connexionId = $utilisateur?->connexion?->id;

        return $request->validate([
            'nom' => [$required, 'string', 'max:255'],
            'prenom' => [$required, 'string', 'max:255'],
            'carte_identite' => [$required, 'string', 'max:100', Rule::unique('utilisateurs', 'carte_identite')->ignore($utilisateur?->id)],
            'telephone' => [$required, 'string', 'max:30'],
            'email' => [$required, 'email', 'max:255', Rule::unique('connexions', 'email')->ignore($connexionId)],
            'password' => [$partial ? 'nullable' : 'required', 'string', 'min:6', 'max:255'],
            'role' => [$required, Rule::in(['client', 'agent'])],
        ]);
    }
}
