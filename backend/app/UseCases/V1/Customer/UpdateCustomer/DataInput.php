<?php

namespace App\UseCases\V1\Customer\UpdateCustomer;

/**
 * DTO входных данных обновления клиента.
 */
final class DataInput
{
    /**
     * Конструктор DTO обновления клиента.
     *
     * @param int $id       Идентификатор клиента.
     * @param string $name  Название клиента.
     * @param ?string $phone Телефон клиента.
     * @param ?string $email Email клиента.
     */
    public function __construct(
        public int $id,
        public string $name,
        public ?string $phone,
        public ?string $email,
    ) {}

    /**
     * Создаёт DTO из массива входных данных.
     *
     * @param array $data Массив входных данных.
     * @param int $id     Идентификатор клиента.
     * @return self
     */
    public static function create(array $data, int $id): self
    {
        return new self(
            id: $id,
            name: $data['name'],
            phone: $data['phone'] ?? null,
            email: $data['email'] ?? null,
        );
    }
}
