<?php

use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderPaymentController;
use App\Http\Controllers\QuickUserController;
use App\Http\Controllers\UserSearchController;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    $numbers = array_map(
        fn (int $n): string => str_pad((string) $n, 3, '0', STR_PAD_LEFT),
        range(0, 999)
    );

    $numbersInOrders = OrderItem::whereHas('order',fn(Builder $q) => $q->whereNot('status', \App\Enums\OrderStatus::Cancelada))
        ->pluck('number')
        ->toArray();


    return Inertia::render('raffle/Index', [
        'numbers' => $numbers,
        'canRegister' => Features::enabled(Features::registration()),
        'taken' => $numbersInOrders,
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function (): void {
    Route::resource('currencies', CurrencyController::class);
    Route::resource('orders', OrderController::class);

    Route::post('orders/{order}/payments', [OrderPaymentController::class, 'store'])->name('orders.payments.store');
    Route::patch('orders/{order}/payments/{payment}', [OrderPaymentController::class, 'update'])->name('orders.payments.update');
    Route::delete('orders/{order}/payments/{payment}', [OrderPaymentController::class, 'destroy'])->name('orders.payments.destroy');

    Route::get('users/search', UserSearchController::class)->name('users.search');
    Route::post('users/quick', [QuickUserController::class, 'store'])->name('users.quick.store');
});

require __DIR__.'/settings.php';
