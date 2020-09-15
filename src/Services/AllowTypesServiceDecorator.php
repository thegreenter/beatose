<?php

namespace App\Services;

use App\Services\Soap\ExceptionCreator;
use App\Validator\FilenameValidator;
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

class AllowTypesServiceDecorator implements BillServiceInterface
{
    private BillServiceInterface $service;

    private FilenameValidator $typesValidator;

    private ExceptionCreator $exceptionCreator;

    /**
     * AllowTypesServiceDecorator constructor.
     *
     * @param BillServiceInterface $service
     * @param FilenameValidator $typesValidator
     * @param ExceptionCreator $exceptionCreator
     */
    public function __construct(BillServiceInterface $service, FilenameValidator $typesValidator, ExceptionCreator $exceptionCreator)
    {
        $this->service = $service;
        $this->typesValidator = $typesValidator;
        $this->exceptionCreator = $exceptionCreator;
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
        $allowedTypes = [
            DocumentType::RESUMEN_DIARIO,
            DocumentType::COMUNICACION_BAJA,
            DocumentType::RESUMEN_REVERSION,
        ];
        if (!$this->typesValidator->isAllow($request->fileName, $allowedTypes)) {
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
}