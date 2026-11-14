<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Test helper only. Real movements are created through StockService.
 *
 * @extends Factory<StockMovement>
 */
class StockMovementFactory extends Factory
{
    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 20);

        return [
            'product_id' => Product::factory(),
            'user_id' => User::factory(),
            'type' => StockMovement::TYPE_IN,
            'quantity' => $quantity,
            'previous_quantity' => 0,
            'new_quantity' => $quantity,
            'reason' => null,
        ];
    }
}
