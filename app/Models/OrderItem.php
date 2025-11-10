<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    /** @use HasFactory<\Database\Factories\OrderItemFactory> */
    use HasFactory;

    /**
     * Guarded attributes.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * Casts.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    /**
     * Parent order.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Normalize number to 3 digits and fix amount to 5.00.
     */
    protected static function booted(): void
    {
        static::saving(function (self $item): void {
            if ($item->number !== null) {
                $item->number = str_pad((string) (int) $item->number, 3, '0', STR_PAD_LEFT);
            }
            $item->amount = 5.00;
        });
    }
}
