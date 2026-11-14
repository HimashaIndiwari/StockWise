<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        $purchase = fake()->randomFloat(2, 1, 500);

        return [
            'name' => ucfirst(fake()->words(3, true)),
            'sku' => strtoupper(fake()->unique()->bothify('SKU-####-??')),
            'description' => fake()->optional()->sentence(),
            'category_id' => Category::factory(),
            'supplier_id' => Supplier::factory(),
            'created_by' => User::factory(),
            'purchase_price' => $purchase,
            'selling_price' => round($purchase * 1.3, 2),
            'quantity' => fake()->numberBetween(0, 100),
            'min_stock_level' => 5,
        ];
    }

    public function ownedBy(User $user): static
    {
        return $this->state(fn () => ['created_by' => $user->id]);
    }

    public function quantity(int $quantity): static
    {
        return $this->state(fn () => ['quantity' => $quantity]);
    }
}
