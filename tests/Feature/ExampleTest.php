<?php

it('redirects guests to login on dashboard', function () {
    $response = $this->get('/dashboard');

    $response->assertRedirect('/login');
});
