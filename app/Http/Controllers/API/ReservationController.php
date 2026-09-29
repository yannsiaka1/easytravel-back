<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Voyage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    /** Crée ou actualise une réservation encore en attente de paiement. */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'voyage_id' => ['required', 'integer', 'exists:voyages,id'],
            'nb_places' => ['required', 'integer', 'min:1'],
        ]);

        $voyage = Voyage::findOrFail($validated['voyage_id']);

        if ($voyage->date_depart->isPast()) {
            return response()->json(['message' => 'Ce voyage est déjà passé.'], 422);
        }

        if ($voyage->nombre_places < $validated['nb_places']) {
            return response()->json(['message' => 'Le nombre de places demandé n’est plus disponible.'], 409);
        }

        $reservation = Reservation::firstOrNew([
            'utilisateur_id' => Auth::id(),
            'voyage_id' => $voyage->id,
            'statut_paiement' => 'en_attente',
        ]);
        $created = ! $reservation->exists;

        $reservation->fill([
            'date_voyage' => $voyage->date_depart->toDateString(),
            'ville_depart' => $voyage->ville_depart,
            'destination' => $voyage->ville_arrivee,
            'classe' => $voyage->classe,
            'nb_places' => $validated['nb_places'],
            'date_reservation' => now(),
        ]);
        $reservation->save();

        return response()->json([
            'message' => $created
                ? 'Réservation enregistrée en attente de paiement.'
                : 'Réservation en attente mise à jour.',
            'reservation' => $reservation->load('voyage'),
        ], $created ? 201 : 200);
    }

    /** Retourne l'historique du client avec voyage, paiement et ticket. */
    public function index()
    {
        $reservations = Reservation::with(['voyage', 'paiement', 'ticket'])
            ->where('utilisateur_id', Auth::id())
            ->latest('date_reservation')
            ->get();

        return response()->json($reservations);
    }

    /** Retourne une réservation précise appartenant au client connecté. */
    public function show(Reservation $reservation)
    {
        abort_unless($reservation->utilisateur_id === Auth::id(), 403, 'Action non autorisée.');

        return response()->json($reservation->load(['voyage', 'paiement', 'ticket']));
    }

    /** Recherche des voyages qui correspondent aux critères saisis. */
    public function disponibles(Request $request)
    {
        $validated = $request->validate([
            'date_voyage' => ['required', 'date', 'after_or_equal:today'],
            'ville_depart' => ['required', 'string', 'max:255'],
            'ville_arrivee' => ['required', 'string', 'max:255'],
            'classe' => ['required', 'in:vip,classique'],
            'nb_places' => ['required', 'integer', 'min:1'],
        ]);

        $depart = mb_strtolower(trim($validated['ville_depart']));
        $arrivee = mb_strtolower(trim($validated['ville_arrivee']));

        $voyages = Voyage::with('agent:id,nom,prenom')
            ->whereDate('date_depart', $validated['date_voyage'])
            ->whereRaw('LOWER(ville_depart) = ?', [$depart])
            ->whereRaw('LOWER(ville_arrivee) = ?', [$arrivee])
            ->where('classe', $validated['classe'])
            ->where('nombre_places', '>=', $validated['nb_places'])
            ->orderBy('date_depart')
            ->get()
            ->each(fn (Voyage $voyage) => $voyage->setAttribute('places_restantes', $voyage->nombre_places));

        return response()->json([
            'date' => $validated['date_voyage'],
            'voyages' => $voyages,
        ]);
    }
}
