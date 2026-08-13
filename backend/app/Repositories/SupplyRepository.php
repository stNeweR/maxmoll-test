<?php

namespace App\Repositories;

use App\Interfaces\SupplyRepositoryInterface;
use App\Models\Supply;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Репозиторий поставок.
 *
 * Реализует доступ к данным таблиц supplies и supply_items.
 */
class SupplyRepository implements SupplyRepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createWithItems(array $attributes, array $items): Supply
    {
        $supply = Supply::create($attributes);

        // Сохраняем позиции поставки.
        foreach ($items as $item) {
            $supply->items()->create($item);
        }

        return $supply;
    }

    /**
     * {@inheritDoc}
     */
    public function paginate(?int $warehouseId, int $perPage): LengthAwarePaginator
    {
        $query = Supply::with('warehouse', 'items.product')->orderByDesc('id');

        // Фильтрация по складу.
        if ($warehouseId !== null) {
            $query->where('warehouse_id', $warehouseId);
        }

        return $query->paginate($perPage);
    }
}
