<?php

namespace App\Repositories;

use App\Interfaces\OrderRepositoryInterface;
use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

/**
 * Репозиторий заказов.
 *
 * Реализует доступ к данным таблиц orders и order_items.
 */
class OrderRepository implements OrderRepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function find(int $id): ?Order
    {
        return Order::find($id);
    }

    /**
     * {@inheritDoc}
     */
    public function findWithRelations(int $id): ?Order
    {
        return Order::with('customer', 'warehouse', 'items.product')->find($id);
    }

    /**
     * {@inheritDoc}
     */
    public function createWithItems(array $attributes, array $items): Order
    {
        $order = Order::create($attributes);

        // Сохраняем позиции заказа.
        foreach ($items as $item) {
            $order->items()->create($item);
        }

        return $order;
    }

    /**
     * {@inheritDoc}
     */
    public function updateAttributes(Order $order, array $attributes): Order
    {
        $order->fill($attributes)->save();

        return $order;
    }

    /**
     * {@inheritDoc}
     */
    public function syncItems(Order $order, array $items): void
    {
        // Пересоздаём позиции заказа.
        $order->items()->delete();
        foreach ($items as $item) {
            $order->items()->create($item);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function updateStatus(Order $order, string $status, ?Carbon $completedAt = null): Order
    {
        $order->status = $status;
        $order->completed_at = $completedAt;
        $order->save();

        return $order;
    }

    /**
     * {@inheritDoc}
     */
    public function paginate(?string $status, ?int $customerId, ?int $warehouseId, int $perPage): LengthAwarePaginator
    {
        $query = Order::with('customer', 'warehouse', 'items.product')->orderByDesc('id');

        // Фильтрация по статусу.
        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }
        // Фильтрация по клиенту.
        if ($customerId !== null) {
            $query->where('customer_id', $customerId);
        }
        // Фильтрация по складу.
        if ($warehouseId !== null) {
            $query->where('warehouse_id', $warehouseId);
        }

        return $query->paginate($perPage);
    }
}
