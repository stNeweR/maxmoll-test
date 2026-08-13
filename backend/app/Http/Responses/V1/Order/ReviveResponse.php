<?php

namespace App\Http\Responses\V1\Order;

use App\Http\Responses\V1\JsonResponse;
use App\UseCases\V1\Order\ReviveOrder\DataOutput;

/**
 * Ответ эндпоинта «возобновление заказа» (v1).
 */
final class ReviveResponse extends JsonResponse
{
    /**
     * Формирует JSON-ответ на основе результата UseCase.
     */
    public function __construct(DataOutput $output)
    {
        parent::__construct($output->toArray(), 200);
    }
}
