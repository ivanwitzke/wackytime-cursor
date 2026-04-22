<?php

use App\Models\Heartbeat;
use App\Models\User;

use function Pest\Laravel\actingAs;

it('shows dashboard report for authenticated users', function () {
    $user = User::factory()->create();

    Heartbeat::factory()->for($user)->create([
        'occurred_at' => now()->subDay(),
        'project_name' => 'project-a',
        'language' => 'PHP',
        'editor' => 'VS Code',
    ]);

    actingAs($user)
        ->get('/dashboard')
        ->assertOk();
});

it('lists derived projects page', function () {
    $user = User::factory()->create();

    Heartbeat::factory()->for($user)->create([
        'occurred_at' => now()->subDay(),
        'project_name' => 'project-b',
        'language' => 'PHP',
        'editor' => 'VS Code',
    ]);

    actingAs($user)
        ->get('/projects')
        ->assertOk();
});
