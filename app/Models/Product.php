<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

/**
 * `created_by` and `quantity` are intentionally NOT fillable:
 * the creator is assigned on the server and quantity only changes through StockService.
 */
#[Fillable([
    'name', 'sku', 'description', 'category_id', 'supplier_id',
    'purchase_price', 'selling_price', 'min_stock_level', 'image',
])]
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory, SoftDeletes;

    public const STATUS_IN_STOCK = 'in_stock';

    public const STATUS_LOW_STOCK = 'low_stock';

    public const STATUS_OUT_OF_STOCK = 'out_of_stock';

    public const STATUSES = [
        self::STATUS_IN_STOCK => 'In stock',
        self::STATUS_LOW_STOCK => 'Low stock',
        self::STATUS_OUT_OF_STOCK => 'Out of stock',
    ];

    protected $attributes = [
        'quantity' => 0,
    ];

    protected function casts(): array
    {
        return [
            'purchase_price' => 'decimal:2',
            'selling_price' => 'decimal:2',
            'quantity' => 'integer',
            'min_stock_level' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function imageUrl(): ?string
    {
        return $this->image ? Storage::disk('public')->url($this->image) : null;
    }

    /**
     * Single definition of stock status (plan §13):
     * out of stock = 0; low stock = 0 < qty <= min; in stock = qty > min.
     */
    public function stockStatus(): string
    {
        if ($this->quantity === 0) {
            return self::STATUS_OUT_OF_STOCK;
        }

        return $this->quantity <= $this->min_stock_level
            ? self::STATUS_LOW_STOCK
            : self::STATUS_IN_STOCK;
    }

    /** Products the given user may see: everything for admin, own products for staff. */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return $user->isAdmin() ? $query : $query->where('created_by', $user->id);
    }

    /** Same status definition as stockStatus(), expressed in SQL. */
    public function scopeWithStockStatus(Builder $query, string $status): Builder
    {
        return match ($status) {
            self::STATUS_OUT_OF_STOCK => $query->where('quantity', 0),
            self::STATUS_LOW_STOCK => $query->where('quantity', '>', 0)->whereColumn('quantity', '<=', 'min_stock_level'),
            self::STATUS_IN_STOCK => $query->whereColumn('quantity', '>', 'min_stock_level'),
            default => $query,
        };
    }
}
