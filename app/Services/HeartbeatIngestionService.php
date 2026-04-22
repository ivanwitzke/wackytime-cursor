<?php

namespace App\Services;

use App\Models\Project;
use App\Models\User;
use App\Repositories\HeartbeatRepository;
use Illuminate\Support\Carbon;

class HeartbeatIngestionService
{
    public function __construct(private readonly HeartbeatRepository $heartbeatRepository) {}

    /**
     * @param  array<string, mixed>  $payload
     * @return array{stored: bool,duplicate: bool,data: array<string, mixed>}
     */
    public function ingest(User $user, array $payload): array
    {
        $timestamp = (float) $payload['time'];
        $occurredAt = Carbon::createFromTimestampUTC((int) floor($timestamp))
            ->setMicroseconds((int) (($timestamp - floor($timestamp)) * 1_000_000));

        $entity = trim((string) $payload['entity']);
        $projectName = $this->normalizeNullableString($payload['project'] ?? null);
        $language = $this->normalizeNullableString($payload['language'] ?? null);
        $editor = $this->normalizeNullableString($payload['editor'] ?? null);
        $type = (string) $payload['type'];
        $isWrite = (bool) ($payload['is_write'] ?? false);

        $dedupeHash = hash('sha256', implode('|', [
            $user->id,
            number_format($timestamp, 6, '.', ''),
            $entity,
            $type,
            $projectName,
            $language,
            $editor,
            (int) $isWrite,
        ]));

        if (
            $this->heartbeatRepository->existsByDedupeHash($dedupeHash)
            || $this->heartbeatRepository->hasNearDuplicate($user->id, $entity, $type, $occurredAt)
        ) {
            return [
                'stored' => false,
                'duplicate' => true,
                'data' => [
                    'time' => $timestamp,
                    'entity' => $entity,
                    'type' => $type,
                ],
            ];
        }

        $project = $this->findOrCreateProject($user, $projectName, $occurredAt);

        $heartbeat = $this->heartbeatRepository->create([
            'user_id' => $user->id,
            'project_id' => $project?->id,
            'heartbeat_at' => $timestamp,
            'occurred_at' => $occurredAt,
            'entity' => $entity,
            'type' => $type,
            'project_name' => $projectName,
            'language' => $language,
            'editor' => $editor,
            'is_write' => $isWrite,
            'dedupe_hash' => $dedupeHash,
        ]);

        return [
            'stored' => true,
            'duplicate' => false,
            'data' => [
                'id' => (string) $heartbeat->id,
                'time' => $timestamp,
                'entity' => $entity,
                'type' => $type,
                'project' => $projectName,
                'language' => $language,
                'editor' => $editor,
                'is_write' => $isWrite,
                'created_at' => $heartbeat->created_at?->toIso8601String(),
            ],
        ];
    }

    private function findOrCreateProject(User $user, ?string $projectName, Carbon $occurredAt): ?Project
    {
        if (! $projectName) {
            return null;
        }

        $normalized = mb_strtolower(trim($projectName));

        /** @var Project $project */
        $project = Project::query()->firstOrCreate(
            [
                'user_id' => $user->id,
                'normalized_name' => $normalized,
            ],
            [
                'name' => $projectName,
                'first_seen_at' => $occurredAt,
                'last_seen_at' => $occurredAt,
            ]
        );

        if ($project->last_seen_at === null || $project->last_seen_at->lt($occurredAt)) {
            $project->forceFill(['last_seen_at' => $occurredAt])->save();
        }

        return $project;
    }

    private function normalizeNullableString(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }
}
