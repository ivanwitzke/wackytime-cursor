<?php

namespace Database\Factories;

use App\Models\ApiKey;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ApiKey>
 */
class ApiKeyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $plain = Str::ulid()->toBase32().Str::random(24);

        return [
            'user_id' => User::factory(),
            'name' => 'Default API Key',
            'key_hash' => hash('sha256', $plain),
            'key_prefix' => substr($plain, 0, 12),
            'last_used_at' => null,
            'revoked_at' => null,
        ];
    }
}
