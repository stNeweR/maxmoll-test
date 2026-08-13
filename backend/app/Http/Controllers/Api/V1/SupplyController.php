<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\V1\Supply\IndexRequest;
use App\Http\Requests\V1\Supply\StoreRequest;
use App\Http\Responses\V1\Supply\ListResponse;
use App\Http\Responses\V1\Supply\StoreResponse;
use App\UseCases\V1\Supply\ListSupplies\DataInput as ListInput;
use App\UseCases\V1\Supply\ListSupplies\ListSuppliesUseCase;
use App\UseCases\V1\Supply\StoreSupply\DataInput as StoreInput;
use App\UseCases\V1\Supply\StoreSupply\StoreSupplyUseCase;

/**
 * Контроллер поставок (v1).
 */
class SupplyController
{
    /**
     * Список поставок с фильтрами и пагинацией.
     */
    public function index(IndexRequest $request, ListSuppliesUseCase $useCase): ListResponse
    {
        $output = $useCase->execute(ListInput::create($request->validated()));
        return new ListResponse($output);
    }

    /**
     * Создание поставки (увеличивает остатки на указанном складе).
     */
    public function store(StoreRequest $request, StoreSupplyUseCase $useCase): StoreResponse
    {
        $output = $useCase->execute(StoreInput::create($request->validated()));
        return new StoreResponse($output);
    }
}