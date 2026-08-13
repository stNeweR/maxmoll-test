<?php

namespace App\UseCases\V1\Order\UpdateOrder;

use App\Exceptions\BusinessException;
use App\Interfaces\OrderRepositoryInterface;
use App\Services\StockService;

/**
 * UseCase обновления заказа (клиент, склад и состав позиций; не статус).
 */
final class UpdateOrderUseCase
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
     * Обновить заказ.
     *
     * @param DataInput $input входные данные заказа
     * @return DataOutput обновлённый заказ
     */
    public function execute(DataInput $input): DataOutput
    {
        $order = $this->orderRepository->find($input->id);
        if ($order === null) {
            throw new BusinessException('Заказ не найден.');
        }

        $order = $this->stockService->updateOrder($order, $input->toServicePayload());
        $order->load('customer', 'warehouse', 'items.product');

        return new DataOutput($order);
    }
}
