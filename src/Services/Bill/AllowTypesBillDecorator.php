<?php

namespace App\Services\Bill;

use App\Services\Soap\ExceptionCreator;
use App\Validator\FilenameValidator;
use App\Validator\PackValidator;
use App\Model\{ErrorCodeList,
    GetStatusCdrRequest,
    GetStatusCdrResponse,
    GetStatusRequest,
    GetStatusResponse,
    SendBillRequest,
    SendBillResponse,
    SendPackRequest,
    SendPackResponse,
    SendSummaryRequest,
    SendSummaryResponse,
    ValidationError};
use Greenter\Validator\Entity\DocumentType;

class AllowTypesBillDecorator implements BillServiceInterface
{
    private const SUMMARY_TYPES = [
        DocumentType::RESUMEN_DIARIO,
        DocumentType::COMUNICACION_BAJA,
        DocumentType::RESUMEN_REVERSION,
    ];

    /**
     * AllowTypesBillDecorator constructor.
     *
     * @param BillServiceInterface $service
     * @param FilenameValidator $typesValidator
     * @param ExceptionCreator $exceptionCreator
     */
    public function __construct(private BillServiceInterface $service, private FilenameValidator $typesValidator, private ExceptionCreator $exceptionCreator)
    {
    }

    /**
     * @param SendBillRequest $request
     * @return SendBillResponse
     */
    public function sendBill(SendBillRequest $request): SendBillResponse
    {
        $allowedTypes = [
            DocumentType::FACTURA,
            DocumentType::BOLETA,
            DocumentType::NOTA_CREDITO,
            DocumentType::NOTA_DEBITO,
            DocumentType::PERCEPCION,
            DocumentType::RETENCION,
            DocumentType::GUIA_REMISION,
        ];
        $this->assertNotPack($request->fileName);
        if (!$this->typesValidator->isAllow($request->fileName, $allowedTypes)) {
            throw $this->exceptionCreator->fromValidation(
                new ValidationError(ErrorCodeList::ZIP_INVALID_NAME)
            );
        }

        return $this->service->sendBill($request);
    }

    /**
     * @param SendSummaryRequest $request
     * @return SendSummaryResponse
     */
    public function sendSummary(SendSummaryRequest $request): SendSummaryResponse
    {
        $this->assertNotPack($request->fileName);
        if (!$this->typesValidator->isAllow($request->fileName, self::SUMMARY_TYPES)) {
            throw $this->exceptionCreator->fromValidation(
                new ValidationError(ErrorCodeList::ZIP_INVALID_NAME)
            );
        }

        return $this->service->sendSummary($request);
    }

    /**
     * @param GetStatusRequest $request
     * @return GetStatusResponse
     */
    public function getStatus(GetStatusRequest $request): GetStatusResponse
    {
        return $this->service->getStatus($request);
    }

    /**
     * @param SendPackRequest $request
     * @return SendPackResponse
     */
    public function sendPack(SendPackRequest $request): SendPackResponse
    {
        if ($this->typesValidator->isAllow($request->fileName, self::SUMMARY_TYPES)) {
            throw $this->exceptionCreator->fromCode(ErrorCodeList::ZIP_IS_SUMMARY);
        }
        if (!$this->typesValidator->isAllow($request->fileName, [PackValidator::TYPE])) {
            throw $this->exceptionCreator->fromValidation(
                new ValidationError(ErrorCodeList::ZIP_INVALID_NAME)
            );
        }

        return $this->service->sendPack($request);
    }

    /**
     * @param GetStatusCdrRequest $request
     * @return GetStatusCdrResponse
     */
    public function getStatusCdr(GetStatusCdrRequest $request): GetStatusCdrResponse
    {
        return $this->service->getStatusCdr($request);
    }

    /**
     * Un lote solo se envía por sendPack.
     */
    private function assertNotPack(?string $fileName): void
    {
        if ($this->typesValidator->isAllow($fileName, [PackValidator::TYPE])) {
            throw $this->exceptionCreator->fromCode(ErrorCodeList::ZIP_IS_PACK);
        }
    }
}
