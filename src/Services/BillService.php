<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\Cdr\CdrOutputInterface;
use App\Services\Soap\ExceptionCreator;
use App\Services\Zip\XmlZipInterface;
use App\Validator\XmlValidatorInterface;
use App\Entity\{
    CpeCdrResult,
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
    StatusResponse
};
use DateTime;
use DOMDocument;
use Psr\Log\LoggerInterface;
use SoapFault;

class BillService implements BillServiceInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var XmlZipInterface
     */
    private $zipReader;

    /**
     * @var XmlValidatorInterface
     */
    private $xmlValidator;

    /**
     * @var ExceptionCreator
     */
    private $exceptionCreator;

    /**
     * @var CdrOutputInterface
     */
    private $cdrOut;

    public function sendBill(SendBillRequest $request): SendBillResponse
    {
        $dateReceived = new DateTime();

        $result = $this->zipReader->decompress($request->contentFile, $request->fileName);
        if ($result->getError() !== null) {
            throw $this->exceptionCreator->fromValidation($result->getError());
        }

        $doc = new DOMDocument();
        $doc->loadXML($result->getContent());

        $error = $this->xmlValidator->validate($request->fileName, $doc);
        if ($error !== null && (int)$error->getCode() < 2000) {
            throw $this->exceptionCreator->fromValidation($error);
        }

        $cdrResult = (new CpeCdrResult())
            ->setDateReceived($dateReceived)
            ->setCodeResult($error !== null ? $error->getCode() : '0')
            ->setNotes([]);

        $obj = new SendBillResponse();
        $obj->applicationResponse = $this->cdrOut->output($doc, $cdrResult);

        return $obj;
    }

    public function sendSummary(SendSummaryRequest $request): SendSummaryResponse
    {
        $obj = new SendSummaryResponse();
        $obj->ticket = (string)(int)(microtime(true) * 1000);

        return $obj;
    }

    public function getStatus(GetStatusRequest $request): GetStatusResponse
    {
        $ticket = $request->ticket;
        $this->logger->info('Ticket '.$ticket);

        $obj = new GetStatusResponse();
        $obj->status = new StatusResponse();
        $obj->status->content = 'xxxxxx';
        $obj->status->statusCode = $ticket;

        return $obj;
    }

    public function sendPack(SendPackRequest $request): SendPackResponse
    {
        throw new SoapFault('0000', 'NO IMPLEMENTADO');
    }

    public function getStatusCdr(GetStatusCdrRequest $request): GetStatusCdrResponse
    {
        throw new SoapFault('0000', 'NO IMPLEMENTADO');
    }
}
