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
    /**
     * @param TransferRepositoryInterface $transferRepository репозиторий перемещений
     * @param StockService $stockService сервис складских операций
     */
    public function __construct(
        private TransferRepositoryInterface $transferRepository,
        private StockService $stockService,
    ) {}

    /**
     * Отменить перемещение.
     *
     * @param int $transferId идентификатор перемещения
     * @return DataOutput данные отменённого перемещения
     */
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
