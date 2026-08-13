<?php

namespace App\Interfaces;

use App\Models\Supply;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Интерфейс репозитория поставок.
 *
 * Описывает операции доступа к данным таблиц supplies и supply_items.
 */
interface SupplyRepositoryInterface
{
    /**
     * Создаёт поставку вместе с позициями.
     *
     * @param  array  $attributes  атрибуты поставки (warehouse_id)
     * @param  array  $items  позиции в формате [['product_id' => ..., 'count' => ...], ...]
     */
    public function createWithItems(array $attributes, array $items): Supply;

    /**
     * Возвращает список поставок с фильтрами и пагинацией.
     */
    public function paginate(?int $warehouseId, int $perPage): LengthAwarePaginator;
}
