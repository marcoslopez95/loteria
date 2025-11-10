<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Http\Requests\PaymentStoreRequest;
use App\Http\Requests\PaymentUpdateRequest;
use App\Models\Currency;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;

class OrderPaymentController extends Controller
{
    /**
     * Store a newly created payment for the order.
     */
    public function store(PaymentStoreRequest $request, Order $order): RedirectResponse
    {
        if ($order->status !== OrderStatus::PorPagar) {
            throw ValidationException::withMessages([
                'payment' => 'No se pueden agregar pagos a una orden Pagada o Cancelada.',
            ]);
        }

        $data = $request->validated();

        // If currency is USD (code==USD), force rate 1; otherwise use provided
        // We keep it simple: expect correct exchange_rate from request as validated
        $order->payments()->create([
            'currency_id' => $data['currency_id'],
            'amount' => $data['amount'],
            'exchange_rate' => $data['exchange_rate'],
            'reference' => $data['reference'],
            'paid_at' => $data['paid_at'] ?? null,
        ]);

        return redirect()->route('orders.show', $order)
            ->with('status', 'Pago agregado');
    }

    /**
     * Update the specified payment.
     */
    public function update(PaymentUpdateRequest $request, Order $order, Payment $payment): RedirectResponse
    {
        $data = $request->validated();

        $payment->update([
            'currency_id' => $data['currency_id'],
            'amount' => $data['amount'],
            'exchange_rate' => $data['exchange_rate'],
            'reference' => $data['reference'],
            'paid_at' => $data['paid_at'] ?? null,
        ]);

        return redirect()->route('orders.show', $order)
            ->with('status', 'Pago actualizado');
    }

    /**
     * Remove the specified payment from storage.
     */
    public function destroy(Order $order, Payment $payment): RedirectResponse
    {
        $payment->delete();

        return redirect()->route('orders.show', $order)
            ->with('status', 'Pago eliminado');
    }
}
