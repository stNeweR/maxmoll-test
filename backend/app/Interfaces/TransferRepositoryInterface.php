<?php

namespace App\Interfaces;

use App\Models\Transfer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

/**
 * Интерфейс репозитория перемещений.
 *
 * Описывает операции доступа к данным таблиц transfers и transfer_items.
 */
interface TransferRepositoryInterface
{
    /**
     * Находит перемещение по идентификатору.
     */
    public function find(int $id): ?Transfer;

    /**
     * Создаёт перемещение вместе с позициями.
     *
     * @param  array  $attributes  атрибуты перемещения (from_warehouse_id, to_warehouse_id, status, completed_at)
     * @param  array  $items  позиции в формате [['product_id' => ..., 'count' => ...], ...]
     */
    public function createWithItems(array $attributes, array $items): Transfer;

    /**
     * Меняет статус перемещения (и, при необходимости, дату завершения).
     */
    public function updateStatus(Transfer $transfer, string $status, ?Carbon $completedAt = null): Transfer;

    /**
     * Возвращает список перемещений с фильтрами и пагинацией.
     */
    public function paginate(?string $status, int $perPage): LengthAwarePaginator;
}
