<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Admin-only category management (routes are in the role:admin group). */
class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->validate(['q' => ['nullable', 'string', 'max:100']])['q'] ?? null;

        $categories = Category::query()
            ->withCount('products')
            ->when($search, fn ($q, $term) => $q->where('name', 'like', '%'.addcslashes($term, '%_\\').'%'))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('categories.index', compact('categories', 'search'));
    }

    public function create(): View
    {
        return view('categories.create', ['category' => new Category]);
    }

    public function store(CategoryRequest $request): RedirectResponse
    {
        $category = Category::create($request->validated());

        return redirect()->route('admin.categories.index')
            ->with('success', "Category \"{$category->name}\" created.");
    }

    public function show(Category $category): View
    {
        $products = $category->products()->with('supplier')->orderBy('name')->paginate(10);

        return view('categories.show', compact('category', 'products'));
    }

    public function edit(Category $category): View
    {
        return view('categories.edit', compact('category'));
    }

    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        $category->update($request->validated());

        return redirect()->route('admin.categories.index')
            ->with('success', "Category \"{$category->name}\" updated.");
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->isInUse()) {
            return back()->with('error', "\"{$category->name}\" cannot be deleted because products (including archived ones) are assigned to it.");
        }

        try {
            $category->delete();
        } catch (QueryException) {
            // FK RESTRICT is the final safeguard (e.g. a product was assigned concurrently).
            return back()->with('error', "\"{$category->name}\" is in use and cannot be deleted.");
        }

        return redirect()->route('admin.categories.index')
            ->with('success', "Category \"{$category->name}\" deleted.");
    }
}
