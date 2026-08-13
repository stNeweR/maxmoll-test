<?php

namespace App\UseCases\V1\Movement\ListMovements;

use App\Interfaces\MovementRepositoryInterface;

/**
 * UseCase получения истории движений товаров с фильтрами и пагинацией.
 */
final class ListMovementsUseCase
{
    public function __construct(private MovementRepositoryInterface $movementRepository) {}

    public function execute(DataInput $input): DataOutput
    {
        $paginated = $this->movementRepository->paginate(
            $input->warehouseId,
            $input->productId,
            $input->docType,
            $input->dateFrom,
            $input->dateTo,
            $input->perPage,
        );
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
