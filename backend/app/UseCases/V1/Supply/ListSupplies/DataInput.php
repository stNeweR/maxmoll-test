<?php

namespace App\UseCases\V1\Supply\ListSupplies;

/**
 * DTO входных данных списка поставок.
 */
final class DataInput
{
    public function __construct(public ?int $warehouseId, public int $perPage) {}

    public static function create(array $data): self
    {
        return new self(
            isset($data['warehouse_id']) ? (int) $data['warehouse_id'] : null,
            max(1, min((int) ($data['per_page'] ?? 15), 100)),
        );
    }
}
