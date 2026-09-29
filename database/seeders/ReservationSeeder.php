<?php

namespace Database\Seeders;

use App\Models\Reservation;
use App\Models\Voyage;
use Illuminate\Database\Seeder;

class ReservationSeeder extends Seeder
{
    public function run(): void
    {
        $voyage = Voyage::first();
        if (! $voyage) {
            return;
        }

        Reservation::create([
            'utilisateur_id' => 1,
            'voyage_id' => $voyage->id,
            'date_voyage' => $voyage->date_depart->toDateString(),
            'ville_depart' => $voyage->ville_depart,
            'destination' => $voyage->ville_arrivee,
            'classe' => $voyage->classe,
            'nb_places' => 1,
            'statut_paiement' => 'en_attente',
            'date_reservation' => now(),
        ]);
    }
}
