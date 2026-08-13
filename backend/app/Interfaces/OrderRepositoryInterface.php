<?php

namespace App\Interfaces;

use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

/**
 * Интерфейс репозитория заказов.
 *
 * Описывает операции доступа к данным таблиц orders и order_items.
 */
interface OrderRepositoryInterface
{
    /**
     * Находит заказ по идентификатору.
     */
    public function find(int $id): ?Order;

    /**
     * Находит заказ с загруженными связями (клиент, склад, позиции, товары).
     */
    public function findWithRelations(int $id): ?Order;

    /**
     * Создаёт заказ вместе с позициями.
     *
     * @param  array  $attributes  атрибуты заказа (customer_id, warehouse_id, status, completed_at)
     * @param  array  $items  позиции в формате [['product_id' => ..., 'count' => ...], ...]
     */
    public function createWithItems(array $attributes, array $items): Order;

    /**
     * Обновляет атрибуты заказа.
     */
    public function updateAttributes(Order $order, array $attributes): Order;

    /**
     * Полностью заменяет состав позиций заказа.
     *
     * @param  array  $items  позиции в формате [['product_id' => ..., 'count' => ...], ...]
     */
    public function syncItems(Order $order, array $items): void;

    /**
     * Меняет статус заказа (и, при необходимости, дату завершения).
     */
    public function updateStatus(Order $order, string $status, ?Carbon $completedAt = null): Order;

    /**
     * Возвращает список заказов с фильтрами и пагинацией.
     */
    public function paginate(?string $status, ?int $customerId, ?int $warehouseId, int $perPage): LengthAwarePaginator;
}
