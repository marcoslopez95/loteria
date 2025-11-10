<?php

namespace App\Http\Controllers;

use App\Http\Requests\CurrencyStoreRequest;
use App\Http\Requests\CurrencyUpdateRequest;
use App\Models\Currency;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class CurrencyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): InertiaResponse|Response
    {
        $currencies = Currency::query()->orderBy('code')->get();

        // TODO: Replace with Inertia page when UI is ready
        return Inertia::render('currencies/Index', [
            'currencies' => $currencies,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): InertiaResponse
    {
        return Inertia::render('currencies/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CurrencyStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();
        Currency::query()->create($data);

        return redirect()->route('currencies.index')
            ->with('status', 'Moneda creada correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(Currency $currency): InertiaResponse
    {
        return Inertia::render('currencies/Show', [
            'currency' => $currency,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Currency $currency): InertiaResponse
    {
        return Inertia::render('currencies/Edit', [
            'currency' => $currency,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CurrencyUpdateRequest $request, Currency $currency): RedirectResponse
    {
        $data = $request->validated();
        $currency->update($data);

        return redirect()->route('currencies.index')
            ->with('status', 'Moneda actualizada');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Currency $currency): RedirectResponse
    {
        $currency->delete();

        return redirect()->route('currencies.index')
            ->with('status', 'Moneda eliminada');
    }
}
