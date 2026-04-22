<?php

namespace App\Http\Middleware;

use App\Services\ApiKeyService;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateApiKey
{
    public function __construct(private readonly ApiKeyService $apiKeyService) {}

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $providedKey = $this->resolveApiKey($request);

        if (! $providedKey) {
            return $this->unauthorized('Missing api key.');
        }

        $apiKey = $this->apiKeyService->findActiveByPlainText($providedKey);

        if (! $apiKey) {
            return $this->unauthorized('Invalid api key.');
        }

        $this->apiKeyService->touchLastUsed($apiKey);

        auth()->setUser($apiKey->user);
        $request->setUserResolver(fn () => $apiKey->user);

        return $next($request);
    }

    private function resolveApiKey(Request $request): ?string
    {
        $header = $request->header('Authorization');

        if (is_string($header) && str_starts_with($header, 'Basic ')) {
            $decoded = base64_decode(substr($header, 6), true);
            if ($decoded !== false && $decoded !== '') {
                return trim(explode(':', $decoded, 2)[0]);
            }
        }

        if ($request->filled('api_key')) {
            return trim((string) $request->string('api_key'));
        }

        return null;
    }

    private function unauthorized(string $message): JsonResponse
    {
        return response()->json([
            'error' => $message,
        ], 401);
    }
}
