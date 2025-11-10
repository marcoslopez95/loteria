<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    /** @use HasFactory<\Database\Factories\PaymentFactory> */
    use HasFactory;

    /**
     * Guarded attributes.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * Casts for monetary fields.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'exchange_rate' => 'decimal:8',
            'paid_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Get payment amount converted to USD using exchange rate.
     */
    public function amountInUsd(): float
    {
        $rate = (float) ($this->exchange_rate ?: 1);
        if ($rate <= 0) {
            $rate = 1.0;
        }

        // If currency is USD (rate == 1), return amount; else divide by rate.
        return round((float) $this->amount / $rate, 2);
    }

    protected static function booted(): void
    {
        // Ensure a sensible default for USD if not provided
        static::creating(function (self $payment): void {
            if (empty($payment->exchange_rate)) {
                $payment->exchange_rate = 1;
            }
        });

        // After save/delete, refresh parent order status
        $refresh = function (self $payment): void {
            if ($payment->relationLoaded('order') || $payment->order) {
                $payment->order->refreshStatusFromPayments();
            }
        };

        static::created($refresh);
        static::updated($refresh);
        static::deleted($refresh);
    }
}
