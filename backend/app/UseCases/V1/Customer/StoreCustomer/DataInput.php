<?php

namespace App\UseCases\V1\Customer\StoreCustomer;

/**
 * DTO входных данных создания клиента.
 */
final class DataInput
{
    public function __construct(
        public string $name,
        public ?string $phone,
        public ?string $email,
    ) {}

    public static function create(array $data): self
    {
        return new self($data['name'], $data['phone'] ?? null, $data['email'] ?? null);
    }
}
