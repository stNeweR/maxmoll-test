<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\V1\Movement\IndexRequest;
use App\Http\Responses\V1\Movement\ListResponse;
use App\UseCases\V1\Movement\ListMovements\DataInput;
use App\UseCases\V1\Movement\ListMovements\ListMovementsUseCase;

/**
 * Контроллер истории движений товаров (v1).
 */
class MovementController
{
    /**
     * История движения остатков с фильтрами и пагинацией.
     */
    public function index(IndexRequest $request, ListMovementsUseCase $useCase): ListResponse
    {
        $output = $useCase->execute(DataInput::create($request->validated()));
        return new ListResponse($output);
    }
}