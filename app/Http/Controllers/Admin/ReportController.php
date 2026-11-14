<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Admin inventory reports (plan §6 Module 8): low stock, out of stock,
 * stock movement summary (IN/OUT/adjustments) for a date range.
 * All figures are computed by database queries.
 */
class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $from = Carbon::parse($validated['from'] ?? now()->subDays(29))->startOfDay();
        $to = Carbon::parse($validated['to'] ?? now())->endOfDay();

        $inRange = fn () => StockMovement::query()->whereBetween('created_at', [$from, $to]);

        // Adjustments are split into increases and decreases using previous/new quantity.
        $summary = [
            'in_units' => (int) $inRange()->where('type', StockMovement::TYPE_IN)->sum('quantity'),
            'in_count' => $inRange()->where('type', StockMovement::TYPE_IN)->count(),
            'out_units' => (int) $inRange()->where('type', StockMovement::TYPE_OUT)->sum('quantity'),
            'out_count' => $inRange()->where('type', StockMovement::TYPE_OUT)->count(),
            'adjust_up' => (int) $inRange()->where('type', StockMovement::TYPE_ADJUSTMENT)->whereColumn('new_quantity', '>', 'previous_quantity')->sum('quantity'),
            'adjust_down' => (int) $inRange()->where('type', StockMovement::TYPE_ADJUSTMENT)->whereColumn('new_quantity', '<', 'previous_quantity')->sum('quantity'),
            'adjust_count' => $inRange()->where('type', StockMovement::TYPE_ADJUSTMENT)->count(),
        ];
        $summary['net_change'] = $summary['in_units'] + $summary['adjust_up'] - $summary['out_units'] - $summary['adjust_down'];

        $inventory = [
            'products' => Product::count(),
            'units' => (int) Product::sum('quantity'),
            'value' => (float) Product::sum(DB::raw('quantity * purchase_price')),
        ];

        $topMovers = StockMovement::query()
            ->whereBetween('created_at', [$from, $to])
            ->select('product_id')
            ->selectRaw("SUM(CASE WHEN type = 'IN' THEN quantity ELSE 0 END) as in_units")
            ->selectRaw("SUM(CASE WHEN type = 'OUT' THEN quantity ELSE 0 END) as out_units")
            ->selectRaw('COUNT(*) as movements')
            ->groupBy('product_id')
            ->orderByDesc('movements')
            ->limit(10)
            ->with('product')
            ->get();

        $lowStock = Product::withStockStatus(Product::STATUS_LOW_STOCK)
            ->with(['category', 'creator'])
            ->orderBy('quantity')
            ->get();

        $outOfStock = Product::withStockStatus(Product::STATUS_OUT_OF_STOCK)
            ->with(['category', 'creator'])
            ->orderBy('name')
            ->get();

        return view('reports.index', compact('from', 'to', 'summary', 'inventory', 'topMovers', 'lowStock', 'outOfStock'));
    }
}
