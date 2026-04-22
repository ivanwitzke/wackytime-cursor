<?php

use App\Models\User;
use App\Services\ApiKeyService;

it('creates hashed api key and can resolve it', function () {
    $user = User::factory()->create();
    $service = app(ApiKeyService::class);

    $generated = $service->createForUser($user, 'CLI');

    expect($generated['plainTextKey'])->not->toBeEmpty()
        ->and($generated['apiKey']->key_hash)->toBe(hash('sha256', $generated['plainTextKey']));

    $resolved = $service->findActiveByPlainText($generated['plainTextKey']);

    expect($resolved)->not->toBeNull()
        ->and($resolved?->user_id)->toBe($user->id);
});
