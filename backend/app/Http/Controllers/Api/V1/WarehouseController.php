<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Responses\V1\Warehouse\ListResponse;
use App\UseCases\V1\Warehouse\ListWarehouses\ListWarehousesUseCase;

/**
 * Контроллер справочника складов (v1).
 */
class WarehouseController
{
    /**
     * Список складов.
     */
    public function index(ListWarehousesUseCase $useCase): ListResponse
    {
        $output = $useCase->execute();
        return new ListResponse($output);
    }
}