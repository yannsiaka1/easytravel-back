<?php

namespace Database\Factories;

use App\Models\Bus;
use App\Models\HorairesBus;
use Illuminate\Database\Eloquent\Factories\Factory;

class HorairesBusFactory extends Factory
{
    protected $model = HorairesBus::class;

    public function definition(): array
    {
        $departure = fake()->dateTimeBetween('+1 day', '+2 weeks');
        $arrival = (clone $departure)->modify('+4 hours');

        return [
            'bus_id' => Bus::factory(),
            'heure_depart' => $departure,
            'heure_arrivee' => $arrival,
            'date_disponible' => $departure->format('Y-m-d'),
            'nb_places_totales' => 40,
            'nb_places_restantes' => 40,
        ];
    }
}
