<?php

namespace App\Interfaces;

use App\Models\Movement;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Интерфейс репозитория движений товаров.
 *
 * Описывает операции доступа к данным таблицы movements.
 */
interface MovementRepositoryInterface
{
    /**
     * Создаёт запись движения.
     */
    public function create(array $attributes): Movement;

    /**
     * Возвращает историю движений с фильтрами и пагинацией.
     */
    public function paginate(?int $warehouseId, ?int $productId, ?string $docType, ?string $dateFrom, ?string $dateTo, int $perPage): LengthAwarePaginator;
}
