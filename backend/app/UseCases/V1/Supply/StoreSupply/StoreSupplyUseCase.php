<?php

namespace App\UseCases\V1\Supply\StoreSupply;

use App\Services\StockService;


/**
 * UseCase создания поставки.
 */
final class StoreSupplyUseCase
{
    public function __construct(private StockService $stockService) {}

    public function execute(DataInput $input): DataOutput
    {
        $supply = $this->stockService->createSupply($input->toServicePayload());
        $supply->load('warehouse', 'items.product');

        return new DataOutput($supply);
    }
}
