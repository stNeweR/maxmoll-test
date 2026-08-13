<?php

namespace App\Http\Responses\V1\Order;

use App\Http\Responses\V1\JsonResponse;
use App\UseCases\V1\Order\ListOrders\DataOutput;

/**
 * Ответ эндпоинта «список заказов» (v1).
 */
final class ListResponse extends JsonResponse
{
    /**
     * Формирует JSON-ответ на основе результата UseCase.
     */
    public function __construct(DataOutput $output)
    {
        parent::__construct($output->toArray(), 200);
    }
}
