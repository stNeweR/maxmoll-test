<?php

namespace App\UseCases\V1\Customer\ListCustomers;

/**
 * DTO входных данных списка клиентов.
 */
final class DataInput
{
    public function __construct(
        public ?string $search,
        public ?string $email,
        public ?string $phone,
        public int $perPage,
    ) {}

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
