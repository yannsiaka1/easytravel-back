<?php

namespace Database\Factories;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition(): array
    {
        return [
            'utilisateur_id' => \App\Models\Utilisateur::factory(),
            'type' => 'info',
            'contenu' => $this->faker->sentence(),
            'lu' => false,
            'date_envoi' => now(),
        ];
    }
}
