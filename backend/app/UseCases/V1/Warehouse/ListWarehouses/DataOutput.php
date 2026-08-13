<?php

namespace App\UseCases\V1\Warehouse\ListWarehouses;

/**
 * DTO результата списка складов.
 */
final class DataOutput
{
    public function __construct(private array $rows) {}

    public function toArray(): array
    {
        return ['data' => $this->rows];
    }
}
