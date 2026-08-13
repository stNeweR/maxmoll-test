<?php

namespace App\QueryBuilders;

use Illuminate\Database\Eloquent\Builder;

/**
 * Кастомный Eloquent-построитель запросов для модель Product.
 */
class ProductQueryBuilder extends Builder
{
    /**
     * Фильтр по названию товара (частичное совпадение).
     */
    public function search(?string $term): self
    {
        if ($term !== null && $term !== '') {
            $this->where('name', 'like', "%{$term}%");
        }
        return $this;
    }

    /**
     * Загружает связи складов и товара.
     */
    public function withRelations(): self
    {
        $this->with('stocks.warehouse');
        return $this;
    }
}