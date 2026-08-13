<?php

namespace App\Repositories;

use App\Interfaces\ProductRepositoryInterface;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

/**
 * Репозиторий товаров.
 *
 * Реализует доступ к данным таблицы products.
 */
class ProductRepository implements ProductRepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function exists(int $id): bool
    {
        return Product::whereKey($id)->exists();
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $attributes): Product
    {
        return Product::create($attributes);
    }

    /**
     * {@inheritDoc}
     */
    public function allWithStocks(?string $search): Collection
    {
        $query = Product::with('stocks.warehouse')->orderBy('name');

        // Фильтрация по частичному совпадению названия.
        if ($search !== null && $search !== '') {
            $query->where('name', 'like', "%{$search}%");
        }

        return $query->get();
    }
}
