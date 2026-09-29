<?php

namespace Database\Factories;

use App\Models\Paiement;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaiementFactory extends Factory
{
    protected $model = Paiement::class;

    public function definition(): array
    {
        return [
            'reservation_id' => Reservation::factory(),
            'ticket_id' => null,
            'montant' => fake()->randomFloat(2, 3000, 30000),
            'mode_paiement' => fake()->randomElement(['carte', 'mobile_money']),
            'statut' => 'valide',
            'reference' => fake()->unique()->bothify('ET-########-???'),
            'date_paiement' => now(),
        ];
    }
}
