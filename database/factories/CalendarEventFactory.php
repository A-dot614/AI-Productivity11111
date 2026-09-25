<?php

namespace Database\Factories;

use App\Models\CalendarEvent;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CalendarEvent>
 */
class CalendarEventFactory extends Factory
{
    public function definition(): array
    {
        $start = fake()->dateTimeBetween('-7 days', '+14 days');
        $end = (clone $start)->modify('+'.fake()->numberBetween(30, 180).' minutes');

        return [
            'user_id' => User::factory(),
            'title' => ucfirst(fake()->words(4, true)),
            'description' => fake()->optional(0.5)->sentence(),
            'start_at' => $start,
            'end_at' => $end,
            'is_all_day' => fake()->boolean(15),
            'color' => fake()->randomElement(['#6366f1', '#10b981', '#f59e0b', '#ec4899', '#06b6d4']),
        ];
    }
}
