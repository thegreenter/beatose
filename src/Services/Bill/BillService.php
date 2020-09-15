<?php

declare(strict_types=1);

namespace App\Services\Bill;

use App\Services\Cdr\CdrOutputInterface;
use App\Services\Soap\ExceptionCreator;
use App\Services\Zip\XmlZipInterface;
use App\Validator\XmlValidatorInterface;
use App\Model\{
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
    StatusResponse,
};
use DateTime;
use DOMDocument;
use Psr\Log\LoggerInterface;
use SoapFault;

class BillService implements BillServiceInterface
{
    private LoggerInterface $logger;

    private XmlZipInterface $zipReader;

    private XmlValidatorInterface $xmlValidator;

    private ExceptionCreator $exceptionCreator;

    private CdrOutputInterface $cdrOut;

    /**
     * BillService constructor.
     * @param LoggerInterface $logger
     * @param XmlZipInterface $zipReader
     * @param XmlValidatorInterface $xmlValidator
     * @param ExceptionCreator $exceptionCreator
     * @param CdrOutputInterface $cdrOut
     */
    public function __construct(LoggerInterface $logger, XmlZipInterface $zipReader, XmlValidatorInterface $xmlValidator, ExceptionCreator $exceptionCreator, CdrOutputInterface $cdrOut)
    {
        $this->logger = $logger;
        $this->zipReader = $zipReader;
        $this->xmlValidator = $xmlValidator;
        $this->exceptionCreator = $exceptionCreator;
        $this->cdrOut = $cdrOut;
    }

    public function sendBill(SendBillRequest $request): SendBillResponse
    {
        $dateReceived = new DateTime();

        $doc = new DOMDocument();
        $doc->loadXML($request->contentFile);

        $error = $this->xmlValidator->validate($request->fileName, $doc);
        if ($error !== null && (int)$error->getCode() < 2000) {
            throw $this->exceptionCreator->fromValidation($error);
        }

        $cdrResult = (new CpeCdrResult())
            ->setDateReceived($dateReceived)
            ->setCodeResult($error !== null ? $error->getCode() : '0')
            ->setNotes([$error->getCode().'-'.$error->getDetail()]);

        $obj = new SendBillResponse();
        $obj->applicationResponse = $this->cdrOut->output($doc, $cdrResult);

        return $obj;
    }

    public function sendSummary(SendSummaryRequest $request): SendSummaryResponse
    {
        $doc = new DOMDocument();
        $doc->loadXML($request->contentFile);

        $error = $this->xmlValidator->validate($request->fileName, $doc);
        if ($error !== null && (int)$error->getCode() < 2000) {
            throw $this->exceptionCreator->fromValidation($error);
        }

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
