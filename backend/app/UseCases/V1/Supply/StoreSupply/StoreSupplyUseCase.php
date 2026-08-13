<?php

namespace App\UseCases\V1\Supply\StoreSupply;

use App\Services\StockService;


/**
 * UseCase создания поставки.
 */
final class StoreSupplyUseCase
{
    /**
     * @param StockService $stockService сервис складских операций
     */
    public function __construct(private StockService $stockService) {}

    /**
     * Создать поставку.
     *
     * @param DataInput $input входные данные поставки
     * @return DataOutput созданная поставка
     */
    public function execute(DataInput $input): DataOutput
    {
        $supply = $this->stockService->createSupply($input->toServicePayload());
        $supply->load('warehouse', 'items.product');

        return new DataOutput($supply);
    }
}
