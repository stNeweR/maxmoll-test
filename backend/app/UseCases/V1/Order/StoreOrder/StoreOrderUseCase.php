<?php

namespace App\UseCases\V1\Order\StoreOrder;

use App\Services\StockService;


/**
 * UseCase создания заказа.
 */
final class StoreOrderUseCase
{
    public function __construct(private StockService $stockService) {}

    public function execute(DataInput $input): DataOutput
    {
        $order = $this->stockService->createOrder($input->toServicePayload());
        $order->load('customer', 'warehouse', 'items.product');

        return new DataOutput($order);
    }
}
