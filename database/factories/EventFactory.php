<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title'       => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'date'        => fake()->dateTimeBetween('now', '+1 year'),
            'location'    => fake()->city(),
            'created_by'  => User::factory(),
        ];
    }
}
