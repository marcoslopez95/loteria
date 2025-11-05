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

    // Fortify should redirect to intended location (dashboard by default)
    $response->assertRedirect(route('dashboard'));

    // Assert user is authenticated
    expect(auth()->check())->toBeTrue();
});
