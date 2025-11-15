<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Http\Requests\OrderStoreRequest;
use App\Http\Requests\OrderUpdateRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): InertiaResponse
    {
        $orders = Order::query()
            ->with('user')
            ->latest('id')
            ->get();

        return Inertia::render('orders/Index', [
            'orders' => $orders,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): InertiaResponse
    {
        $numbersInOrders = OrderItem::whereHas('order',fn(Builder $q) => $q->whereNot('status', \App\Enums\OrderStatus::Cancelada))
            ->pluck('number')
            ->toArray();
        return Inertia::render('orders/Create',[
            'taken' => $numbersInOrders,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OrderStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Create or get user
        $userId = $data['user_id'] ?? null;
        if (! $userId && ! empty($data['quick_user'])) {
            $quick = $data['quick_user'];
            /** @var User $user */
            $user = User::query()->create([
                'name' => $quick['name'],
                'email' => $quick['email'],
                'password' => bcrypt('Secret*123*'),
            ]);
            $userId = $user->id;
        }

        $order = Order::query()->create([
            'user_id' => $userId,
            'status' => OrderStatus::PorPagar,
            'total' => 0,
            'notes' => $data['notes'] ?? null,
        ]);

        // Save items (normalize number and fixed amount applied in model event)
        $items = collect($data['items'])
            ->unique(fn ($i) => str_pad((string) ((int) $i['number']), 3, '0', STR_PAD_LEFT))
            ->map(fn ($i) => [
                'number' => $i['number'],
            ])->all();

        foreach ($items as $i) {
            $order->items()->create($i);
        }

        // Recalculate total (5.00 per item)
        $order->recalcTotalFromItems();
        $order->save();

        return redirect()->route('orders.show', $order)
            ->with('status', 'Orden creada correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order): InertiaResponse
    {
        $order->load(['user', 'items', 'payments.currency']);

        return Inertia::render('orders/Show', [
            'order' => $order,
            'paidAmountUsd' => $order->paidAmountUsd(),
            'currencies' => \App\Models\Currency::query()->orderBy('code')->get(['id', 'code', 'name', 'symbol', 'active']),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order): InertiaResponse
    {
        $order->load(['user', 'items']);

        return Inertia::render('orders/Edit', [
            'order' => $order,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(OrderUpdateRequest $request, Order $order): RedirectResponse
    {
        $data = $request->validated();
        $quantityNumberBefore = $order->items->count();
        if (! empty($data['items'])) {
            // Replace items
            $order->items()->delete();

            $items = collect($data['items'])
                ->unique(fn ($i) => str_pad((string) ((int) $i['number']), 3, '0', STR_PAD_LEFT))
                ->map(fn ($i) => [
                    'number' => $i['number'],
                ])->all();

            foreach ($items as $i) {
                $order->items()->create($i);
            }

            $order->recalcTotalFromItems();
        }

        // Only allow manual cancellation; Pagada is automatic by payments
        if (! empty($data['status']) && $data['status'] === OrderStatus::Cancelada->value) {
            $order->status = OrderStatus::Cancelada;
        }else if ($quantityNumberBefore !== count($data['items'])) {
            $order->status = OrderStatus::PorPagar;
        }

        $order->notes = $data['notes'] ?? $order->notes;
        $order->save();

        return redirect()->route('orders.show', $order)
            ->with('status', 'Orden actualizada');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order): RedirectResponse
    {
        $order->delete();

        return redirect()->route('orders.index')
            ->with('status', 'Orden eliminada');
    }
}
