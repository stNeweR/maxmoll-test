<?php

namespace App\UseCases\V1\Movement\ListMovements;

/**
 * DTO входных данных истории движений товаров.
 */
final class DataInput
{
    /**
     * Конструктор DTO истории движений товаров.
     *
     * @param ?int $warehouseId Идентификатор склада.
     * @param ?int $productId   Идентификатор товара.
     * @param ?string $docType  Тип документа.
     * @param ?string $dateFrom Начало периода.
     * @param ?string $dateTo   Конец периода.
     * @param int $perPage      Количество записей на страницу.
     */
    public function __construct(
        public ?int $warehouseId,
        public ?int $productId,
        public ?string $docType,
        public ?string $dateFrom,
        public ?string $dateTo,
        public int $perPage,
    ) {}

    /**
     * Создаёт DTO из массива входных данных.
     *
     * @param array $data Массив входных данных.
     * @return self
     */
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
