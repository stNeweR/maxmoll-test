<?php

namespace App\QueryBuilders;

use Illuminate\Database\Eloquent\Builder;

/**
 * Кастомный Eloquent-построитель запросов для модель Supply.
 */
class SupplyQueryBuilder extends Builder
{
    /**
     * Фильтр по складу поставки.
     */
    public function atWarehouse(?int $warehouseId): self
    {
        if ($warehouseId !== null) {
            $this->where('warehouse_id', $warehouseId);
        }
        return $this;
    }

    /**
     * Загружает связи поставки (склад, позиции с товарами).
     */
    public function withRelations(): self
    {
        $this->with('warehouse', 'items.product');
        return $this;
    }
}