<?php

namespace App\UseCases\V1\Product\ListProducts;

/**
 * DTO входных данных списка товаров.
 */
final class DataInput
{
    public function __construct(public ?string $search) {}

    public static function create(array $data): self
    {
        return new self($data['search'] ?? null);
    }
}
