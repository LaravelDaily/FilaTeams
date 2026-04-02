<?php

namespace LaravelDaily\FilaTeams\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use LaravelDaily\FilaTeams\Models\Team;

/**
 * @extends Factory<Team>
 */
class TeamFactory extends Factory
{
    protected $model = Team::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'slug' => fake()->unique()->slug(2),
            'is_personal' => false,
        ];
    }

    public function personal(): static
    {
        return $this->state(fn () => [
            'is_personal' => true,
        ]);
    }

    public function trashed(): static
    {
        return $this->state(fn () => [
            'deleted_at' => now(),
        ]);
    }
}
