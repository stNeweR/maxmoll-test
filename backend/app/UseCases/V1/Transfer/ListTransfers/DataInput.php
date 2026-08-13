<?php

namespace App\UseCases\V1\Transfer\ListTransfers;

/**
 * DTO входных данных списка перемещений.
 */
final class DataInput
{
    public function __construct(public ?string $status, public int $perPage) {}

    public static function create(array $data): self
    {
        return new self(
            $data['status'] ?? null,
            max(1, min((int) ($data['per_page'] ?? 15), 100)),
        );
    }
}
