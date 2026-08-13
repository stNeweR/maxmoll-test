<?php

namespace Tests\Feature\Api\V1;

use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature-тесты справочников складов и товаров (v1).
 */
class ReferenceApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Список складов возвращает успешный ответ.
     */
    public function test_warehouses_index_returns_list(): void
    {
        Warehouse::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/warehouses');

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    /**
     * Список товаров возвращает успешный ответ.
     */
    public function test_products_index_returns_list(): void
    {
        Product::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/products');

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }
}