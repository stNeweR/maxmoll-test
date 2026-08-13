<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Supply;
use App\Models\SupplyItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SupplyItem>
 */
class SupplyItemFactory extends Factory
{
    protected $model = SupplyItem::class;

    /**
     * Определяет исходное состояние позиции поставки.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'supply_id' => Supply::factory(),
            'product_id' => Product::factory(),
            'count' => fake()->numberBetween(1, 50),
        ];
    }
}
