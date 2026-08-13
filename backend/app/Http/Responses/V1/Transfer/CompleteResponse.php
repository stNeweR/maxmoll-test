<?php

namespace App\Http\Responses\V1\Transfer;

use App\Http\Responses\V1\JsonResponse;
use App\UseCases\V1\Transfer\CompleteTransfer\DataOutput;

/**
 * Ответ эндпоинта «проведение перемещения» (v1).
 */
final class CompleteResponse extends JsonResponse
{
    /**
     * Формирует JSON-ответ на основе результата UseCase.
     */
    public function __construct(DataOutput $output)
    {
        parent::__construct($output->toArray(), 200);
    }
}
