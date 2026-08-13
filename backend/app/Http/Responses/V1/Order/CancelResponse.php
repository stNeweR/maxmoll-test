<?php

namespace App\Http\Responses\V1\Order;

use App\Http\Responses\V1\JsonResponse;
use App\UseCases\V1\Order\CancelOrder\DataOutput;

/**
 * Ответ эндпоинта «отмена заказа» (v1).
 */
final class CancelResponse extends JsonResponse
{
    /**
     * Формирует JSON-ответ на основе результата UseCase.
     */
    public function __construct(DataOutput $output)
    {
        parent::__construct($output->toArray(), 200);
    }
}
