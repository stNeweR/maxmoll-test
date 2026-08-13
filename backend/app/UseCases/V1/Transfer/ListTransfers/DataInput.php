<?php

namespace App\UseCases\V1\Transfer\ListTransfers;

/**
 * DTO входных данных списка перемещений.
 */
final class DataInput
{
    /**
     * Конструктор DTO списка перемещений.
     *
     * @param ?string $status Фильтр по статусу перемещения.
     * @param int $perPage    Количество записей на страницу.
     */
    public function __construct(public ?string $status, public int $perPage) {}

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
            max(1, min((int) ($data['per_page'] ?? 15), 100)),
        );
    }
}
