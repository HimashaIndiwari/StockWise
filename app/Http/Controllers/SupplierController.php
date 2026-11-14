<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierRequest;
use App\Models\Supplier;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Admin-only supplier management (routes are in the role:admin group). */
class SupplierController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->validate(['q' => ['nullable', 'string', 'max:100']])['q'] ?? null;

        $suppliers = Supplier::query()
            ->withCount('products')
            ->when($search, function ($query, string $term) {
                $like = '%'.addcslashes($term, '%_\\').'%';
                $query->where(fn ($q) => $q->where('name', 'like', $like)
                    ->orWhere('email', 'like', $like)
                    ->orWhere('phone', 'like', $like));
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('suppliers.index', compact('suppliers', 'search'));
    }

    public function create(): View
    {
        return view('suppliers.create', ['supplier' => new Supplier]);
    }

    public function store(SupplierRequest $request): RedirectResponse
    {
        $supplier = Supplier::create($request->validated());

        return redirect()->route('admin.suppliers.show', $supplier)
            ->with('success', "Supplier \"{$supplier->name}\" created.");
    }

    public function show(Supplier $supplier): View
    {
        $products = $supplier->products()->with('category')->orderBy('name')->paginate(10);

        return view('suppliers.show', compact('supplier', 'products'));
    }

    public function edit(Supplier $supplier): View
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(SupplierRequest $request, Supplier $supplier): RedirectResponse
    {
        $supplier->update($request->validated());

        return redirect()->route('admin.suppliers.show', $supplier)
            ->with('success', "Supplier \"{$supplier->name}\" updated.");
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        if ($supplier->isInUse()) {
            return back()->with('error', "\"{$supplier->name}\" cannot be deleted because products (including archived ones) are assigned to it.");
        }

        try {
            $supplier->delete();
        } catch (QueryException) {
            return back()->with('error', "\"{$supplier->name}\" is in use and cannot be deleted.");
        }

        return redirect()->route('admin.suppliers.index')
            ->with('success', "Supplier \"{$supplier->name}\" deleted.");
    }
}
