<?php

namespace Database\Factories;

use App\Models\Note;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Note>
 */
class NoteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => ucfirst(fake()->words(3, true)),
            'content' => implode("\n\n", [
                fake()->paragraph(3),
                '## '.ucfirst(fake()->words(2, true)),
                fake()->paragraph(2),
            ]),
            'is_pinned' => fake()->boolean(15),
        ];
    }
}
