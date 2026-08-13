<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\V1\Product\IndexRequest;
use App\Http\Responses\V1\Product\ListResponse;
use App\UseCases\V1\Product\ListProducts\DataInput;
use App\UseCases\V1\Product\ListProducts\ListProductsUseCase;

/**
 * Контроллер справочника товаров (v1).
 */
class ProductController
{
    /**
     * Список товаров с остатками по складам.
     */
    public function index(IndexRequest $request, ListProductsUseCase $useCase): ListResponse
    {
        $output = $useCase->execute(DataInput::create($request->validated()));
        return new ListResponse($output);
    }
}