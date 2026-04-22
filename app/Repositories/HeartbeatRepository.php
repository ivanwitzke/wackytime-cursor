<?php

namespace App\Repositories;

use App\Models\Heartbeat;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class HeartbeatRepository
{
    public function create(array $attributes): Heartbeat
    {
        return Heartbeat::query()->create($attributes);
    }

    public function existsByDedupeHash(string $hash): bool
    {
        return Heartbeat::query()->where('dedupe_hash', $hash)->exists();
    }

    public function hasNearDuplicate(int $userId, string $entity, string $type, Carbon $occurredAt, int $seconds = 2): bool
    {
        return Heartbeat::query()
            ->where('user_id', $userId)
            ->where('entity', $entity)
            ->where('type', $type)
            ->whereBetween('occurred_at', [
                $occurredAt->copy()->subSeconds($seconds),
                $occurredAt->copy()->addSeconds($seconds),
            ])
            ->exists();
    }

    /**
     * @return Collection<int, Heartbeat>
     */
    public function between(int $userId, Carbon $start, Carbon $end): Collection
    {
        return Heartbeat::query()
            ->where('user_id', $userId)
            ->whereBetween('occurred_at', [$start, $end])
            ->orderBy('occurred_at')
            ->get();
    }
}
