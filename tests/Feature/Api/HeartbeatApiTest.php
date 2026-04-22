<?php

use App\Models\ApiKey;
use App\Models\Heartbeat;
use App\Models\User;

use function Pest\Laravel\postJson;

function basicHeader(ApiKey $apiKey): string
{
    return 'Basic '.base64_encode($apiKey->key_prefix);
}

it('rejects heartbeat without api key', function () {
    postJson('/api/v1/users/current/heartbeats', [
        'time' => now()->timestamp,
        'entity' => '/tmp/a.php',
        'type' => 'file',
    ])->assertUnauthorized();
});

it('ingests heartbeat with valid basic auth key', function () {
    $user = User::factory()->create();
    $plain = 'waka_test_key_123';

    $apiKey = ApiKey::factory()->for($user)->create([
        'key_hash' => hash('sha256', $plain),
        'key_prefix' => substr($plain, 0, 12),
    ]);

    postJson('/api/v1/users/current/heartbeats', [
        'time' => now()->valueOf() / 1000,
        'entity' => '/workspace/app/Http/Controllers/Test.php',
        'type' => 'file',
        'project' => 'my-project',
        'language' => 'PHP',
        'editor' => 'VS Code',
        'is_write' => true,
    ], [
        'Authorization' => 'Basic '.base64_encode($plain),
    ])
        ->assertStatus(201)
        ->assertJsonPath('data.project', 'my-project');

    expect($apiKey->fresh()?->last_used_at)->not->toBeNull();
    expect(Heartbeat::query()->count())->toBe(1);
});

it('marks near duplicated heartbeat as accepted duplicate', function () {
    $user = User::factory()->create();
    $plain = 'waka_duplicate_key_123';

    ApiKey::factory()->for($user)->create([
        'key_hash' => hash('sha256', $plain),
        'key_prefix' => substr($plain, 0, 12),
    ]);

    $timestamp = now()->timestamp + 0.123456;

    postJson('/api/v1/users/current/heartbeats', [
        'time' => $timestamp,
        'entity' => '/workspace/app/Http/Controllers/Test.php',
        'type' => 'file',
    ], [
        'Authorization' => 'Basic '.base64_encode($plain),
    ])->assertStatus(201);

    postJson('/api/v1/users/current/heartbeats', [
        'time' => $timestamp + 1,
        'entity' => '/workspace/app/Http/Controllers/Test.php',
        'type' => 'file',
    ], [
        'Authorization' => 'Basic '.base64_encode($plain),
    ])->assertStatus(202);

    expect(Heartbeat::query()->count())->toBe(1);
});

it('accepts bulk heartbeats endpoint', function () {
    $user = User::factory()->create();
    $plain = 'waka_bulk_key_123';

    ApiKey::factory()->for($user)->create([
        'key_hash' => hash('sha256', $plain),
        'key_prefix' => substr($plain, 0, 12),
    ]);

    postJson('/api/v1/users/current/heartbeats.bulk', [
        'heartbeats' => [
            [
                'time' => now()->timestamp,
                'entity' => '/tmp/a.php',
                'type' => 'file',
                'project' => 'bulk-project',
            ],
            [
                'time' => now()->timestamp + 300,
                'entity' => '/tmp/b.php',
                'type' => 'file',
                'project' => 'bulk-project',
            ],
        ],
    ], [
        'Authorization' => 'Basic '.base64_encode($plain),
    ])
        ->assertStatus(202)
        ->assertJsonCount(2, 'data');

    expect(Heartbeat::query()->count())->toBe(2);
});
