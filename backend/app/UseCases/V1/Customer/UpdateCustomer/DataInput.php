<?php

namespace App\UseCases\V1\Customer\UpdateCustomer;

/**
 * DTO входных данных обновления клиента.
 */
final class DataInput
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $phone,
        public ?string $email,
    ) {}

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
