<?php

namespace App\UseCases\V1\Transfer\CancelTransfer;

use App\Exceptions\BusinessException;
use App\Interfaces\TransferRepositoryInterface;
use App\Services\StockService;

/**
 * UseCase операции «cancelTransfer перемещения».
 */
final class CancelTransferUseCase
{
    public function __construct(
        private TransferRepositoryInterface $transferRepository,
        private StockService $stockService,
    ) {}

    public function execute(int $transferId): DataOutput
    {
        $transfer = $this->transferRepository->find($transferId);
        if ($transfer === null) {
            throw new BusinessException('Перемещение не найдено.');
        }

        $this->stockService->cancelTransfer($transfer);
        $transfer->load('fromWarehouse', 'toWarehouse', 'items.product');

        return new DataOutput($transfer);
    }
}
