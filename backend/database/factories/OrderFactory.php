<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    /**
     * Определяет исходное состояние заказа.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'warehouse_id' => Warehouse::factory(),
            'status' => Order::STATUS_ACTIVE,
            'completed_at' => null,
        ];
    }
}
