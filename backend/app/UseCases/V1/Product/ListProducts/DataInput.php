<?php

namespace App\UseCases\V1\Product\ListProducts;

/**
 * DTO входных данных списка товаров.
 */
final class DataInput
{
    /**
     * Конструктор DTO списка товаров.
     *
     * @param ?string $search Строка поиска по товарам.
     */
    public function __construct(public ?string $search) {}

    /**
     * Создаёт DTO из массива входных данных.
     *
     * @param array $data Массив входных данных.
     * @return self
     */
    public static function create(array $data): self
    {
        return new self($data['search'] ?? null);
    }
}
