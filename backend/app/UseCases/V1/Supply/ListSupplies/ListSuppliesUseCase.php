<?php

namespace App\UseCases\V1\Supply\ListSupplies;

use App\Interfaces\SupplyRepositoryInterface;

/**
 * UseCase получения списка поставок.
 */
final class ListSuppliesUseCase
{
    public function __construct(private SupplyRepositoryInterface $supplyRepository) {}

    public function execute(DataInput $input): DataOutput
    {
        $paginated = $this->supplyRepository->paginate($input->warehouseId, $input->perPage);
        $rows = collect($paginated->items())->map->toArray()->all();

        return new DataOutput(
            $rows,
            [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        );
    }
}
