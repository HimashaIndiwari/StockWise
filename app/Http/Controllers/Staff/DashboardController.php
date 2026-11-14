<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Staff dashboard: every figure is scoped to products the user created.
 */
class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $mine = fn () => Product::visibleTo($user);

        $stats = [
            'products' => $mine()->count(),
            'stock_quantity' => (int) $mine()->sum('quantity'),
            'low_stock' => $mine()->withStockStatus(Product::STATUS_LOW_STOCK)->count(),
            'out_of_stock' => $mine()->withStockStatus(Product::STATUS_OUT_OF_STOCK)->count(),
        ];

        $recentMovements = StockMovement::visibleTo($user)
            ->with(['product', 'user'])
            ->latest('id')
            ->limit(8)
            ->get();

        $attentionProducts = $mine()
            ->with('category')
            ->where(fn ($q) => $q->where('quantity', 0)->orWhereColumn('quantity', '<=', 'min_stock_level'))
            ->orderBy('quantity')
            ->limit(6)
            ->get();

        return view('staff.dashboard', compact('stats', 'recentMovements', 'attentionProducts'));
    }
}
