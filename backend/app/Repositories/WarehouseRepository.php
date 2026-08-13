<?php

namespace App\Repositories;

use App\Interfaces\WarehouseRepositoryInterface;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Collection;

/**
 * Репозиторий складов.
 *
 * Реализует доступ к данным таблицы warehouses.
 */
class WarehouseRepository implements WarehouseRepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function create(array $attributes): Warehouse
    {
        return Warehouse::create($attributes);
    }

    /**
     * {@inheritDoc}
     */
    public function all(): Collection
    {
        return Warehouse::orderBy('id')->get();
    }
}
