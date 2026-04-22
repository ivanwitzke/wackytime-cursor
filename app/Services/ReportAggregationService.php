<?php

namespace App\Services;

use App\Repositories\HeartbeatRepository;
use Illuminate\Support\Carbon;

class ReportAggregationService
{
    private const GAP_SECONDS = 120;

    public function __construct(private readonly HeartbeatRepository $heartbeatRepository) {}

    /**
     * @return array<string, mixed>
     */
    public function aggregateForUser(int $userId, Carbon $start, Carbon $end): array
    {
        $heartbeats = $this->heartbeatRepository->between($userId, $start, $end)->values();

        $byProject = [];
        $byLanguage = [];
        $byEditor = [];
        $daily = [];
        $totalSeconds = 0;

        for ($i = 0; $i < $heartbeats->count(); $i++) {
            $current = $heartbeats[$i];
            $next = $heartbeats[$i + 1] ?? null;

            $duration = 0;
            if ($next && $next->occurred_at !== null && $current->occurred_at !== null) {
                $delta = $next->occurred_at->diffInSeconds($current->occurred_at, false);
                $deltaAbs = abs($delta);
                $duration = min($deltaAbs, self::GAP_SECONDS);
            }

            $project = $current->project_name ?? 'Unknown';
            $language = $current->language ?? 'Unknown';
            $editor = $current->editor ?? 'Unknown';
            $day = $current->occurred_at?->copy()->timezone(config('app.timezone'))->toDateString() ?? 'unknown';

            $byProject[$project] = ($byProject[$project] ?? 0) + $duration;
            $byLanguage[$language] = ($byLanguage[$language] ?? 0) + $duration;
            $byEditor[$editor] = ($byEditor[$editor] ?? 0) + $duration;
            $daily[$day] = ($daily[$day] ?? 0) + $duration;
            $totalSeconds += $duration;
        }

        return [
            'range' => [
                'start' => $start->toIso8601String(),
                'end' => $end->toIso8601String(),
            ],
            'totals' => [
                'seconds' => $totalSeconds,
            ],
            'daily' => $this->withPercentages($daily, $totalSeconds),
            'projects' => $this->withPercentages($byProject, $totalSeconds),
            'languages' => $this->withPercentages($byLanguage, $totalSeconds),
            'editors' => $this->withPercentages($byEditor, $totalSeconds),
        ];
    }

    /**
     * @param  array<string, int>  $buckets
     * @return array<int, array{name: string, seconds: int, percentage: float}>
     */
    private function withPercentages(array $buckets, int $total): array
    {
        arsort($buckets);

        return collect($buckets)
            ->map(fn (int $seconds, string $name): array => [
                'name' => $name,
                'seconds' => $seconds,
                'percentage' => $total > 0 ? round(($seconds / $total) * 100, 2) : 0.0,
            ])
            ->values()
            ->all();
    }
}
