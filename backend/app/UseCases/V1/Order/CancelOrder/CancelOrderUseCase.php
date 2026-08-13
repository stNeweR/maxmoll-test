<?php

namespace App\UseCases\V1\Order\CancelOrder;

use App\Exceptions\BusinessException;
use App\Interfaces\OrderRepositoryInterface;
use App\Services\StockService;

/**
 * UseCase операции «cancel заказа».
 */
final class CancelOrderUseCase
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private StockService $stockService,
    ) {}

    public function execute(int $orderId): DataOutput
    {
        $order = $this->orderRepository->find($orderId);
        if ($order === null) {
            throw new BusinessException('Заказ не найден.');
        }

        $this->stockService->cancelOrder($order);
        $order->load('customer', 'warehouse', 'items.product');

        return new DataOutput($order);
    }
}
