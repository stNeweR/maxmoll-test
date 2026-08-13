<?php

namespace App\UseCases\V1\Warehouse\ListWarehouses;

use App\Interfaces\WarehouseRepositoryInterface;
use App\Models\Warehouse;

/**
 * UseCase получения списка складов.
 */
final class ListWarehousesUseCase
{
    /**
     * @param WarehouseRepositoryInterface $warehouseRepository репозиторий складов
     */
    public function __construct(private WarehouseRepositoryInterface $warehouseRepository) {}

    /**
     * Получить список складов.
     *
     * @return DataOutput список складов
     */
    public function execute(): DataOutput
    {
        $rows = $this->warehouseRepository->all()->map(function (Warehouse $wh) {
            return ['id' => $wh->id, 'name' => $wh->name];
        })->values()->all();

        return new DataOutput($rows);
    }
}
