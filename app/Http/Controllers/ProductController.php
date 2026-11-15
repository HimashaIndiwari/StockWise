<?php

namespace App\Http\Controllers;

use App\Exceptions\StockException;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use App\Services\StockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

/**
 * Product CRUD shared by the Admin (/admin/products) and Staff (/staff/products) areas.
 * Listing is scoped with Product::visibleTo(); every record action is authorized by ProductPolicy.
 */
class ProductController extends Controller
{
    private const SORTABLE = ['name', 'sku', 'purchase_price', 'selling_price', 'quantity', 'created_at'];

    public function __construct(private readonly StockService $stock) {}

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Product::class);

        $user = $request->user();
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'category_id' => ['nullable', 'integer'],
            'supplier_id' => ['nullable', 'integer'],
            'status' => ['nullable', 'string', 'in:'.implode(',', array_keys(Product::STATUSES))],
            'created_by' => ['nullable', 'integer'],
            'sort' => ['nullable', 'string', 'in:'.implode(',', self::SORTABLE)],
            'direction' => ['nullable', 'string', 'in:asc,desc'],
        ]);

        $sort = $filters['sort'] ?? 'created_at';
        $direction = $filters['direction'] ?? ($sort === 'created_at' ? 'desc' : 'asc');

        $products = Product::query()
            ->visibleTo($user)
            ->with(['category', 'supplier', 'creator'])
            ->when($filters['q'] ?? null, function ($query, string $term) {
                $like = '%'.str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $term).'%';
                $query->where(fn ($q) => $q->where('name', 'like', $like)->orWhere('sku', 'like', $like));
            })
            ->when($filters['category_id'] ?? null, fn ($q, $id) => $q->where('category_id', $id))
            ->when($filters['supplier_id'] ?? null, fn ($q, $id) => $q->where('supplier_id', $id))
            ->when($filters['status'] ?? null, fn ($q, $status) => $q->withStockStatus($status))
            ->when($user->isAdmin() ? ($filters['created_by'] ?? null) : null, fn ($q, $id) => $q->where('created_by', $id))
            ->orderBy($sort, $direction)
            ->orderBy('id', $direction)
            ->paginate(10)
            ->withQueryString();

        return view('products.index', [
            'products' => $products,
            'filters' => $filters,
            'sort' => $sort,
            'direction' => $direction,
            'categories' => Category::orderBy('name')->pluck('name', 'id'),
            'suppliers' => Supplier::orderBy('name')->pluck('name', 'id'),
            'creators' => $user->isAdmin() ? User::orderBy('name')->pluck('name', 'id') : collect(),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Product::class);

        return view('products.create', $this->formOptions() + ['product' => new Product]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->safe()->except(['image', 'quantity']);
        $initialQuantity = (int) ($request->validated('quantity') ?? 0);
        $imagePath = $request->hasFile('image') ? $request->file('image')->store('products', 'public') : null;

        try {
            $product = DB::transaction(function () use ($data, $user, $initialQuantity, $imagePath) {
                $product = new Product($data);
                $product->image = $imagePath;
                $product->created_by = $user->id; // server-assigned, never from input
                $product->quantity = 0;
                $product->save();

                // Initial stock is recorded as an auditable IN movement.
                if ($initialQuantity > 0) {
                    $this->stock->stockIn($product, $user, $initialQuantity, 'Initial stock');
                }

                return $product;
            });
        } catch (Throwable $e) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }

            if ($e instanceof StockException) {
                return back()->withInput()->withErrors(['quantity' => $e->getMessage()]);
            }

            throw $e;
        }

        return redirect()->to(area_route('products.show', $product))
            ->with('success', "Product \"{$product->name}\" created successfully.");
    }

    public function show(Product $product): View
    {
        Gate::authorize('view', $product);

        $product->load(['category', 'supplier', 'creator']);
        $movements = $product->stockMovements()->with('user')->latest('id')->limit(10)->get();

        return view('products.show', compact('product', 'movements'));
    }

    public function edit(Product $product): View
    {
        Gate::authorize('update', $product);

        return view('products.edit', $this->formOptions() + ['product' => $product]);
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $oldImage = $product->image;
        $newImage = $request->hasFile('image') ? $request->file('image')->store('products', 'public') : null;

        $product->fill($request->safe()->except(['image', 'remove_image']));

        if ($newImage) {
            $product->image = $newImage;
        } elseif ($request->boolean('remove_image')) {
            $product->image = null;
        }

        try {
            $product->save();
        } catch (Throwable $e) {
            if ($newImage) {
                Storage::disk('public')->delete($newImage);
            }
            throw $e;
        }

        // Remove the replaced/removed file only after the record was saved.
        if ($oldImage && $oldImage !== $product->image) {
            Storage::disk('public')->delete($oldImage);
        }

        return redirect()->to(area_route('products.show', $product))
            ->with('success', "Product \"{$product->name}\" updated successfully.");
    }

    /**
     * Permanent delete, regardless of stock. The product's stock movements are
     * removed with it (the FK restricts deletes), so its history is lost.
     */
    public function destroy(Product $product): RedirectResponse
    {
        Gate::authorize('delete', $product);

        DB::transaction(function () use ($product) {
            $product->stockMovements()->delete();
            $product->forceDelete();
        });

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        return redirect()->to(area_route('products.index'))
            ->with('success', "Product \"{$product->name}\" deleted permanently.");
    }

    /** Admin-only list of archived (soft-deleted) products. */
    public function archived(Request $request): View
    {
        Gate::authorize('restore', new Product);

        $search = $request->validate(['q' => ['nullable', 'string', 'max:100']])['q'] ?? null;

        $products = Product::onlyTrashed()
            ->with(['category', 'creator'])
            ->when($search, function ($query, string $term) {
                $like = '%'.addcslashes($term, '%_\\').'%';
                $query->where(fn ($q) => $q->where('name', 'like', $like)->orWhere('sku', 'like', $like));
            })
            ->latest('deleted_at')
            ->paginate(10)
            ->withQueryString();

        return view('products.archived', compact('products', 'search'));
    }

    /** Admin-only: bring an archived product back into normal listings. */
    public function restore(int $productId): RedirectResponse
    {
        $product = Product::onlyTrashed()->findOrFail($productId);
        Gate::authorize('restore', $product);

        $product->restore();

        return redirect()->route('admin.products.show', $product)
            ->with('success', "Product \"{$product->name}\" restored.");
    }

    private function formOptions(): array
    {
        return [
            'categories' => Category::orderBy('name')->pluck('name', 'id'),
            'suppliers' => Supplier::orderBy('name')->pluck('name', 'id'),
        ];
    }
}
