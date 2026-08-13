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
    /**
     * @param OrderRepositoryInterface $orderRepository репозиторий заказов
     * @param StockService $stockService сервис складских операций
     */
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private StockService $stockService,
    ) {}

    /**
     * Отменить заказ.
     *
     * @param int $orderId идентификатор заказа
     * @return DataOutput данные отменённого заказа
     */
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
