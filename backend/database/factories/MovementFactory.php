<?php

namespace Database\Factories;

use App\Models\Movement;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Movement>
 */
class MovementFactory extends Factory
{
    protected $model = Movement::class;

    /**
     * Определяет исходное состояние записи движения товара.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'doc_type' => 'App\\Models\\Product',
            'doc_id' => Product::factory(),
            'product_id' => Product::factory(),
            'warehouse_id' => Warehouse::factory(),
            'quantity' => fake()->numberBetween(-50, 50),
            'created_at' => now(),
        ];
    }
}
