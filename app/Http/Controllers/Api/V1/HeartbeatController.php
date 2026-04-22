<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHeartbeatBulkRequest;
use App\Http\Requests\StoreHeartbeatRequest;
use App\Http\Resources\HeartbeatResource;
use App\Services\HeartbeatIngestionService;
use Illuminate\Http\JsonResponse;

class HeartbeatController extends Controller
{
    public function __construct(private readonly HeartbeatIngestionService $ingestionService) {}

    public function store(StoreHeartbeatRequest $request): JsonResponse
    {
        $result = $this->ingestionService->ingest($request->user(), $request->validated());

        return response()->json([
            'data' => HeartbeatResource::make($result['data'])->resolve(),
        ], $result['duplicate'] ? 202 : 201);
    }

    public function bulk(StoreHeartbeatBulkRequest $request): JsonResponse
    {
        $data = collect($request->validated('heartbeats'))
            ->map(fn (array $payload) => $this->ingestionService->ingest($request->user(), $payload)['data'])
            ->map(fn (array $item) => HeartbeatResource::make($item)->resolve())
            ->all();

        return response()->json([
            'data' => $data,
        ], 202);
    }
}
