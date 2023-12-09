<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence,
            'description' => $this->faker->text,
            // 'genre' => $this->faker->word,
            // 'total_pages' => $this->faker->numberBetween(10, 100),

            /** priority_id ranges from 1-4 */
            'priority_id' => $this->faker->numberBetween(1, 4),
            'due_at' => $this->faker->boolean ? null : now()->addDays($this->faker->numberBetween(1, 4)),
            'completed_at' => $this->faker->boolean ? null : now()->addDays($this->faker->numberBetween(1, 4)),
            'archived_at' => $this->faker->boolean ? null : now()->addDays($this->faker->numberBetween(2, 6)),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
