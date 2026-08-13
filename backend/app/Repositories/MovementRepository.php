<?php

namespace App\Repositories;

use App\Interfaces\MovementRepositoryInterface;
use App\Models\Movement;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Репозиторий движений товаров.
 *
 * Реализует доступ к данным таблицы movements.
 */
class MovementRepository implements MovementRepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function create(array $attributes): Movement
    {
        return Movement::create($attributes);
    }

    /**
     * {@inheritDoc}
     */
    public function paginate(?int $warehouseId, ?int $productId, ?string $docType, ?string $dateFrom, ?string $dateTo, int $perPage): LengthAwarePaginator
    {
        $query = Movement::with('product', 'warehouse')->orderByDesc('id');

        // Фильтрация по складу.
        if ($warehouseId !== null) {
            $query->where('warehouse_id', $warehouseId);
        }
        // Фильтрация по товару.
        if ($productId !== null) {
            $query->where('product_id', $productId);
        }
        // Фильтрация по типу документа-источника.
        if ($docType !== null && $docType !== '') {
            $query->where('doc_type', $docType);
        }
        // Фильтрация по началу периода.
        if ($dateFrom !== null && $dateFrom !== '') {
            $query->where('created_at', '>=', $dateFrom);
        }
        // Фильтрация по концу периода.
        if ($dateTo !== null && $dateTo !== '') {
            $query->where('created_at', '<=', $dateTo);
        }

        return $query->paginate($perPage);
    }
}
