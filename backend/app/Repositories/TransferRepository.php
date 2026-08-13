<?php

namespace App\Repositories;

use App\Interfaces\TransferRepositoryInterface;
use App\Models\Transfer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

/**
 * Репозиторий перемещений.
 *
 * Реализует доступ к данным таблиц transfers и transfer_items.
 */
class TransferRepository implements TransferRepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function find(int $id): ?Transfer
    {
        return Transfer::find($id);
    }

    /**
     * {@inheritDoc}
     */
    public function createWithItems(array $attributes, array $items): Transfer
    {
        $transfer = Transfer::create($attributes);

        // Сохраняем позиции перемещения.
        foreach ($items as $item) {
            $transfer->items()->create($item);
        }

        return $transfer;
    }

    /**
     * {@inheritDoc}
     */
    public function updateStatus(Transfer $transfer, string $status, ?Carbon $completedAt = null): Transfer
    {
        $transfer->status = $status;
        $transfer->completed_at = $completedAt;
        $transfer->save();

        return $transfer;
    }

    /**
     * {@inheritDoc}
     */
    public function paginate(?string $status, int $perPage): LengthAwarePaginator
    {
        $query = Transfer::with('fromWarehouse', 'toWarehouse', 'items.product')->orderByDesc('id');

        // Фильтрация по статусу.
        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }

        return $query->paginate($perPage);
    }
}
