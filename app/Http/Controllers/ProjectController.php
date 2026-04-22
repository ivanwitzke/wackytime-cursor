<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function index(Request $request): Response
    {
        $projects = Project::query()
            ->where('user_id', $request->user()->id)
            ->orderByDesc('last_seen_at')
            ->get(['id', 'name', 'first_seen_at', 'last_seen_at'])
            ->map(fn (Project $project): array => [
                'id' => $project->id,
                'name' => $project->name,
                'first_seen_at' => $project->first_seen_at?->toIso8601String(),
                'last_seen_at' => $project->last_seen_at?->toIso8601String(),
            ]);

        return Inertia::render('Projects/Index', [
            'projects' => $projects,
        ]);
    }
}
