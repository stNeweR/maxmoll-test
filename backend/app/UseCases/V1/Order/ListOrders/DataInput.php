<?php

namespace App\UseCases\V1\Order\ListOrders;

/**
 * DTO входных данных списка заказов.
 */
final class DataInput
{
    /**
     * Конструктор DTO списка заказов.
     *
     * @param ?string $status     Фильтр по статусу заказа.
     * @param ?int $customerId    Идентификатор клиента.
     * @param ?int $warehouseId   Идентификатор склада.
     * @param int $perPage        Количество записей на страницу.
     */
    public function __construct(
        public ?string $status,
        public ?int $customerId,
        public ?int $warehouseId,
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
            $data['status'] ?? null,
            isset($data['customer_id']) ? (int) $data['customer_id'] : null,
            isset($data['warehouse_id']) ? (int) $data['warehouse_id'] : null,
            max(1, min((int) ($data['per_page'] ?? 15), 100)),
        );
    }
}
