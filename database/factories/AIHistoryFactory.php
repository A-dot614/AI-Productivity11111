<?php

namespace Database\Factories;

use App\Models\AIHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AIHistory>
 */
class AIHistoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'feature' => fake()->randomElement([
                'natural_language_task',
                'task_breakdown',
                'task_prioritization',
                'daily_planning',
                'weekly_planning',
                'productivity_suggestions',
                'note_summarization',
            ]),
            'prompt' => fake()->sentence(),
            'response' => fake()->paragraph(2),
            'status' => fake()->randomElement([AIHistory::STATUS_SUCCESS, AIHistory::STATUS_SUCCESS, AIHistory::STATUS_FAILED]),
            'provider' => fake()->randomElement(['openai', 'gemini']),
            'model' => fake()->randomElement(['gpt-4o-mini', 'gemini-1.5-flash']),
            'tokens_in' => fake()->numberBetween(50, 800),
            'tokens_out' => fake()->numberBetween(50, 600),
            'duration_ms' => fake()->numberBetween(400, 6000),
        ];
    }
}
