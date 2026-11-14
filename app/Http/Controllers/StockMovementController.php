<?php

namespace App\Http\Controllers;

use App\Exceptions\StockException;
use App\Http\Requests\StoreStockMovementRequest;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use App\Services\StockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * Stock movement history and recording, shared by the Admin and Staff areas.
 * Movements are audit records: there is deliberately no edit/update/delete.
 */
class StockMovementController extends Controller
{
    public function __construct(private readonly StockService $stock) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'product_id' => ['nullable', 'integer'],
            'type' => ['nullable', 'string', 'in:'.implode(',', StockMovement::TYPES)],
            'user_id' => ['nullable', 'integer'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $movements = StockMovement::query()
            ->visibleTo($user)
            ->with(['product', 'user'])
            ->when($filters['product_id'] ?? null, fn ($q, $id) => $q->where('product_id', $id))
            ->when($filters['q'] ?? null, function ($query, string $term) {
                $like = '%'.addcslashes($term, '%_\\').'%';
                $query->whereHas('product', fn ($p) => $p->withTrashed()
                    ->where(fn ($w) => $w->where('name', 'like', $like)->orWhere('sku', 'like', $like)));
            })
            ->when($filters['type'] ?? null, fn ($q, $type) => $q->where('type', $type))
            ->when($user->isAdmin() ? ($filters['user_id'] ?? null) : null, fn ($q, $id) => $q->where('user_id', $id))
            ->when($filters['from'] ?? null, fn ($q, $from) => $q->whereDate('created_at', '>=', $from))
            ->when($filters['to'] ?? null, fn ($q, $to) => $q->whereDate('created_at', '<=', $to))
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $filteredProduct = isset($filters['product_id'])
            ? Product::withTrashed()->visibleTo($user)->find($filters['product_id'])
            : null;

        return view('stock.index', [
            'movements' => $movements,
            'filters' => $filters,
            'filteredProduct' => $filteredProduct,
            'users' => $user->isAdmin() ? User::orderBy('name')->pluck('name', 'id') : collect(),
        ]);
    }

    public function create(Request $request): View
    {
        $user = $request->user();

        $products = Product::query()
            ->visibleTo($user)
            ->orderBy('name')
            ->get(['id', 'name', 'sku', 'quantity']);

        return view('stock.create', [
            'products' => $products,
            'selectedProductId' => $request->integer('product_id') ?: null,
        ]);
    }

    public function store(StoreStockMovementRequest $request): RedirectResponse
    {
        $user = $request->user();
        $product = Product::findOrFail($request->validated('product_id'));

        // Staff may only record movements for their own products (crafted requests get 403).
        Gate::authorize('manageStock', $product);

        try {
            $movement = $this->stock->record(
                $product,
                $user,
                $request->validated('type'),
                (int) $request->validated('quantity'),
                $request->validated('reason'),
            );
        } catch (StockException $e) {
            return back()->withInput()->withErrors(['quantity' => $e->getMessage()]);
        }

        $change = $movement->signedChange();

        return redirect()->to(area_route('products.show', $product))->with('success', sprintf(
            'Stock %s recorded for "%s": %s%d (now %d in stock).',
            $movement->type === StockMovement::TYPE_ADJUSTMENT ? 'adjustment' : $movement->type,
            $product->name,
            $change > 0 ? '+' : '',
            $change,
            $movement->new_quantity,
        ));
    }
}
