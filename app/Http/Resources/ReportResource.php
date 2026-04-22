<?php

namespace App\Http\Resources;

use App\Support\DurationFormatter;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'range' => $this->resource['range'],
            'totals' => [
                'seconds' => $this->resource['totals']['seconds'],
                'text' => DurationFormatter::humanize($this->resource['totals']['seconds']),
            ],
            'daily' => $this->resource['daily'],
            'projects' => $this->resource['projects'],
            'languages' => $this->resource['languages'],
            'editors' => $this->resource['editors'],
        ];
    }
}
