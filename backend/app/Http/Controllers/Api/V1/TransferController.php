<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\V1\Transfer\IndexRequest;
use App\Http\Requests\V1\Transfer\StoreRequest;
use App\Http\Responses\V1\Transfer\ListResponse;
use App\Http\Responses\V1\Transfer\StoreResponse;
use App\Http\Responses\V1\Transfer\CompleteResponse;
use App\Http\Responses\V1\Transfer\CancelResponse;
use App\UseCases\V1\Transfer\ListTransfers\DataInput as ListInput;
use App\UseCases\V1\Transfer\ListTransfers\ListTransfersUseCase;
use App\UseCases\V1\Transfer\StoreTransfer\DataInput as StoreInput;
use App\UseCases\V1\Transfer\StoreTransfer\StoreTransferUseCase;
use App\UseCases\V1\Transfer\CompleteTransfer\CompleteTransferUseCase;
use App\UseCases\V1\Transfer\CancelTransfer\CancelTransferUseCase;

/**
 * Контроллер перемещений товаров между складами (v1).
 */
class TransferController
{
    /**
     * Список перемещений с пагинацией.
     */
    public function index(IndexRequest $request, ListTransfersUseCase $useCase): ListResponse
    {
        $output = $useCase->execute(ListInput::create($request->validated()));
        return new ListResponse($output);
    }

    /**
     * Создание перемещения (не влияет на остатки до проведения).
     */
    public function store(StoreRequest $request, StoreTransferUseCase $useCase): StoreResponse
    {
        $output = $useCase->execute(StoreInput::create($request->validated()));
        return new StoreResponse($output);
    }

    /**
     * Проведение перемещения (влияет на остатки и фиксирует движения).
     */
    public function complete(int $transferId, CompleteTransferUseCase $useCase): CompleteResponse
    {
        $output = $useCase->execute($transferId);
        return new CompleteResponse($output);
    }

    /**
     * Отмена перемещения.
     */
    public function cancel(int $transferId, CancelTransferUseCase $useCase): CancelResponse
    {
        $output = $useCase->execute($transferId);
        return new CancelResponse($output);
    }
}