<?php

namespace Database\Factories;

use App\Models\Reservation;
use App\Models\Utilisateur;
use App\Models\Voyage;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    public function definition(): array
    {
        return [
            'utilisateur_id' => Utilisateur::factory(),
            'voyage_id' => Voyage::factory(),
            'date_voyage' => fake()->dateTimeBetween('+1 day', '+1 month')->format('Y-m-d'),
            'ville_depart' => fake()->city(),
            'destination' => fake()->city(),
            'classe' => fake()->randomElement(['vip', 'classique']),
            'nb_places' => fake()->numberBetween(1, 5),
            'statut_paiement' => 'en_attente',
            'date_reservation' => now(),
        ];
    }
}
