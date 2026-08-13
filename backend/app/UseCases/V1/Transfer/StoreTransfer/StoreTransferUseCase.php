<?php

namespace App\UseCases\V1\Transfer\StoreTransfer;

use App\Services\StockService;


/**
 * UseCase создания перемещения.
 */
final class StoreTransferUseCase
{
    public function __construct(private StockService $stockService) {}

    public function execute(DataInput $input): DataOutput
    {
        $transfer = $this->stockService->createTransfer($input->toServicePayload());
        $transfer->load('fromWarehouse', 'toWarehouse', 'items.product');

        return new DataOutput($transfer);
    }
}
