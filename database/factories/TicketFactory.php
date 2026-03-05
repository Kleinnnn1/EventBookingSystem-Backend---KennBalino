<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'type'     => fake()->randomElement(['VIP', 'Standard', 'Early Bird']),
            'price'    => fake()->randomFloat(2, 10, 500),
            'quantity' => fake()->numberBetween(10, 100),
        ];
    }
}
