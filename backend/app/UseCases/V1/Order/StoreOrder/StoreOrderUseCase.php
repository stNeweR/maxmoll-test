<?php

namespace App\UseCases\V1\Order\StoreOrder;

use App\Services\StockService;


/**
 * UseCase создания заказа.
 */
final class StoreOrderUseCase
{
    /**
     * @param StockService $stockService сервис складских операций
     */
    public function __construct(private StockService $stockService) {}

    /**
     * Создать заказ.
     *
     * @param DataInput $input входные данные заказа
     * @return DataOutput созданный заказ
     */
    public function execute(DataInput $input): DataOutput
    {
        $order = $this->stockService->createOrder($input->toServicePayload());
        $order->load('customer', 'warehouse', 'items.product');

        return new DataOutput($order);
    }
}
