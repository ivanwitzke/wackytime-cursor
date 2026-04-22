<?php

use App\Models\ApiKey;

use function Pest\Laravel\post;

it('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

it('new users can register and receive an api key', function () {
    $response = post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
    $response->assertSessionHas('new_api_key');

    expect(ApiKey::query()->count())->toBe(1);
    expect(ApiKey::query()->first()?->key_hash)->not->toBeNull();
});
