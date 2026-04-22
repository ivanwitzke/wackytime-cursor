<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReportResource;
use App\Services\ReportAggregationService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(private readonly ReportAggregationService $aggregationService) {}

    public function __invoke(Request $request): Response
    {
        $validated = $request->validate([
            'start' => ['nullable', 'date'],
            'end' => ['nullable', 'date', 'after_or_equal:start'],
        ]);

        $end = isset($validated['end']) ? Carbon::parse($validated['end'])->endOfDay() : Carbon::now();
        $start = isset($validated['start'])
            ? Carbon::parse($validated['start'])->startOfDay()
            : Carbon::now()->subDays(6)->startOfDay();

        $report = $this->aggregationService->aggregateForUser(
            $request->user()->id,
            $start,
            $end
        );

        return Inertia::render('Dashboard', [
            'report' => ReportResource::make($report)->resolve(),
            'filters' => [
                'start' => $start->toDateString(),
                'end' => $end->toDateString(),
            ],
        ]);
    }
}
