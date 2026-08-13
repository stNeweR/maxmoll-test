<?php

namespace App\UseCases\V1\Product\ListProducts;

use App\Interfaces\ProductRepositoryInterface;
use App\Models\Product;

/**
 * UseCase получения списка товаров с остатками по складам.
 */
final class ListProductsUseCase
{
    public function __construct(private ProductRepositoryInterface $productRepository) {}

    public function execute(DataInput $input): DataOutput
    {
        $rows = $this->productRepository->allWithStocks($input->search)->map(function (Product $product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'stocks' => $product->stocks->map(function ($stock) {
                    return [
                        'warehouse_id' => $stock->warehouse_id,
                        'warehouse_name' => $stock->warehouse->name ?? null,
                        'stock' => $stock->stock,
                    ];
                })->values()->all(),
            ];
        })->values()->all();

        return new DataOutput($rows, []);
    }
}
