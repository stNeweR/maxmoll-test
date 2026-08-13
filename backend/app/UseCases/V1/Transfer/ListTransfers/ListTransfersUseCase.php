<?php

namespace App\UseCases\V1\Transfer\ListTransfers;

use App\Interfaces\TransferRepositoryInterface;

/**
 * UseCase получения списка перемещений.
 */
final class ListTransfersUseCase
{
    public function __construct(private TransferRepositoryInterface $transferRepository) {}

    public function execute(DataInput $input): DataOutput
    {
        $paginated = $this->transferRepository->paginate($input->status, $input->perPage);
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
