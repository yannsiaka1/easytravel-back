<?php

namespace App\Http\Controllers;

use App\Models\Paiement;
use App\Models\Reservation;
use App\Models\Ticket;
use App\Models\Voyage;
use App\Notifications\TicketEnvoyeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaiementController extends Controller
{
    /**
     * Simule un paiement. Le montant est recalculé côté serveur afin de ne pas
     * faire confiance au montant envoyé par le navigateur.
     */
    public function effectuerPaiement(Request $request)
    {
        $validated = $request->validate([
            'reservation_id' => ['required', 'integer', 'exists:reservations,id'],
            'mode_paiement' => ['required', 'string', 'max:50'],
            'reference' => ['required', 'string', 'max:100', 'unique:paiements,reference'],
        ]);

        $reservation = Reservation::with(['voyage', 'utilisateur.connexion'])
            ->findOrFail($validated['reservation_id']);

        if ($reservation->utilisateur_id !== Auth::id()) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        if ($reservation->statut_paiement === 'paye') {
            return response()->json(['message' => 'Paiement déjà effectué.'], 409);
        }

        try {
            $result = DB::transaction(function () use ($reservation, $validated) {
                $voyage = Voyage::whereKey($reservation->voyage_id)->lockForUpdate()->firstOrFail();
                $reservation->refresh();

                if ($reservation->statut_paiement === 'paye') {
                    abort(409, 'Paiement déjà effectué.');
                }

                if ($voyage->nombre_places < $reservation->nb_places) {
                    abort(409, 'Le nombre de places demandé n’est plus disponible.');
                }

                $montant = (float) $voyage->prix * $reservation->nb_places;

                $ticket = Ticket::create([
                    'reservation_id' => $reservation->id,
                    'code_unique' => 'TCK-' . strtoupper(Str::random(10)),
                    'prix' => $montant,
                    'heure_depart' => $voyage->date_depart->format('H:i:s'),
                    'details_bus' => strtoupper($reservation->classe) . ' - ' . $voyage->ville_depart . ' → ' . $voyage->ville_arrivee,
                ]);

                $paiement = Paiement::create([
                    'reservation_id' => $reservation->id,
                    'ticket_id' => $ticket->id,
                    'mode_paiement' => $validated['mode_paiement'],
                    'montant' => $montant,
                    'reference' => $validated['reference'],
                    'statut' => 'valide',
                    'date_paiement' => now(),
                ]);

                $reservation->update(['statut_paiement' => 'paye']);
                $voyage->decrement('nombre_places', $reservation->nb_places);

                return compact('ticket', 'paiement');
            });

            // L'email ne doit pas annuler un paiement déjà validé.
            try {
                $reservation->utilisateur?->notify(new TicketEnvoyeNotification($result['ticket']));
            } catch (\Throwable $exception) {
                Log::warning('Ticket créé mais notification email non envoyée.', [
                    'reservation_id' => $reservation->id,
                    'message' => $exception->getMessage(),
                ]);
            }

            return response()->json([
                'message' => 'Paiement effectué avec succès.',
                'paiement' => $result['paiement'],
                'ticket' => $result['ticket'],
            ], 201);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $exception) {
            return response()->json(['message' => $exception->getMessage()], $exception->getStatusCode());
        } catch (\Throwable $exception) {
            Log::error('Erreur lors du paiement.', ['message' => $exception->getMessage()]);
            return response()->json(['message' => 'Erreur lors du traitement du paiement.'], 500);
        }
    }

    /** Dernière réservation du client qui attend encore un paiement. */
    public function reservationAPayer()
    {
        $reservation = Reservation::with('voyage')
            ->where('utilisateur_id', Auth::id())
            ->where('statut_paiement', 'en_attente')
            ->latest()
            ->first();

        if (! $reservation) {
            return response()->json(['message' => 'Aucune réservation en attente.'], 404);
        }

        return response()->json($reservation);
    }
}
