<?php

namespace App\UseCases\V1\Customer\StoreCustomer;

/**
 * DTO входных данных создания клиента.
 */
final class DataInput
{
    /**
     * Конструктор DTO создания клиента.
     *
     * @param string $name   Название клиента.
     * @param ?string $phone Телефон клиента.
     * @param ?string $email Email клиента.
     */
    public function __construct(
        public string $name,
        public ?string $phone,
        public ?string $email,
    ) {}

    /**
     * Создаёт DTO из массива входных данных.
     *
     * @param array $data Массив входных данных.
     * @return self
     */
    public static function create(array $data): self
    {
        return new self($data['name'], $data['phone'] ?? null, $data['email'] ?? null);
    }
}
