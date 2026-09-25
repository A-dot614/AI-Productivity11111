<?php

namespace Database\Factories;

use App\Models\ProductivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductivityLog>
 */
class ProductivityLogFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'log_date' => fake()->dateTimeBetween('-30 days', 'today')->format('Y-m-d'),
            'focus_minutes' => fake()->numberBetween(30, 360),
            'completed_tasks' => fake()->numberBetween(0, 8),
            'planned_tasks' => fake()->numberBetween(2, 10),
        ];
    }
}
