<?php

namespace App\Services;

use App\Models\ApiKey;
use App\Models\User;
use Illuminate\Support\Str;

class ApiKeyService
{
    /**
     * @return array{apiKey: ApiKey,plainTextKey: string}
     */
    public function createForUser(User $user, string $name = 'Default API Key'): array
    {
        $plainTextKey = Str::ulid()->toBase32().Str::random(24);
        $hash = hash('sha256', $plainTextKey);

        $apiKey = ApiKey::query()->create([
            'user_id' => $user->id,
            'name' => $name,
            'key_hash' => $hash,
            'key_prefix' => substr($plainTextKey, 0, 12),
        ]);

        return [
            'apiKey' => $apiKey,
            'plainTextKey' => $plainTextKey,
        ];
    }

    public function findActiveByPlainText(string $plainTextKey): ?ApiKey
    {
        return ApiKey::query()
            ->active()
            ->with('user')
            ->where('key_hash', hash('sha256', $plainTextKey))
            ->first();
    }

    public function touchLastUsed(ApiKey $apiKey): void
    {
        $apiKey->forceFill(['last_used_at' => now()])->save();
    }

    public function revoke(ApiKey $apiKey): void
    {
        $apiKey->forceFill(['revoked_at' => now()])->save();
    }
}
