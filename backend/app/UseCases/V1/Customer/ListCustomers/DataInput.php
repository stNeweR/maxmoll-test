<?php

namespace App\UseCases\V1\Customer\ListCustomers;

/**
 * DTO входных данных списка клиентов.
 */
final class DataInput
{
    /**
     * Конструктор DTO списка клиентов.
     *
     * @param ?string $search  Строка поиска по клиентам.
     * @param ?string $email   Фильтр по email клиента.
     * @param ?string $phone   Фильтр по телефону клиента.
     * @param int $perPage     Количество записей на страницу.
     */
    public function __construct(
        public ?string $search,
        public ?string $email,
        public ?string $phone,
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
            $data['search'] ?? null,
            $data['email'] ?? null,
            $data['phone'] ?? null,
            max(1, min((int) ($data['per_page'] ?? 15), 100)),
        );
    }
}
