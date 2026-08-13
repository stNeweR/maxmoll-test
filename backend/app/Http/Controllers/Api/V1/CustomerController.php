<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\V1\Customer\IndexRequest;
use App\Http\Requests\V1\Customer\StoreRequest;
use App\Http\Requests\V1\Customer\UpdateRequest;
use App\Http\Responses\V1\Customer\ListResponse;
use App\Http\Responses\V1\Customer\StoreResponse;
use App\Http\Responses\V1\Customer\UpdateResponse;
use App\UseCases\V1\Customer\ListCustomers\DataInput as ListInput;
use App\UseCases\V1\Customer\ListCustomers\ListCustomersUseCase;
use App\UseCases\V1\Customer\StoreCustomer\DataInput as StoreInput;
use App\UseCases\V1\Customer\StoreCustomer\StoreCustomerUseCase;
use App\UseCases\V1\Customer\UpdateCustomer\DataInput as UpdateInput;
use App\UseCases\V1\Customer\UpdateCustomer\UpdateCustomerUseCase;

/**
 * Контроллер клиентов (v1).
 */
class CustomerController
{
    /**
     * Список клиентов с фильтрами и пагинацией.
     */
    public function index(IndexRequest $request, ListCustomersUseCase $useCase): ListResponse
    {
        $output = $useCase->execute(ListInput::create($request->validated()));
        return new ListResponse($output);
    }

    /**
     * Создание клиента.
     */
    public function store(StoreRequest $request, StoreCustomerUseCase $useCase): StoreResponse
    {
        $output = $useCase->execute(StoreInput::create($request->validated()));
        return new StoreResponse($output);
    }

    /**
     * Обновление клиента.
     */
    public function update(UpdateRequest $request, int $customerId, UpdateCustomerUseCase $useCase): UpdateResponse
    {
        $output = $useCase->execute(UpdateInput::create($request->validated(), $customerId));
        return new UpdateResponse($output);
    }
}