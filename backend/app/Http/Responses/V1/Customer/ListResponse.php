<?php

namespace App\Http\Responses\V1\Customer;

use App\Http\Responses\V1\JsonResponse;
use App\UseCases\V1\Customer\ListCustomers\DataOutput;

/**
 * Ответ эндпоинта «список клиентов» (v1).
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
