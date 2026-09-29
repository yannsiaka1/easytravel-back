<?php

namespace Database\Seeders;

use App\Models\Reservation;
use App\Models\Ticket;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    public function run(): void
    {
        $reservation = Reservation::where('statut_paiement', 'paye')->first();
        if (! $reservation || Ticket::where('reservation_id', $reservation->id)->exists()) {
            return;
        }

        Ticket::create([
            'reservation_id' => $reservation->id,
            'code_unique' => 'TCK-SEED-001',
            'prix' => $reservation->voyage->prix * $reservation->nb_places,
            'heure_depart' => $reservation->voyage->date_depart->format('H:i:s'),
            'details_bus' => strtoupper($reservation->classe) . ' - ' . $reservation->ville_depart . ' → ' . $reservation->destination,
        ]);
    }
}
