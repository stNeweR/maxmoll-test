<?php

namespace App\UseCases\V1\Transfer\StoreTransfer;

use App\Services\StockService;


/**
 * UseCase создания перемещения.
 */
final class StoreTransferUseCase
{
    /**
     * @param StockService $stockService сервис складских операций
     */
    public function __construct(private StockService $stockService) {}

    /**
     * Создать перемещение.
     *
     * @param DataInput $input входные данные перемещения
     * @return DataOutput созданное перемещение
     */
    public function execute(DataInput $input): DataOutput
    {
        $transfer = $this->stockService->createTransfer($input->toServicePayload());
        $transfer->load('fromWarehouse', 'toWarehouse', 'items.product');

        return new DataOutput($transfer);
    }
}
