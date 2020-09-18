<?php

declare(strict_types=1);

namespace App\Services\Bill;

use App\Repository\CpeDocumentRepository;
use App\Services\Cdr\CdrOutputInterface;
use App\Services\File\FileStoreInterface;
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

    private FileStoreInterface $fileStore;

    /**
     * BillService constructor.
     * @param XmlValidatorInterface $xmlValidator
     * @param ExceptionCreator $exceptionCreator
     * @param CdrOutputInterface $cdrOut
     * @param CpeDocumentRepository $repository
     * @param FileStoreInterface $fileStore
     */
    public function __construct(XmlValidatorInterface $xmlValidator, ExceptionCreator $exceptionCreator, CdrOutputInterface $cdrOut, CpeDocumentRepository $repository, FileStoreInterface $fileStore)
    {
        $this->xmlValidator = $xmlValidator;
        $this->exceptionCreator = $exceptionCreator;
        $this->cdrOut = $cdrOut;
        $this->repository = $repository;
        $this->fileStore = $fileStore;
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

        $response = new SendBillResponse();
        $response->applicationResponse = $this->cdrOut->output($doc, $cdrResult);

        return $response;
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

        $response = new SendSummaryResponse();
        $response->ticket = $ticket;

        return $response;
    }

    public function getStatus(GetStatusRequest $request): GetStatusResponse
    {
        $ticket = $request->ticket;
        $cpe = $this->repository->findOneByTicket($ticket);

        if ($cpe === null) {
            throw $this->exceptionCreator->fromCode(ErrorCodeList::TICKET_NOTFOUND);
        }

        $response = new GetStatusResponse();
        $response->status->content = $this->fileStore->get('R-'.$cpe->getName().'.xml');
        $response->status->statusCode = '0';

        return $response;
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
        $response = new GetStatusCdrResponse();
        $response->statusCdr->statusCode = '0';
        $response->statusCdr->statusMessage = 'La constancia existe';
        $response->statusCdr->content = $this->fileStore->get('R-'.$cpe->getName().'.xml');

        return $response;
    }
}
