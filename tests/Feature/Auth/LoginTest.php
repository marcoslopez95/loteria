<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;

it('renders the login page', function () {
    $response = $this->get(route('login'));

    $response->assertOk();

    $response->assertInertia(fn (Assert $page) => $page
        ->component('auth/Login')
    );
});

it('logs in with valid credentials', function () {
    // Create a user with a known password
    $user = User::factory()->create([
        'password' => Hash::make('password'),
    ]);

    // Prepare CSRF token in session and header
    $token = csrf_token();

    $response = $this
        ->withSession(['_token' => $token])
        ->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
            '_token' => $token,
        ]);

    // With two-factor enabled and configured by default in factories,
    // Fortify redirects to the 2FA challenge after validating credentials.
    $response->assertRedirect(url('/two-factor-challenge'));

    // At this point the user is not fully authenticated until 2FA step completes.
    // So we don't assert auth()->check() here.
});
