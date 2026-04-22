<?php

namespace App\Models;

use Database\Factories\HeartbeatFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Heartbeat extends Model
{
    /** @use HasFactory<HeartbeatFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'project_id',
        'heartbeat_at',
        'occurred_at',
        'entity',
        'type',
        'project_name',
        'language',
        'editor',
        'is_write',
        'dedupe_hash',
    ];

    protected function casts(): array
    {
        return [
            'heartbeat_at' => 'decimal:6',
            'occurred_at' => 'datetime',
            'is_write' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
