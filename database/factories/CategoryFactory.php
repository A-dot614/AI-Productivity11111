<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => ucfirst(fake()->unique()->word()),
            'color' => fake()->hexColor(),
            'is_global' => false,
        ];
    }

    public function global(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => null,
            'is_global' => true,
        ]);
    }
}
