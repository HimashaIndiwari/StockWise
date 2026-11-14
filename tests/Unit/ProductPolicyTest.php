<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Models\User;
use App\Policies\ProductPolicy;
use PHPUnit\Framework\TestCase;

class ProductPolicyTest extends TestCase
{
    private function user(int $id, string $role): User
    {
        $user = new User;
        $user->id = $id;
        $user->role = $role;

        return $user;
    }

    private function productOwnedBy(int $userId): Product
    {
        $product = new Product;
        $product->created_by = $userId;

        return $product;
    }

    public function test_staff_can_manage_own_product(): void
    {
        $policy = new ProductPolicy;
        $staff = $this->user(1, User::ROLE_STAFF);
        $product = $this->productOwnedBy(1);

        $this->assertTrue($policy->view($staff, $product));
        $this->assertTrue($policy->update($staff, $product));
        $this->assertTrue($policy->delete($staff, $product));
        $this->assertTrue($policy->manageStock($staff, $product));
    }

    public function test_staff_cannot_touch_another_users_product(): void
    {
        $policy = new ProductPolicy;
        $staff = $this->user(1, User::ROLE_STAFF);
        $product = $this->productOwnedBy(2);

        $this->assertFalse($policy->view($staff, $product));
        $this->assertFalse($policy->update($staff, $product));
        $this->assertFalse($policy->delete($staff, $product));
        $this->assertFalse($policy->manageStock($staff, $product));
        $this->assertFalse($policy->restore($staff, $product));
    }

    public function test_admin_can_manage_every_product(): void
    {
        $policy = new ProductPolicy;
        $admin = $this->user(9, User::ROLE_ADMIN);
        $product = $this->productOwnedBy(2);

        $this->assertTrue($policy->view($admin, $product));
        $this->assertTrue($policy->update($admin, $product));
        $this->assertTrue($policy->delete($admin, $product));
        $this->assertTrue($policy->manageStock($admin, $product));
        $this->assertTrue($policy->restore($admin, $product));
    }

    public function test_nobody_can_force_delete(): void
    {
        $policy = new ProductPolicy;

        $this->assertFalse($policy->forceDelete($this->user(9, User::ROLE_ADMIN), $this->productOwnedBy(9)));
    }
}
