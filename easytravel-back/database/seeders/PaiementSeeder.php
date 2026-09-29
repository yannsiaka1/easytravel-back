<?php

namespace Database\Seeders;

use App\Models\Paiement;
use App\Models\Reservation;
use Illuminate\Database\Seeder;

class PaiementSeeder extends Seeder
{
    public function run(): void
    {
        // Les paiements sont créés par le parcours applicatif. Aucun paiement
        // fictif n'est injecté afin de conserver une base de démonstration cohérente.
        if (Reservation::count() === 0) {
            return;
        }
    }
}
