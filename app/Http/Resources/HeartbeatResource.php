<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HeartbeatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) ($this->resource['id'] ?? ''),
            'time' => $this->resource['time'] ?? null,
            'entity' => $this->resource['entity'] ?? null,
            'type' => $this->resource['type'] ?? null,
            'project' => $this->resource['project'] ?? null,
            'language' => $this->resource['language'] ?? null,
            'editor' => $this->resource['editor'] ?? null,
            'is_write' => $this->resource['is_write'] ?? false,
            'created_at' => $this->resource['created_at'] ?? null,
        ];
    }
}
