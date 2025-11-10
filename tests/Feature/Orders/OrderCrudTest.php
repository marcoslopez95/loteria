<?php

use App\Enums\OrderStatus;
use App\Models\Currency;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

use function Pest\Laravel\post;
use function Pest\Laravel\withoutMiddleware;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    // Disable all middleware for these backend behavior tests
    withoutMiddleware();
});

use function Pest\Laravel\actingAs;

it('creates an order with quick user and items; normalizes numbers and calculates total', function (): void {
    $auth = User::factory()->create();
    actingAs($auth);

    // Ensure currencies exist (not required for order creation, but seed base data anyway)
    Currency::factory()->usd()->create();
    Currency::factory()->ves()->create();

    $payload = [
        'quick_user' => [
            'name' => 'Cliente Rápido',
            'email' => 'cliente'.Str::random(5).'@example.com',
        ],
        'items' => [
            ['number' => '1'],
            ['number' => '002'],
            ['number' => '999'],
        ],
        'notes' => 'Prueba de orden',
    ];

    $response = post(route('orders.store'), $payload);

    $response->assertRedirect();

    /** @var Order $order */
    $order = Order::query()->latest('id')->first();

    expect($order)->not->toBeNull();
    expect($order->status->value)->toBe(OrderStatus::PorPagar->value);
    expect((string) $order->total)->toBe('15.00');

    $numbers = $order->items()->pluck('number')->all();
    sort($numbers);

    expect($numbers)->toEqual(['001', '002', '999']);
});
