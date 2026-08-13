<?php

namespace Tests\Feature\Api\V1;

use App\Models\Customer;
use App\Models\Movement;
use App\Models\Order;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature-тесты REST-эндпоинтов заказов (v1).
 */
class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Вспомогательный метод: создаёт товар с достаточным остатком на складе.
     *
     * @return Product товар с остатком
     */
    private function makeProductWithStock(Warehouse $warehouse, int $stock = 100): Product
    {
        $product = Product::factory()->create();
        Stock::factory()->create([
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'stock' => $stock,
        ]);

        return $product;
    }

    /**
     * Создание заказа резервирует товары и фиксирует движение.
     */
    public function test_store_creates_order_and_reserves_stock(): void
    {
        $customer = Customer::factory()->create();
        $warehouse = Warehouse::factory()->create();
        $product = $this->makeProductWithStock($warehouse, 100);

        $response = $this->postJson('/api/v1/orders', [
            'customer_id' => $customer->id,
            'warehouse_id' => $warehouse->id,
            'items' => [
                ['product_id' => $product->id, 'count' => 10],
            ],
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.status', Order::STATUS_ACTIVE)
            ->assertJsonCount(1, 'data.items');

        // Остаток уменьшился со 100 до 90.
        $this->assertDatabaseHas('stocks', [
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'stock' => 90,
        ]);

        // Зафиксировано движение -10.
        $this->assertDatabaseHas('movements', [
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'quantity' => -10,
        ]);
    }

    /**
     * Создание заказа несуществующим клиентом отклоняется.
     */
    public function test_store_rejects_non_existent_customer(): void
    {
        $warehouse = Warehouse::factory()->create();
        $product = $this->makeProductWithStock($warehouse);

        $response = $this->postJson('/api/v1/orders', [
            'customer_id' => 99999,
            'warehouse_id' => $warehouse->id,
            'items' => [['product_id' => $product->id, 'count' => 1]],
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('customer_id');
    }

    /**
     * Просмотр заказа возвращает его с позициями.
     */
    public function test_show_returns_order_with_items(): void
    {
        $customer = Customer::factory()->create();
        $warehouse = Warehouse::factory()->create();
        $product = $this->makeProductWithStock($warehouse);

        $order = $this->postJson('/api/v1/orders', [
            'customer_id' => $customer->id,
            'warehouse_id' => $warehouse->id,
            'items' => [['product_id' => $product->id, 'count' => 2]],
        ])->json('data');

        $response = $this->getJson("/api/v1/orders/{$order['id']}");

        $response->assertOk()
            ->assertJsonPath('data.id', $order['id'])
            ->assertJsonCount(1, 'data.items');
    }

    /**
     * Обновление заказа меняет состав и пересчитывает остатки.
     */
    public function test_update_changes_items_and_stock(): void
    {
        $customer = Customer::factory()->create();
        $warehouse = Warehouse::factory()->create();
        $product = $this->makeProductWithStock($warehouse, 100);

        $order = $this->postJson('/api/v1/orders', [
            'customer_id' => $customer->id,
            'warehouse_id' => $warehouse->id,
            'items' => [['product_id' => $product->id, 'count' => 10]],
        ])->json('data');

        $response = $this->putJson("/api/v1/orders/{$order['id']}", [
            'customer_id' => $customer->id,
            'warehouse_id' => $warehouse->id,
            'items' => [['product_id' => $product->id, 'count' => 20]],
        ]);

        $response->assertOk()
            ->assertJsonPath('data.items.0.count', 20);

        // Сначала вернули 10, затем списали 20: итог 90 - 20 + 10 = 80.
        $this->assertDatabaseHas('stocks', [
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'stock' => 80,
        ]);
    }

    /**
     * Обновление выполненного заказа запрещено.
     */
    public function test_update_rejects_completed_order(): void
    {
        $customer = Customer::factory()->create();
        $warehouse = Warehouse::factory()->create();
        $product = $this->makeProductWithStock($warehouse, 100);

        $order = $this->postJson('/api/v1/orders', [
            'customer_id' => $customer->id,
            'warehouse_id' => $warehouse->id,
            'items' => [['product_id' => $product->id, 'count' => 10]],
        ])->json('data');

        $this->postJson("/api/v1/orders/{$order['id']}/complete")->assertOk();

        $this->putJson("/api/v1/orders/{$order['id']}", [
            'customer_id' => $customer->id,
            'warehouse_id' => $warehouse->id,
            'items' => [['product_id' => $product->id, 'count' => 5]],
        ])->assertStatus(422);
    }

    /**
     * Завершение заказа меняет статус на completed.
     */
    public function test_complete_sets_status_completed(): void
    {
        $customer = Customer::factory()->create();
        $warehouse = Warehouse::factory()->create();
        $product = $this->makeProductWithStock($warehouse, 100);

        $order = $this->postJson('/api/v1/orders', [
            'customer_id' => $customer->id,
            'warehouse_id' => $warehouse->id,
            'items' => [['product_id' => $product->id, 'count' => 10]],
        ])->json('data');

        $response = $this->postJson("/api/v1/orders/{$order['id']}/complete");

        $response->assertOk()
            ->assertJsonPath('data.status', Order::STATUS_COMPLETED)
            ->assertJsonPath('data.completed_at', fn ($v) => $v !== null);
    }

    /**
     * Отмена заказа возвращает товары на остаток.
     */
    public function test_cancel_returns_stock(): void
    {
        $customer = Customer::factory()->create();
        $warehouse = Warehouse::factory()->create();
        $product = $this->makeProductWithStock($warehouse, 100);

        $order = $this->postJson('/api/v1/orders', [
            'customer_id' => $customer->id,
            'warehouse_id' => $warehouse->id,
            'items' => [['product_id' => $product->id, 'count' => 10]],
        ])->json('data');

        $response = $this->postJson("/api/v1/orders/{$order['id']}/cancel");

        $response->assertOk()
            ->assertJsonPath('data.status', Order::STATUS_CANCELED);

        // Товар вернулся на остаток (90 + 10 = 100).
        $this->assertDatabaseHas('stocks', [
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'stock' => 100,
        ]);

        // Зафиксировано возвратное движение +10.
        $this->assertDatabaseHas('movements', [
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'quantity' => 10,
        ]);
    }

    /**
     * Возобновление отменённого заказа снова списывает товары.
     */
    public function test_revive_reserves_stock_again(): void
    {
        $customer = Customer::factory()->create();
        $warehouse = Warehouse::factory()->create();
        $product = $this->makeProductWithStock($warehouse, 100);

        $order = $this->postJson('/api/v1/orders', [
            'customer_id' => $customer->id,
            'warehouse_id' => $warehouse->id,
            'items' => [['product_id' => $product->id, 'count' => 10]],
        ])->json('data');

        $this->postJson("/api/v1/orders/{$order['id']}/cancel")->assertOk();

        $response = $this->postJson("/api/v1/orders/{$order['id']}/revive");

        $response->assertOk()
            ->assertJsonPath('data.status', Order::STATUS_ACTIVE);

        // После revive остаток снова уменьшился (100 - 10 = 90).
        $this->assertDatabaseHas('stocks', [
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'stock' => 90,
        ]);
    }

    /**
     * Возобновление заказа без достаточного остатка завершается ошибкой.
     */
    public function test_revive_fails_when_no_stock_available(): void
    {
        $customer = Customer::factory()->create();
        $warehouse = Warehouse::factory()->create();
        $product = $this->makeProductWithStock($warehouse, 10);

        $order = $this->postJson('/api/v1/orders', [
            'customer_id' => $customer->id,
            'warehouse_id' => $warehouse->id,
            'items' => [['product_id' => $product->id, 'count' => 10]],
        ])->json('data');

        $this->postJson("/api/v1/orders/{$order['id']}/cancel")->assertOk();

        // Товары "ушли" на другой заказ — остаток исчерпан.
        Stock::query()->where('product_id', $product->id)
            ->where('warehouse_id', $warehouse->id)
            ->update(['stock' => 0]);

        $response = $this->postJson("/api/v1/orders/{$order['id']}/revive");

        $response->assertStatus(422);
        $this->assertDatabaseHas('orders', [
            'id' => $order['id'],
            'status' => Order::STATUS_CANCELED,
        ]);
    }

    /**
     * Список заказов фильтруется по статусу.
     */
    public function test_index_filters_by_status(): void
    {
        Order::factory()->create(['status' => Order::STATUS_COMPLETED]);
        Order::factory()->create(['status' => Order::STATUS_CANCELED]);

        $response = $this->getJson('/api/v1/orders?status=completed');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.status', Order::STATUS_COMPLETED);
    }

    /**
     * Операции меняющие остаток фиксируются в истории движений.
     */
    public function test_order_operations_create_movements(): void
    {
        $customer = Customer::factory()->create();
        $warehouse = Warehouse::factory()->create();
        $product = $this->makeProductWithStock($warehouse, 100);

        $order = $this->postJson('/api/v1/orders', [
            'customer_id' => $customer->id,
            'warehouse_id' => $warehouse->id,
            'items' => [['product_id' => $product->id, 'count' => 5]],
        ])->json('data');

        $docType = Order::class;

        // Резервирование (create), возврат (cancel) — всего 2 движения.
        $this->assertDatabaseCount('movements', 1);
        $this->assertDatabaseHas('movements', [
            'doc_type' => $docType,
            'doc_id' => $order['id'],
            'quantity' => -5,
        ]);
    }
}
