<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Transfer;
use App\Models\TransferItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TransferItem>
 */
class TransferItemFactory extends Factory
{
    protected $model = TransferItem::class;

    /**
     * Определяет исходное состояние позиции перемещения.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'transfer_id' => Transfer::factory(),
            'product_id' => Product::factory(),
            'count' => fake()->numberBetween(1, 30),
        ];
    }
}
