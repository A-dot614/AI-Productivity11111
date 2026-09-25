<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    public function definition(): array
    {
        $dueDate = fake()->dateTimeBetween('-7 days', '+14 days');

        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'title' => rtrim(fake()->sentence(4), '.'),
            'description' => fake()->optional(0.7)->paragraph(),
            'status' => fake()->randomElement([
                Task::STATUS_PENDING,
                Task::STATUS_IN_PROGRESS,
                Task::STATUS_COMPLETED,
            ]),
            'priority' => fake()->randomElement(Task::PRIORITIES),
            'due_date' => $dueDate,
            'scheduled_at' => fake()->optional(0.4)->dateTimeBetween('now', '+7 days'),
            'estimated_minutes' => fake()->optional(0.8)->numberBetween(15, 240),
            'actual_minutes' => null,
            'recurrence' => fake()->randomElement(Task::RECURRENCES),
            'sort_order' => 0,
            'completed_at' => null,
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Task::STATUS_COMPLETED,
            'completed_at' => fake()->dateTimeBetween('-30 days', 'now'),
            'actual_minutes' => fake()->numberBetween(10, 200),
        ]);
    }

    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Task::STATUS_PENDING,
            'due_date' => fake()->dateTimeBetween('-7 days', '-1 hour'),
        ]);
    }
}
