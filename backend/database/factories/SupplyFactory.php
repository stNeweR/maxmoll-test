<?php

namespace Database\Factories;

use App\Models\Supply;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Supply>
 */
class SupplyFactory extends Factory
{
    protected $model = Supply::class;

    /**
     * Определяет исходное состояние поставки.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'warehouse_id' => Warehouse::factory(),
        ];
    }
}
