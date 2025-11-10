<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

it('renders the dashboard page when authenticated', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();

    $response->assertInertia(fn (Assert $page) => $page
        ->component('Dashboard')
    );
});
