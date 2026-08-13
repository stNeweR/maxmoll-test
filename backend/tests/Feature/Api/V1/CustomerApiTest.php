<?php

namespace Tests\Feature\Api\V1;

use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature-тесты REST-эндпоинтов клиентов (v1).
 */
class CustomerApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Список клиентов возвращает успешный ответ с пагинацией.
     */
    public function test_index_returns_paginated_customers(): void
    {
        Customer::factory()->count(5)->create();

        $response = $this->getJson('/api/v1/customers');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [['id', 'name', 'phone', 'email', 'created_at']],
                'meta' => ['current_page', 'per_page', 'total', 'last_page'],
            ])
            ->assertJsonCount(5, 'data');
    }

    /**
     * Список клиентов поддерживает настраиваемую пагинацию.
     */
    public function test_index_respects_per_page(): void
    {
        Customer::factory()->count(5)->create();

        $response = $this->getJson('/api/v1/customers?per_page=2');

        $response->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.per_page', 2)
            ->assertJsonPath('meta.total', 5);
    }

    /**
     * Создание клиента возвращает 201 и сохраняет данные.
     */
    public function test_store_creates_a_customer(): void
    {
        $payload = [
            'name' => 'Иван Петров',
            'phone' => '+7 900 000-00-01',
            'email' => 'ivan@example.com',
        ];

        $response = $this->postJson('/api/v1/customers', $payload);

        $response->assertCreated()
            ->assertJsonPath('data.name', $payload['name'])
            ->assertJsonPath('data.phone', $payload['phone'])
            ->assertJsonPath('data.email', $payload['email']);

        $this->assertDatabaseHas('customers', $payload);
    }

    /**
     * Создание клиента без обязательного имени отклоняется.
     */
    public function test_store_requires_name(): void
    {
        $response = $this->postJson('/api/v1/customers', ['email' => 'ivan@example.com']);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('name');
    }

    /**
     * Обновление клиента изменяет его данные.
     */
    public function test_update_modifies_customer(): void
    {
        $customer = Customer::factory()->create(['name' => 'Старое имя']);

        $response = $this->putJson("/api/v1/customers/{$customer->id}", [
            'name' => 'Новое имя',
            'email' => $customer->email,
        ]);

        $response->assertOk()
            ->assertJsonPath('data.name', 'Новое имя');

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'name' => 'Новое имя',
        ]);
    }

    /**
     * Обновление клиента допускает пустой телефон.
     */
    public function test_update_allows_nullable_phone(): void
    {
        $customer = Customer::factory()->create();

        $response = $this->putJson("/api/v1/customers/{$customer->id}", [
            'name' => $customer->name,
            'phone' => null,
            'email' => $customer->email,
        ]);

        $response->assertOk()->assertJsonPath('data.phone', null);
    }
}