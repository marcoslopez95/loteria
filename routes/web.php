<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    $numbers = array_map(
        fn (int $n): string => str_pad((string) $n, 3, '0', STR_PAD_LEFT),
        range(0, 999)
    );

    return Inertia::render('raffle/Index', [
        'numbers' => $numbers,
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';
