<?php

namespace App\Interfaces;

use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Collection;

/**
 * Интерфейс репозитория складов.
 *
 * Описывает операции доступа к данным таблицы warehouses.
 */
interface WarehouseRepositoryInterface
{
    /**
     * Создаёт склад.
     */
    public function create(array $attributes): Warehouse;

    /**
     * Возвращает все склады, упорядоченные по идентификатору.
     */
    public function all(): Collection;
}
