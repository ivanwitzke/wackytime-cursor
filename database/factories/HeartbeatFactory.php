<?php

namespace Database\Factories;

use App\Models\Heartbeat;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Heartbeat>
 */
class HeartbeatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $occurredAt = now()->subMinutes(fake()->numberBetween(1, 120));

        return [
            'user_id' => User::factory(),
            'project_id' => null,
            'heartbeat_at' => (float) $occurredAt->format('U.u'),
            'occurred_at' => $occurredAt,
            'entity' => '/workspace/'.fake()->word().'.php',
            'type' => 'file',
            'project_name' => null,
            'language' => fake()->randomElement(['PHP', 'JavaScript', 'Vue']),
            'editor' => fake()->randomElement(['VS Code', 'Vim']),
            'is_write' => fake()->boolean(),
            'dedupe_hash' => hash('sha256', fake()->uuid()),
        ];
    }

    public function forProject(Project $project): static
    {
        return $this->state(fn (): array => [
            'project_id' => $project->id,
            'project_name' => $project->name,
        ]);
    }
}
