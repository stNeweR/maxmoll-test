<?php

namespace App\QueryBuilders;

use Illuminate\Database\Eloquent\Builder;

/**
 * Кастомный Eloquent-построитель запросов для модель Movement.
 */
class MovementQueryBuilder extends Builder
{
    /**
     * Фильтр по складу.
     */
    public function atWarehouse(?int $warehouseId): self
    {
        if ($warehouseId !== null) {
            $this->where('warehouse_id', $warehouseId);
        }
        return $this;
    }

    /**
     * Фильтр по товару.
     */
    public function forProduct(?int $productId): self
    {
        if ($productId !== null) {
            $this->where('product_id', $productId);
        }
        return $this;
    }

    /**
     * Фильтр по типу документа-источника.
     */
    public function byDocType(?string $docType): self
    {
        if ($docType !== null && $docType !== '') {
            $this->where('doc_type', $docType);
        }
        return $this;
    }

    /**
     * Фильтр по диапазону дат движения.
     */
    public function betweenDates(?string $from, ?string $to): self
    {
        if ($from !== null && $from !== '') {
            $this->where('created_at', '>=', $from);
        }
        if ($to !== null && $to !== '') {
            $this->where('created_at', '<=', $to);
        }
        return $this;
    }

    /**
     * Загружает связи товара и склада.
     */
    public function withRelations(): self
    {
        $this->with('product', 'warehouse');
        return $this;
    }
}