<?php

namespace App\UseCases\V1\Order\ShowOrder;

use App\Exceptions\BusinessException;
use App\Interfaces\OrderRepositoryInterface;

/**
 * UseCase просмотра одного заказа.
 */
final class ShowOrderUseCase
{
    /**
     * @param OrderRepositoryInterface $orderRepository репозиторий заказов
     */
    public function __construct(private OrderRepositoryInterface $orderRepository) {}

    /**
     * Просмотреть один заказ.
     *
     * @param int $orderId идентификатор заказа
     * @return DataOutput данные заказа
     */
    public function execute(int $orderId): DataOutput
    {
        $order = $this->orderRepository->findWithRelations($orderId);
        if ($order === null) {
            throw new BusinessException('Заказ не найден.');
        }

        return new DataOutput($order);
    }
}
