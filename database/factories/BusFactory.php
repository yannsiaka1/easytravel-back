<?php

namespace Database\Factories;

use App\Models\Bus;
use Illuminate\Database\Eloquent\Factories\Factory;

class BusFactory extends Factory
{
    protected $model = Bus::class;

    public function definition(): array
    {
        return [
            'numero_bus' => $this->faker->unique()->numerify('BUS###'),
            'capacite' => $this->faker->numberBetween(30, 60),
            'chauffeur' => $this->faker->name(),
        ];
    }
}
