<?php

namespace App\Services;

use App\Exceptions\StockException;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

/**
 * The only place where product quantities change (plan §6 Module 6, §11).
 *
 * Every change runs in one DB transaction that locks the product row,
 * re-checks authorization and stock on the locked row, updates the quantity
 * and writes the audit movement. Any failure rolls back both.
 */
class StockService
{
    /** Upper bound that keeps quantities well inside an UNSIGNED INT column. */
    public const MAX_QUANTITY = 2_000_000_000;

    public function stockIn(Product $product, User $user, int $quantity, ?string $reason = null): StockMovement
    {
        return $this->apply($product, $user, StockMovement::TYPE_IN, $quantity, $reason);
    }

    public function stockOut(Product $product, User $user, int $quantity, ?string $reason = null): StockMovement
    {
        return $this->apply($product, $user, StockMovement::TYPE_OUT, $quantity, $reason);
    }

    /** Set the counted quantity; the movement stores the absolute difference. */
    public function adjust(Product $product, User $user, int $newQuantity, string $reason): StockMovement
    {
        return $this->apply($product, $user, StockMovement::TYPE_ADJUSTMENT, $newQuantity, $reason);
    }

    /**
     * Dispatch by type. For IN/OUT $value is the movement quantity;
     * for ADJUSTMENT it is the new (counted) stock level.
     */
    public function record(Product $product, User $user, string $type, int $value, ?string $reason = null): StockMovement
    {
        return $this->apply($product, $user, $type, $value, $reason);
    }

    private function apply(Product $product, User $user, string $type, int $value, ?string $reason): StockMovement
    {
        if (! in_array($type, StockMovement::TYPES, true)) {
            throw new StockException('Invalid stock movement type.');
        }

        $reason = $reason !== null ? trim($reason) : null;

        if ($type === StockMovement::TYPE_ADJUSTMENT && ($reason === null || $reason === '')) {
            throw new StockException('A reason is required for stock adjustments.');
        }

        return DB::transaction(function () use ($product, $user, $type, $value, $reason) {
            // Lock the row so concurrent operations serialize on this product.
            $locked = Product::query()->whereKey($product->getKey())->lockForUpdate()->first();

            if (! $locked) {
                throw new StockException('This product no longer exists or has been archived.');
            }

            // Authorization is re-checked against the locked, current row.
            Gate::forUser($user)->authorize('manageStock', $locked);

            $previous = $locked->quantity;

            [$new, $difference] = match ($type) {
                StockMovement::TYPE_IN => [$previous + $this->positive($value), $value],
                StockMovement::TYPE_OUT => [$previous - $this->positive($value), $value],
                StockMovement::TYPE_ADJUSTMENT => [$value, abs($value - $previous)],
            };

            if ($new < 0) {
                throw new StockException(
                    "Insufficient stock: only {$previous} unit(s) of {$locked->name} available."
                );
            }

            if ($new > self::MAX_QUANTITY) {
                throw new StockException('The resulting quantity is too large.');
            }

            if ($difference === 0) {
                throw new StockException('The adjustment does not change the current quantity.');
            }

            $locked->quantity = $new;
            $locked->save();

            $movement = new StockMovement;
            $movement->product_id = $locked->id;
            $movement->user_id = $user->id;
            $movement->type = $type;
            $movement->quantity = $difference;
            $movement->previous_quantity = $previous;
            $movement->new_quantity = $new;
            $movement->reason = $reason === '' ? null : $reason;
            $movement->save();

            // Keep the caller's instance in sync with the committed state.
            $product->quantity = $new;

            return $movement;
        });
    }

    private function positive(int $quantity): int
    {
        if ($quantity < 1) {
            throw new StockException('Quantity must be a positive whole number.');
        }

        return $quantity;
    }
}
