<?php

namespace Tests\Feature\Api\V1;

use App\Models\Movement;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature-тесты истории движений товаров (v1).
 */
class MovementApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Список движений возвращает успешный ответ с пагинацией.
     */
    public function test_index_returns_paginated_movements(): void
    {
        Movement::factory()->count(5)->create();

        $response = $this->getJson('/api/v1/movements');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [['id', 'doc_type', 'doc_id', 'product_id', 'warehouse_id', 'quantity', 'created_at']],
                'meta' => ['current_page', 'per_page', 'total', 'last_page'],
            ])
            ->assertJsonCount(5, 'data');
    }

    /**
     * Фильтрация по складу возвращает только нужные движения.
     */
    public function test_index_filters_by_warehouse(): void
    {
        $warehouseA = Warehouse::factory()->create();
        $warehouseB = Warehouse::factory()->create();
        $product = Product::factory()->create();

        Movement::factory()->create([
            'product_id' => $product->id,
            'warehouse_id' => $warehouseA->id,
        ]);
        Movement::factory()->create([
            'product_id' => $product->id,
            'warehouse_id' => $warehouseB->id,
        ]);

        $response = $this->getJson("/api/v1/movements?warehouse_id={$warehouseA->id}");

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.warehouse_id', $warehouseA->id);
    }

    /**
     * Фильтрация по товару возвращает только нужные движения.
     */
    public function test_index_filters_by_product(): void
    {
        $warehouse = Warehouse::factory()->create();
        $productA = Product::factory()->create();
        $productB = Product::factory()->create();

        Movement::factory()->create([
            'product_id' => $productA->id,
            'warehouse_id' => $warehouse->id,
        ]);
        Movement::factory()->create([
            'product_id' => $productB->id,
            'warehouse_id' => $warehouse->id,
        ]);

        $response = $this->getJson("/api/v1/movements?product_id={$productA->id}");

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.product_id', $productA->id);
    }

    /**
     * Фильтрация по типу документа возвращает только нужные движения.
     */
    public function test_index_filters_by_doc_type(): void
    {
        $warehouse = Warehouse::factory()->create();
        $product = Product::factory()->create();
        $docType = 'App\\Models\\Order';

        Movement::factory()->create([
            'doc_type' => $docType,
            'doc_id' => 1,
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
        ]);
        Movement::factory()->create([
            'doc_type' => 'App\\Models\\Supply',
            'doc_id' => 1,
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
        ]);

        $response = $this->getJson('/api/v1/movements?doc_type=' . urlencode($docType));

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.doc_type', $docType);
    }

    /**
     * Фильтрация по диапазону дат возвращает только попадающие в него движения.
     */
    public function test_index_filters_by_date_range(): void
    {
        $product = Product::factory()->create();
        $warehouse = Warehouse::factory()->create();

        Movement::factory()->create([
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'created_at' => now()->subDays(5),
        ]);
        Movement::factory()->create([
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'created_at' => now(),
        ]);

        $from = now()->subDay()->toDateString();
        $to = now()->addDay()->toDateString();

        $response = $this->getJson("/api/v1/movements?date_from={$from}&date_to={$to}");

        $response->assertOk()
            ->assertJsonCount(1, 'data');
    }
}