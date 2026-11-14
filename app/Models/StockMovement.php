<?php

namespace App\Models;

use Database\Factories\StockMovementFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Audit record. Created only by StockService; never edited or deleted through the UI.
 * No fillable attributes: every column is assigned explicitly on the server.
 */
class StockMovement extends Model
{
    /** @use HasFactory<StockMovementFactory> */
    use HasFactory;

    public const TYPE_IN = 'IN';

    public const TYPE_OUT = 'OUT';

    public const TYPE_ADJUSTMENT = 'ADJUSTMENT';

    public const TYPES = [self::TYPE_IN, self::TYPE_OUT, self::TYPE_ADJUSTMENT];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'previous_quantity' => 'integer',
            'new_quantity' => 'integer',
        ];
    }

    /** Includes archived products so history stays readable after archival. */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Signed change, e.g. +20 / -10 (adjustments derive the sign from previous/new). */
    public function signedChange(): int
    {
        return $this->new_quantity - $this->previous_quantity;
    }

    /** Movements the given user may see: all for admin, movements on own products for staff. */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->isAdmin()) {
            return $query;
        }

        return $query->whereHas('product', fn (Builder $q) => $q->withTrashed()->where('created_by', $user->id));
    }
}
