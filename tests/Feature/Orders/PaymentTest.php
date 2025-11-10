<?php

use App\Enums\OrderStatus;
use App\Models\Currency;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\patch;
use function Pest\Laravel\post;
use function Pest\Laravel\withoutMiddleware;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    // Disable CSRF for simplicity in feature tests
    withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);

    $this->auth = User::factory()->create();
    actingAs($this->auth);

    // Ensure currencies exist
    $this->usd = Currency::factory()->usd()->create();
    $this->ves = Currency::factory()->ves()->create();
});

function createOrderWithItems(int $count = 3): Order
{
    $payload = [
        'quick_user' => [
            'name' => 'Cliente '.Str::random(4),
            'email' => 'c'.Str::random(6).'@example.com',
        ],
        'items' => collect(range(1, $count))->map(fn ($n) => ['number' => (string) $n])->all(),
    ];

    post(route('orders.store'), $payload)
        ->assertRedirect();

    return Order::query()->latest('id')->first();
}

it('marks order as Pagada with a USD payment that covers the total', function (): void {
    $order = createOrderWithItems(2); // total = 10.00

    $response = post(route('orders.payments.store', $order), [
        'amount' => '10.00',
        'currency_id' => $this->usd->id,
        'exchange_rate' => '1',
        'reference' => 'REF-USD-'.Str::random(6),
        'paid_at' => now()->toDateTimeString(),
    ]);

    $response->assertRedirect();

    $order->refresh();
    expect($order->status->value)->toBe(OrderStatus::Pagada->value);
});

it('converts VES payment using exchange_rate and sets Pagada when covered', function (): void {
    $order = createOrderWithItems(2); // total = 10.00

    $amountVES = 221 * 10; // 2210 VES = 10 USD

    $response = post(route('orders.payments.store', $order), [
        'amount' => (string) $amountVES,
        'currency_id' => $this->ves->id,
        'exchange_rate' => '221',
        'reference' => 'REF-VES-'.Str::random(6),
        'paid_at' => now()->toDateTimeString(),
    ]);

    $response->assertRedirect();

    $order->refresh();
    expect($order->status->value)->toBe(OrderStatus::Pagada->value);
});

it('blocks adding payments when order is Pagada or Cancelada', function (): void {
    $order = createOrderWithItems(1); // total 5.00

    // First: pay fully in USD to set Pagada
    post(route('orders.payments.store', $order), [
        'amount' => '5.00',
        'currency_id' => $this->usd->id,
        'exchange_rate' => '1',
        'reference' => 'REF-BLOCK-'.Str::random(6),
    ])->assertRedirect();

    $order->refresh();
    expect($order->status->value)->toBe(OrderStatus::Pagada->value);

    // Try to add another payment now → should get validation error
    $resp2 = post(route('orders.payments.store', $order), [
        'amount' => '1.00',
        'currency_id' => $this->usd->id,
        'exchange_rate' => '1',
        'reference' => 'REF-POST-PAGADA-'.Str::random(4),
    ]);
    $resp2->assertSessionHasErrors('payment');

    // Set Cancelada and try to add payment
    patch(route('orders.update', $order), [
        'status' => OrderStatus::Cancelada->value,
        'items' => [['number' => '001']], // keep at least one item to satisfy validation
    ])->assertRedirect();

    $order->refresh();
    expect($order->status->value)->toBe(OrderStatus::Cancelada->value);

    $resp3 = post(route('orders.payments.store', $order), [
        'amount' => '1.00',
        'currency_id' => $this->usd->id,
        'exchange_rate' => '1',
        'reference' => 'REF-POST-CANCEL-'.Str::random(4),
    ]);
    $resp3->assertSessionHasErrors('payment');
});

it('requires unique payment reference', function (): void {
    $order = createOrderWithItems(1);

    $ref = 'REF-UNIQ-'.Str::random(5);

    post(route('orders.payments.store', $order), [
        'amount' => '5.00',
        'currency_id' => $this->usd->id,
        'exchange_rate' => '1',
        'reference' => $ref,
    ])->assertRedirect();

    // duplicate
    $resp2 = post(route('orders.payments.store', $order), [
        'amount' => '1.00',
        'currency_id' => $this->usd->id,
        'exchange_rate' => '1',
        'reference' => $ref,
    ]);

    $resp2->assertSessionHasErrors('reference');
});

it('quick user password is the fixed one', function (): void {
    $resp = post(route('users.quick.store'), [
        'name' => 'Nuevo Cliente',
        'email' => 'nuevo'.Str::random(4).'@example.com',
    ]);

    $resp->assertOk();

    $userId = $resp->json('id');
    $user = User::find($userId);

    expect($user)->not->toBeNull();
    expect(Hash::check('Secret*123*', $user->password))->toBeTrue();
});
