<?php

namespace App\UseCases\V1\Order\ReviveOrder;

use App\Exceptions\BusinessException;
use App\Interfaces\OrderRepositoryInterface;
use App\Services\StockService;

/**
 * UseCase операции «revive заказа».
 */
final class ReviveOrderUseCase
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
     * Возобновить (revive) заказ.
     *
     * @param int $orderId идентификатор заказа
     * @return DataOutput данные возобновлённого заказа
     */
    public function execute(int $orderId): DataOutput
    {
        $order = $this->orderRepository->find($orderId);
        if ($order === null) {
            throw new BusinessException('Заказ не найден.');
        }

        $this->stockService->reviveOrder($order);
        $order->load('customer', 'warehouse', 'items.product');

        return new DataOutput($order);
    }
}
