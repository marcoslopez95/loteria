<?php

use Inertia\Testing\AssertableInertia as Assert;

it('renders raffle home with numbers 000-999', function () {
    $response = $this->get(route('home'));

    $response->assertOk();

    $response->assertInertia(fn (Assert $page) => $page
        ->component('raffle/Index')
        ->has('numbers', 1000)
        ->where('numbers.0', '000')
        ->where('numbers.999', '999')
    );
});
