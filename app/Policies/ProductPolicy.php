<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

/**
 * Record-level authorization for products (plan §3.3).
 * Admin: every product. Staff: only products they created.
 */
class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // listings are scoped with Product::visibleTo()
    }

    public function view(User $user, Product $product): bool
    {
        return $this->ownsOrAdmin($user, $product);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Product $product): bool
    {
        return $this->ownsOrAdmin($user, $product);
    }

    public function delete(User $user, Product $product): bool
    {
        return $this->ownsOrAdmin($user, $product);
    }

    /** Record stock IN / OUT / ADJUSTMENT movements for the product. */
    public function manageStock(User $user, Product $product): bool
    {
        return $this->ownsOrAdmin($user, $product) && ! $product->trashed();
    }

    public function restore(User $user, Product $product): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Product $product): bool
    {
        return false; // products are only ever archived (soft-deleted)
    }

    private function ownsOrAdmin(User $user, Product $product): bool
    {
        return $user->isAdmin() || $product->created_by === $user->id;
    }
}
