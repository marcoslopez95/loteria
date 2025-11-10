<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;

    /**
     * Guarded attributes.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * Casts definition.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'total' => 'decimal:2',
        ];
    }

    /**
     * Owner user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Order items.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Payments.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Recalculate total from items (5.00 USD per item).
     */
    public function recalcTotalFromItems(): void
    {
        $count = $this->items()->count();
        $this->total = $count * 5.00;
    }

    /**
     * Sum of payments in USD.
     */
    public function paidAmountUsd(): float
    {
        $sum = 0.0;
        $this->loadMissing('payments.currency');

        foreach ($this->payments as $payment) {
            $sum += $payment->amountInUsd();
        }

        return round($sum, 2);
    }

    /**
     * Refresh status according to payments vs total.
     */
    public function refreshStatusFromPayments(): void
    {
        $paid = $this->paidAmountUsd();
        $total = (float) $this->total;

        if ($paid >= $total && $this->status !== OrderStatus::Pagada) {
            $this->status = OrderStatus::Pagada;
            $this->save();
        } elseif ($paid < $total && $this->status === OrderStatus::Pagada) {
            $this->status = OrderStatus::PorPagar;
            $this->save();
        }
    }
}
