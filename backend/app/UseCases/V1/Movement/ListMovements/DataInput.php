<?php

namespace App\UseCases\V1\Movement\ListMovements;

/**
 * DTO входных данных истории движений товаров.
 */
final class DataInput
{
    public function __construct(
        public ?int $warehouseId,
        public ?int $productId,
        public ?string $docType,
        public ?string $dateFrom,
        public ?string $dateTo,
        public int $perPage,
    ) {}

    public static function create(array $data): self
    {
        return new self(
            isset($data['warehouse_id']) ? (int) $data['warehouse_id'] : null,
            isset($data['product_id']) ? (int) $data['product_id'] : null,
            $data['doc_type'] ?? null,
            $data['date_from'] ?? null,
            $data['date_to'] ?? null,
            max(1, min((int) ($data['per_page'] ?? 15), 100)),
        );
    }
}
