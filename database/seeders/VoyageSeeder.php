<?php

namespace Database\Seeders;

use App\Models\Voyage;
use Illuminate\Database\Seeder;

class VoyageSeeder extends Seeder
{
    public function run(): void
    {
        Voyage::insert([
            [
                'ville_depart' => 'Dakar',
                'ville_arrivee' => 'Saint-Louis',
                'date_depart' => now()->addDays(2)->setTime(8, 0),
                'prix' => 7500,
                'nombre_places' => 20,
                'classe' => 'vip',
                'agent_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ville_depart' => 'Thiès',
                'ville_arrivee' => 'Kaolack',
                'date_depart' => now()->addDays(3)->setTime(9, 30),
                'prix' => 5000,
                'nombre_places' => 15,
                'classe' => 'classique',
                'agent_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ville_depart' => 'Daloa',
                'ville_arrivee' => 'Man',
                'date_depart' => now()->addDays(4)->setTime(14, 15),
                'prix' => 9500,
                'nombre_places' => 18,
                'classe' => 'vip',
                'agent_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
