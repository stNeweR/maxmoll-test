<?php

namespace App\QueryBuilders;

use Illuminate\Database\Eloquent\Builder;

/**
 * Кастомный Eloquent-построитель запросов для модель Order.
 */
class OrderQueryBuilder extends Builder
{
    /**
     * Фильтр по статусу заказа.
     */
    public function byStatus(?string $status): self
    {
        if ($status !== null && $status !== '') {
            $this->where('status', $status);
        }
        return $this;
    }

    /**
     * Фильтр по клиенту.
     */
    public function forCustomer(?int $customerId): self
    {
        if ($customerId !== null) {
            $this->where('customer_id', $customerId);
        }
        return $this;
    }

    /**
     * Фильтр по складу отгрузки.
     */
    public function atWarehouse(?int $warehouseId): self
    {
        if ($warehouseId !== null) {
            $this->where('warehouse_id', $warehouseId);
        }
        return $this;
    }

    /**
     * Загружает связи заказа (клиент, склад, позиции с товарами).
     */
    public function withRelations(): self
    {
        $this->with('customer', 'warehouse', 'items.product');
        return $this;
    }
}