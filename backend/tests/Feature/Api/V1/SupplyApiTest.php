<?php

namespace Tests\Feature\Api\V1;

use App\Models\Movement;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Supply;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature-тесты REST-эндпоинтов поставок (v1).
 */
class SupplyApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Создание поставки увеличивает остаток на складе.
     */
    public function test_store_increases_stock(): void
    {
        $warehouse = Warehouse::factory()->create();
        $product = Product::factory()->create();
        Stock::factory()->create([
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'stock' => 20,
        ]);

        $response = $this->postJson('/api/v1/supplies', [
            'warehouse_id' => $warehouse->id,
            'items' => [
                ['product_id' => $product->id, 'count' => 30],
            ],
        ]);

        $response->assertCreated()
            ->assertJsonCount(1, 'data.items');

        // Остаток вырос с 20 до 50.
        $this->assertDatabaseHas('stocks', [
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'stock' => 50,
        ]);
    }

    /**
     * Создание поставки фиксирует положительное движение.
     */
    public function test_store_creates_positive_movement(): void
    {
        $warehouse = Warehouse::factory()->create();
        $product = Product::factory()->create();
        Stock::factory()->create([
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'stock' => 0,
        ]);

        $supply = $this->postJson('/api/v1/supplies', [
            'warehouse_id' => $warehouse->id,
            'items' => [['product_id' => $product->id, 'count' => 10]],
        ])->json('data');

        $this->assertDatabaseHas('movements', [
            'doc_type' => Supply::class,
            'doc_id' => $supply['id'],
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'quantity' => 10,
        ]);
    }

    /**
     * Поставка на несуществующий склад отклоняется.
     */
    public function test_store_rejects_non_existent_warehouse(): void
    {
        $product = Product::factory()->create();

        $response = $this->postJson('/api/v1/supplies', [
            'warehouse_id' => 99999,
            'items' => [['product_id' => $product->id, 'count' => 1]],
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('warehouse_id');
    }

    /**
     * Поставка без позиций отклоняется.
     */
    public function test_store_requires_at_least_one_item(): void
    {
        $warehouse = Warehouse::factory()->create();

        $response = $this->postJson('/api/v1/supplies', [
            'warehouse_id' => $warehouse->id,
            'items' => [],
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('items');
    }
}