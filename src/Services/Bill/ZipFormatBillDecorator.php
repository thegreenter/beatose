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
use App\Services\Zip\XmlZipInterface;
use Greenter\Ws\Reader\FilenameExtractorInterface;

class ZipFormatBillDecorator implements BillServiceInterface
{
    private BillServiceInterface $service;

    private XmlZipInterface $zipper;

    private FilenameExtractorInterface $filenameResolver;

    private ExceptionCreator $exceptionCreator;

    /**
     * ZipFormatBillDecorator constructor.
     *
     * @param BillServiceInterface $service
     * @param XmlZipInterface $zipper
     * @param FilenameExtractorInterface $filenameResolver
     * @param ExceptionCreator $exceptionCreator
     */
    public function __construct(BillServiceInterface $service, XmlZipInterface $zipper, FilenameExtractorInterface $filenameResolver, ExceptionCreator $exceptionCreator)
    {
        $this->service = $service;
        $this->zipper = $zipper;
        $this->filenameResolver = $filenameResolver;
        $this->exceptionCreator = $exceptionCreator;
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

        $response->applicationResponse = $this->zipper->compress('R-'.$request->fileName, $response->applicationResponse);

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
        if ($xmlContent !== null) {
            $name = $this->filenameResolver->getFilename($xmlContent);
            $response->status->content = $this->zipper->compress('R-'.$name.'.zip', $xmlContent);
        }

        return $response;
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
        $response = $this->service->getStatusCdr($request);
        $xmlContent = $response->statusCdr->content;
        if ($xmlContent !== null) {
            $name = $this->filenameResolver->getFilename($xmlContent);
            $response->statusCdr->content = $this->zipper->compress('R-'.$name.'.zip', $xmlContent);
        }

        return $response;
    }
}