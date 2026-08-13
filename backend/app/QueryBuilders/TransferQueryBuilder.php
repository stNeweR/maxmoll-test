<?php

namespace App\QueryBuilders;

use Illuminate\Database\Eloquent\Builder;

/**
 * Кастомный Eloquent-построитель запросов для модель Transfer.
 */
class TransferQueryBuilder extends Builder
{
    /**
     * Фильтр по статусу перемещения.
     */
    public function byStatus(?string $status): self
    {
        if ($status !== null && $status !== '') {
            $this->where('status', $status);
        }
        return $this;
    }

    /**
     * Загружает связи перемещения.
     */
    public function withRelations(): self
    {
        $this->with('fromWarehouse', 'toWarehouse', 'items.product');
        return $this;
    }
}