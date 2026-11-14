<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\View\View;

/**
 * System-wide inventory dashboard. All figures come from the database.
 */
class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'products' => Product::count(),
            'categories' => Category::count(),
            'suppliers' => Supplier::count(),
            'users' => User::count(),
            'stock_quantity' => (int) Product::sum('quantity'),
            'low_stock' => Product::withStockStatus(Product::STATUS_LOW_STOCK)->count(),
            'out_of_stock' => Product::withStockStatus(Product::STATUS_OUT_OF_STOCK)->count(),
        ];

        $recentMovements = StockMovement::with(['product', 'user'])
            ->latest('id')
            ->limit(8)
            ->get();

        $attentionProducts = Product::with('category')
            ->where(fn ($q) => $q->where('quantity', 0)->orWhereColumn('quantity', '<=', 'min_stock_level'))
            ->orderBy('quantity')
            ->limit(6)
            ->get();

        $stockByCategory = Category::withSum('products as stock_total', 'quantity')
            ->orderByDesc('stock_total')
            ->limit(6)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentMovements', 'attentionProducts', 'stockByCategory'));
    }
}
