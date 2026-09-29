<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bus;
use Carbon\Carbon;

class BusSeeder extends Seeder
{
    public function run(): void
    {
        Bus::insert([
            [
                'numero_bus' => 'DKR-001',
                'capacite'   => 50,
                'chauffeur'  => 'Mamadou Ndiaye',
                'type'       => 'VIP',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'numero_bus' => 'DKR-002',
                'capacite'   => 40,
                'chauffeur'  => 'Awa Diop',
                'type'       => 'Classique',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
