<?php

declare(strict_types=1);

namespace App\Services\Bill;

use App\Repository\CpeDocumentRepository;
use App\Services\Cdr\CdrOutputInterface;
use App\Services\Soap\ExceptionCreator;
use App\Validator\XmlValidatorInterface;
use App\Model\{CpeCdrResult,
    ErrorCodeList,
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
};
use DateTime;
use DOMDocument;

class BillService implements BillServiceInterface
{
    private XmlValidatorInterface $xmlValidator;

    private ExceptionCreator $exceptionCreator;

    private CdrOutputInterface $cdrOut;

    private CpeDocumentRepository $repository;

    /**
     * BillService constructor.
     * @param XmlValidatorInterface $xmlValidator
     * @param ExceptionCreator $exceptionCreator
     * @param CdrOutputInterface $cdrOut
     * @param CpeDocumentRepository $repository
     */
    public function __construct(XmlValidatorInterface $xmlValidator, ExceptionCreator $exceptionCreator, CdrOutputInterface $cdrOut, CpeDocumentRepository $repository)
    {
        $this->xmlValidator = $xmlValidator;
        $this->exceptionCreator = $exceptionCreator;
        $this->cdrOut = $cdrOut;
        $this->repository = $repository;
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
            ->setNotes($error !== null ? [$error->getCode().'-'.$error->getDetail()] : [])
            ->setTicket(null)
        ;

        $obj = new SendBillResponse();
        $obj->applicationResponse = $this->cdrOut->output($doc, $cdrResult);

        return $obj;
    }

    public function sendSummary(SendSummaryRequest $request): SendSummaryResponse
    {
        $dateReceived = new DateTime();
        $doc = new DOMDocument();
        $doc->loadXML($request->contentFile);

        $error = $this->xmlValidator->validate($request->fileName, $doc);
        if ($error !== null && (int)$error->getCode() < 2000) {
            throw $this->exceptionCreator->fromValidation($error);
        }

        $ticket = (string)(int)(microtime(true) * 1000);
        $cdrResult = (new CpeCdrResult())
            ->setDateReceived($dateReceived)
            ->setCodeResult($error !== null ? $error->getCode() : '0')
            ->setNotes($error !== null ? [$error->getCode().'-'.$error->getDetail()] : [])
            ->setTicket($ticket)
        ;
        $this->cdrOut->output($doc, $cdrResult);

        $obj = new SendSummaryResponse();
        $obj->ticket = $ticket;

        return $obj;
    }

    public function getStatus(GetStatusRequest $request): GetStatusResponse
    {
        $ticket = $request->ticket;
        $cpe = $this->repository->findOneByTicket($ticket);

        if ($cpe === null) {
            throw $this->exceptionCreator->fromCode(ErrorCodeList::TICKET_NOTFOUND);
        }

        $obj = new GetStatusResponse();
        $obj->status->content = file_get_contents('R-'.$cpe->getName().'.xml');
        $obj->status->statusCode = '0';

        return $obj;
    }

    public function sendPack(SendPackRequest $request): SendPackResponse
    {
        throw $this->exceptionCreator->fromCode(ErrorCodeList::XML_CANNOT_PROCESS);
    }

    public function getStatusCdr(GetStatusCdrRequest $request): GetStatusCdrResponse
    {
        $name = implode('-', [
            $request->rucComprobante,
            $request->tipoComprobante,
            $request->serieComprobante,
            $request->numeroComprobante,
        ]);
        $cpe = $this->repository->findOneByName($name);

        if ($cpe === null) {
            throw $this->exceptionCreator->fromCode(ErrorCodeList::TICKET_NOTFOUND);
        }
        $obj = new GetStatusCdrResponse();
        $obj->statusCdr->statusCode = $cpe->getStateCode();
        $obj->statusCdr->statusMessage = $cpe->getStateCode() === '0' ? 'ACEPTADO' : 'RECHAZADO';
        $obj->statusCdr->content = file_get_contents('R-'.$cpe->getName().'.xml');

        return $obj;
    }
}
