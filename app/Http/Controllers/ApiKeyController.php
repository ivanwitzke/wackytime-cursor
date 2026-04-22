<?php

namespace App\Http\Controllers;

use App\Services\ApiKeyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ApiKeyController extends Controller
{
    public function __construct(private readonly ApiKeyService $apiKeyService) {}

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
        ]);

        $result = $this->apiKeyService->createForUser(
            $request->user(),
            $validated['name'] ?? 'Default API Key'
        );

        return back()->with('new_api_key', $result['plainTextKey']);
    }
}
