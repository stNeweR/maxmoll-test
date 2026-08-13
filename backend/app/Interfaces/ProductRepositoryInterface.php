<?php

namespace App\Interfaces;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

/**
 * Интерфейс репозитория товаров.
 *
 * Описывает операции доступа к данным таблицы products.
 */
interface ProductRepositoryInterface
{
    /**
     * Проверяет, существует ли товар с указанным идентификатором.
     */
    public function exists(int $id): bool;

    /**
     * Создаёт товар.
     */
    public function create(array $attributes): Product;

    /**
     * Возвращает все товары с остатками по складам (с фильтром по имени).
     */
    public function allWithStocks(?string $search): Collection;
}
