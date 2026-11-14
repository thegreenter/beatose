<?php

declare(strict_types=1);

namespace App\Services\Bill;

use App\Model\GetStatusCdrRequest;
use App\Model\GetStatusCdrResponse;
use App\Model\GetStatusRequest;
use App\Model\GetStatusResponse;
use App\Model\SendBillRequest;
use App\Model\SendBillResponse;
use App\Model\SendPackRequest;
use App\Model\SendPackResponse;
use App\Model\SendSummaryRequest;
use App\Model\SendSummaryResponse;
use App\Services\Soap\ExceptionCreator;
use App\Services\Zip\InvalidZipException;
use App\Services\Zip\XmlPackZip;
use App\Services\Zip\XmlZipInterface;
use App\Validator\PackValidator;

class ZipFormatBillDecorator implements BillServiceInterface
{
    /**
     * ZipFormatBillDecorator constructor.
     * @param BillServiceInterface $service
     * @param XmlZipInterface $zipper
     * @param ExceptionCreator $exceptionCreator
     * @param XmlPackZip $packZip
     * @param PackValidator $packValidator
     */
    public function __construct(private BillServiceInterface $service, private XmlZipInterface $zipper, private ExceptionCreator $exceptionCreator, private XmlPackZip $packZip, private PackValidator $packValidator)
    {
    }

    /**
     * @param SendBillRequest $request
     * @return SendBillResponse
     */
    public function sendBill(SendBillRequest $request): SendBillResponse
    {
        $result = $this->zipper->decompress($request->contentFile, $request->fileName);
        if ($result->getError() !== null) {
            throw $this->exceptionCreator->fromValidation($result->getError());
        }

        $request->contentFile = $result->getContent();
        $response = $this->service->sendBill($request);

        $response->applicationResponse = $this->zipper->compress('R-'.$request->fileName, $response->applicationResponse)->getContent();

        return $response;
    }

    /**
     * @param SendSummaryRequest $request
     * @return SendSummaryResponse
     */
    public function sendSummary(SendSummaryRequest $request): SendSummaryResponse
    {
        $result = $this->zipper->decompress($request->contentFile, $request->fileName);
        if ($result->getError() !== null) {
            throw $this->exceptionCreator->fromValidation($result->getError());
        }

        $request->contentFile = $result->getContent();
        return $this->service->sendSummary($request);
    }

    /**
     * @param GetStatusRequest $request
     * @return GetStatusResponse
     */
    public function getStatus(GetStatusRequest $request): GetStatusResponse
    {
        $response = $this->service->getStatus($request);

        $xmlContent = $response->status->content;
        // El CDR de un lote ya viene como ZIP (un CDR por comprobante).
        if ($xmlContent !== null && !$this->isZip($xmlContent)) {
            $name = $request->ticket;
            $response->status->content = $this->zipper->compress('R-'.$name.'.zip', $xmlContent)->getContent();
        }

        return $response;
    }

    /**
     * @param SendPackRequest $request
     * @return SendPackResponse
     */
    public function sendPack(SendPackRequest $request): SendPackResponse
    {
        // El ZIP del lote se valida al recibirlo; se descomprime de nuevo al procesarlo (PackProcessor).
        try {
            $files = $this->packZip->decompress($request->contentFile);
        } catch (InvalidZipException $e) {
            throw $this->exceptionCreator->fromValidation($e->getError());
        }

        $error = $this->packValidator->validate((string) $request->fileName, $files);
        if ($error !== null) {
            throw $this->exceptionCreator->fromValidation($error);
        }

        return $this->service->sendPack($request);
    }

    /**
     * @param GetStatusCdrRequest $request
     * @return GetStatusCdrResponse
     */
    public function getStatusCdr(GetStatusCdrRequest $request): GetStatusCdrResponse
    {
        $response = $this->service->getStatusCdr($request);
        $xmlContent = $response->statusCdr->content;
        if ($xmlContent !== null) {
            $name = implode('-', [
                $request->rucComprobante,
                $request->tipoComprobante,
                $request->serieComprobante,
                $request->numeroComprobante,
            ]);
            $response->statusCdr->content = $this->zipper->compress('R-'.$name.'.zip', $xmlContent)->getContent();
        }

        return $response;
    }

    private function isZip(string $content): bool
    {
        return str_starts_with($content, "PK\x03\x04");
    }
}
