<?php

namespace Database\Factories;

use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition(): array
    {
        return [
            'reservation_id' => \App\Models\Reservation::factory(),
            'code_unique' => strtoupper($this->faker->bothify('TKT###??')),
            'prix' => $this->faker->randomFloat(2, 10, 100),
            'heure_depart' => $this->faker->time(),
            'details_bus' => $this->faker->sentence(),
        ];
    }
}
