<?php

use App\Models\Heartbeat;
use App\Models\User;
use App\Services\ReportAggregationService;

it('aggregates heartbeat durations by buckets', function () {
    $user = User::factory()->create();

    $base = now()->subHour();

    Heartbeat::factory()->for($user)->create([
        'occurred_at' => $base,
        'heartbeat_at' => $base->timestamp,
        'project_name' => 'proj-1',
        'language' => 'PHP',
        'editor' => 'VS Code',
        'entity' => '/tmp/a.php',
        'type' => 'file',
    ]);

    Heartbeat::factory()->for($user)->create([
        'occurred_at' => $base->copy()->addSeconds(30),
        'heartbeat_at' => $base->copy()->addSeconds(30)->timestamp,
        'project_name' => 'proj-1',
        'language' => 'PHP',
        'editor' => 'VS Code',
        'entity' => '/tmp/b.php',
        'type' => 'file',
    ]);

    Heartbeat::factory()->for($user)->create([
        'occurred_at' => $base->copy()->addSeconds(300),
        'heartbeat_at' => $base->copy()->addSeconds(300)->timestamp,
        'project_name' => 'proj-2',
        'language' => 'Vue',
        'editor' => 'VS Code',
        'entity' => '/tmp/c.vue',
        'type' => 'file',
    ]);

    $service = app(ReportAggregationService::class);
    $report = $service->aggregateForUser($user->id, now()->subDays(2), now());

    expect($report['totals']['seconds'])->toBeGreaterThan(0)
        ->and($report['projects'])->toBeArray()
        ->and($report['languages'])->toBeArray()
        ->and($report['editors'])->toBeArray()
        ->and($report['daily'])->toBeArray();
});
