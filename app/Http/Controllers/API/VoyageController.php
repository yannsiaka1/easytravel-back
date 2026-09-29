<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Voyage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoyageController extends Controller
{
    /** Retourne les voyages à venir. Un agent ne voit que ceux qu'il gère. */
    public function index(Request $request)
    {
        $query = Voyage::with('agent:id,nom,prenom')
            ->where('date_depart', '>=', now());

        if ($request->user()?->role === 'agent') {
            $query->where('agent_id', $request->user()->id);
        }

        $voyages = $query
            ->orderBy('date_depart')
            ->get()
            ->map(fn (Voyage $voyage) => $this->withRemainingSeats($voyage));

        return response()->json(['voyages' => $voyages]);
    }

    /** Crée un voyage pour l'agent connecté. */
    public function store(Request $request)
    {
        $validated = $this->validateVoyage($request);

        $voyage = Voyage::create([
            ...$validated,
            'agent_id' => Auth::id(),
        ]);

        return response()->json([
            'message' => 'Voyage créé avec succès.',
            'voyage' => $this->withRemainingSeats($voyage->load('agent:id,nom,prenom')),
        ], 201);
    }

    /** Modifie uniquement un voyage appartenant à l'agent connecté. */
    public function update(Request $request, Voyage $voyage)
    {
        $this->ensureOwner($voyage);
        $voyage->update($this->validateVoyage($request, true));

        return response()->json([
            'message' => 'Voyage mis à jour avec succès.',
            'voyage' => $this->withRemainingSeats($voyage->fresh('agent:id,nom,prenom')),
        ]);
    }

    /** Supprime uniquement un voyage appartenant à l'agent connecté. */
    public function destroy(Voyage $voyage)
    {
        $this->ensureOwner($voyage);

        if ($voyage->reservations()->exists()) {
            return response()->json([
                'message' => 'Ce voyage possède déjà des réservations et ne peut plus être supprimé.',
            ], 409);
        }

        $voyage->delete();

        return response()->json(['message' => 'Voyage supprimé.']);
    }

    private function validateVoyage(Request $request, bool $partial = false): array
    {
        $required = $partial ? 'sometimes' : 'required';

        return $request->validate([
            'ville_depart' => [$required, 'string', 'max:255'],
            'ville_arrivee' => [$required, 'string', 'max:255'],
            'date_depart' => [$required, 'date', 'after_or_equal:now'],
            'prix' => [$required, 'numeric', 'min:0'],
            'nombre_places' => [$required, 'integer', 'min:1'],
            'classe' => [$required, 'in:classique,vip'],
        ]);
    }

    private function ensureOwner(Voyage $voyage): void
    {
        abort_unless($voyage->agent_id === Auth::id(), 403, 'Action non autorisée.');
    }

    private function withRemainingSeats(Voyage $voyage): Voyage
    {
        // nombre_places stocke les places encore disponibles après paiement.
        $voyage->setAttribute('places_restantes', max(0, $voyage->nombre_places));

        return $voyage;
    }
}
