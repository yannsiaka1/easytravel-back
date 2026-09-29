<?php

namespace Database\Seeders;

use App\Models\HorairesBus;
use Illuminate\Database\Seeder;

class HorairesBusSeeder extends Seeder
{
    public function run(): void
    {
        $depart = now()->addDays(2)->setTime(8, 0);

        HorairesBus::create([
            'bus_id' => 1,
            'heure_depart' => $depart,
            'heure_arrivee' => $depart->copy()->addHours(4),
            'date_disponible' => $depart->toDateString(),
            'nb_places_totales' => 50,
            'nb_places_restantes' => 45,
        ]);
    }
}
