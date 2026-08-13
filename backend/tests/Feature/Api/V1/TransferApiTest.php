<?php

namespace Tests\Feature\Api\V1;

use App\Models\Movement;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Transfer;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature-тесты REST-эндпоинтов перемещений между складами (v1).
 */
class TransferApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Создание перемещения не влияет на остатки (проводится позже).
     */
    public function test_store_does_not_change_stock(): void
    {
        $from = Warehouse::factory()->create();
        $to = Warehouse::factory()->create();
        $product = Product::factory()->create();
        Stock::factory()->create([
            'product_id' => $product->id,
            'warehouse_id' => $from->id,
            'stock' => 50,
        ]);

        $response = $this->postJson('/api/v1/transfers', [
            'from_warehouse_id' => $from->id,
            'to_warehouse_id' => $to->id,
            'items' => [['product_id' => $product->id, 'count' => 10]],
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.status', Transfer::STATUS_ACTIVE);

        // Остаток на складе-источнике не изменился.
        $this->assertDatabaseHas('stocks', [
            'product_id' => $product->id,
            'warehouse_id' => $from->id,
            'stock' => 50,
        ]);
    }

    /**
     * Проведение перемещения списывает со склада-источника и оприходывает на приёмник.
     */
    public function test_complete_moves_stock_between_warehouses(): void
    {
        $from = Warehouse::factory()->create();
        $to = Warehouse::factory()->create();
        $product = Product::factory()->create();
        Stock::factory()->create([
            'product_id' => $product->id,
            'warehouse_id' => $from->id,
            'stock' => 50,
        ]);

        $transfer = $this->postJson('/api/v1/transfers', [
            'from_warehouse_id' => $from->id,
            'to_warehouse_id' => $to->id,
            'items' => [['product_id' => $product->id, 'count' => 10]],
        ])->json('data');

        $response = $this->postJson("/api/v1/transfers/{$transfer['id']}/complete");

        $response->assertOk()
            ->assertJsonPath('data.status', Transfer::STATUS_COMPLETED);

        // Источник: 50 - 10 = 40; приёмник: 0 + 10 = 10.
        $this->assertDatabaseHas('stocks', [
            'product_id' => $product->id,
            'warehouse_id' => $from->id,
            'stock' => 40,
        ]);
        $this->assertDatabaseHas('stocks', [
            'product_id' => $product->id,
            'warehouse_id' => $to->id,
            'stock' => 10,
        ]);

        // Зафиксированы два движения: -10 (источник) и +10 (приёмник).
        $this->assertDatabaseHas('movements', [
            'doc_type' => Transfer::class,
            'doc_id' => $transfer['id'],
            'product_id' => $product->id,
            'warehouse_id' => $from->id,
            'quantity' => -10,
        ]);
        $this->assertDatabaseHas('movements', [
            'doc_type' => Transfer::class,
            'doc_id' => $transfer['id'],
            'product_id' => $product->id,
            'warehouse_id' => $to->id,
            'quantity' => 10,
        ]);
    }

    /**
     * Проведение перемещения без достаточного остатка завершается ошибкой.
     */
    public function test_complete_fails_when_no_stock_available(): void
    {
        $from = Warehouse::factory()->create();
        $to = Warehouse::factory()->create();
        $product = Product::factory()->create();
        Stock::factory()->create([
            'product_id' => $product->id,
            'warehouse_id' => $from->id,
            'stock' => 5,
        ]);

        $transfer = $this->postJson('/api/v1/transfers', [
            'from_warehouse_id' => $from->id,
            'to_warehouse_id' => $to->id,
            'items' => [['product_id' => $product->id, 'count' => 10]],
        ])->json('data');

        $this->postJson("/api/v1/transfers/{$transfer['id']}/complete")
            ->assertStatus(422);
    }

    /**
     * Отмена перемещения меняет статус без изменения остатков.
     */
    public function test_cancel_sets_status_canceled(): void
    {
        $from = Warehouse::factory()->create();
        $to = Warehouse::factory()->create();
        $product = Product::factory()->create();
        Stock::factory()->create([
            'product_id' => $product->id,
            'warehouse_id' => $from->id,
            'stock' => 50,
        ]);

        $transfer = $this->postJson('/api/v1/transfers', [
            'from_warehouse_id' => $from->id,
            'to_warehouse_id' => $to->id,
            'items' => [['product_id' => $product->id, 'count' => 10]],
        ])->json('data');

        $response = $this->postJson("/api/v1/transfers/{$transfer['id']}/cancel");

        $response->assertOk()
            ->assertJsonPath('data.status', Transfer::STATUS_CANCELED);
    }

    /**
     * Перемещение между одинаковыми складами отклоняется валидацией.
     */
    public function test_store_rejects_same_warehouse(): void
    {
        $warehouse = Warehouse::factory()->create();
        $product = Product::factory()->create();

        $response = $this->postJson('/api/v1/transfers', [
            'from_warehouse_id' => $warehouse->id,
            'to_warehouse_id' => $warehouse->id,
            'items' => [['product_id' => $product->id, 'count' => 1]],
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('from_warehouse_id');
    }
}