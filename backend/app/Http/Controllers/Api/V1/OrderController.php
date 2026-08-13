<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\V1\Order\IndexRequest;
use App\Http\Requests\V1\Order\StoreRequest;
use App\Http\Requests\V1\Order\UpdateRequest;
use App\Http\Responses\V1\Order\ListResponse;
use App\Http\Responses\V1\Order\ShowResponse;
use App\Http\Responses\V1\Order\StoreResponse;
use App\Http\Responses\V1\Order\UpdateResponse;
use App\Http\Responses\V1\Order\CompleteResponse;
use App\Http\Responses\V1\Order\CancelResponse;
use App\Http\Responses\V1\Order\ReviveResponse;
use App\UseCases\V1\Order\ListOrders\DataInput as ListInput;
use App\UseCases\V1\Order\ListOrders\ListOrdersUseCase;
use App\UseCases\V1\Order\ShowOrder\ShowOrderUseCase;
use App\UseCases\V1\Order\StoreOrder\DataInput as StoreInput;
use App\UseCases\V1\Order\StoreOrder\StoreOrderUseCase;
use App\UseCases\V1\Order\UpdateOrder\DataInput as UpdateInput;
use App\UseCases\V1\Order\UpdateOrder\UpdateOrderUseCase;
use App\UseCases\V1\Order\CompleteOrder\CompleteOrderUseCase;
use App\UseCases\V1\Order\CancelOrder\CancelOrderUseCase;
use App\UseCases\V1\Order\ReviveOrder\ReviveOrderUseCase;

/**
 * Контроллер заказов (v1).
 */
class OrderController
{
    /**
     * Список заказов с фильтрами и пагинацией.
     */
    public function index(IndexRequest $request, ListOrdersUseCase $useCase): ListResponse
    {
        $output = $useCase->execute(ListInput::create($request->validated()));
        return new ListResponse($output);
    }

    /**
     * Просмотр одного заказа с позициями.
     */
    public function show(int $orderId, ShowOrderUseCase $useCase): ShowResponse
    {
        $output = $useCase->execute($orderId);
        return new ShowResponse($output);
    }

    /**
     * Создание заказа (клиент должен существовать).
     */
    public function store(StoreRequest $request, StoreOrderUseCase $useCase): StoreResponse
    {
        $output = $useCase->execute(StoreInput::create($request->validated()));
        return new StoreResponse($output);
    }

    /**
     * Обновление заказа (клиент, склад и позиции; статус не меняется).
     */
    public function update(UpdateRequest $request, int $orderId, UpdateOrderUseCase $useCase): UpdateResponse
    {
        $output = $useCase->execute(UpdateInput::create($request->validated(), $orderId));
        return new UpdateResponse($output);
    }

    /**
     * Завершение заказа.
     */
    public function complete(int $orderId, CompleteOrderUseCase $useCase): CompleteResponse
    {
        $output = $useCase->execute($orderId);
        return new CompleteResponse($output);
    }

    /**
     * Отмена заказа (товары возвращаются на остаток).
     */
    public function cancel(int $orderId, CancelOrderUseCase $useCase): CancelResponse
    {
        $output = $useCase->execute($orderId);
        return new CancelResponse($output);
    }

    /**
     * Возобновление отменённого заказа.
     */
    public function revive(int $orderId, ReviveOrderUseCase $useCase): ReviveResponse
    {
        $output = $useCase->execute($orderId);
        return new ReviveResponse($output);
    }
}