<?php

namespace App\UseCases\V1\Order\ListOrders;

use App\Interfaces\OrderRepositoryInterface;

/**
 * UseCase получения списка заказов с фильтрами и пагинацией.
 */
final class ListOrdersUseCase
{
    public function __construct(private OrderRepositoryInterface $orderRepository) {}

    public function execute(DataInput $input): DataOutput
    {
        $paginated = $this->orderRepository->paginate($input->status, $input->customerId, $input->warehouseId, $input->perPage);
        $rows = collect($paginated->items())->map->toArray()->all();

        return new DataOutput(
            $rows,
            [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        );
    }
}
