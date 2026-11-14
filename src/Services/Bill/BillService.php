<?php

declare(strict_types=1);

namespace App\Services\Bill;

use App\Message\ProcessSummary;
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
use App\Model\CpeDocument;
use DateTime;
use DOMDocument;
use Symfony\Component\Messenger\MessageBusInterface;
use function Safe\base64_decode;

class BillService implements BillServiceInterface
{
    private const STATUS_IN_PROCESS = '98';
    private const STATUS_WITH_ERRORS = '99';

    /**
     * BillService constructor.
     * @param XmlValidatorInterface $xmlValidator
     * @param ExceptionCreator $exceptionCreator
     * @param CdrOutputInterface $cdrOut
     * @param CpeDocumentRepository $repository
     * @param FileStoreInterface $fileStore
     * @param MessageBusInterface $bus
     */
    public function __construct(private XmlValidatorInterface $xmlValidator, private ExceptionCreator $exceptionCreator, private CdrOutputInterface $cdrOut, private CpeDocumentRepository $repository, private FileStoreInterface $fileStore, private MessageBusInterface $bus)
    {
    }

    /**
     * @inheritDoc
     */
    public function sendBill(SendBillRequest $request): SendBillResponse
    {
        $dateReceived = new DateTime();

        $doc = new DOMDocument();
        $doc->loadXML($request->contentFile);

        $errors = $this->xmlValidator->validate($request->fileName, $doc);
        if (count($errors) > 0 && $errors[0]->getCode() < 2000) {
            throw $this->exceptionCreator->fromValidation($errors[0]);
        }

        $cdrResult = (new CpeCdrResult())
            ->setDateReceived($dateReceived)
            ->setCodeResult(count($errors) > 0 ? $errors[0]->getCode() : '0')
            ->setErrorList($errors)
        ;

        $response = new SendBillResponse();
        $response->applicationResponse = $this->cdrOut->output($doc, $cdrResult);

        return $response;
    }

    /**
     * @inheritDoc
     */
    public function sendSummary(SendSummaryRequest $request): SendSummaryResponse
    {
        // Como en SUNAT, aquí solo se valida el nombre y el ZIP (decoradores);
        // el contenido se valida en segundo plano y su resultado se consulta con getStatus.
        $ticket = $this->createTicket();
        $name = pathinfo((string) $request->fileName, PATHINFO_FILENAME);

        $cpe = ($this->repository->findOneByName($name) ?? new CpeDocument())
            ->setName($name)
            ->setTicket($ticket)
            ->setStateCode(null)
            ->setHashCpe(null)
            ->setHashCdr(null)
            ->setCreateDate(new DateTime());

        $this->fileStore->save(SummaryProcessor::pendingFile($ticket), $request->contentFile);
        $this->repository->save($cpe);
        $this->bus->dispatch(new ProcessSummary($ticket, (string) $request->fileName));

        $response = new SendSummaryResponse();
        $response->ticket = $ticket;

        return $response;
    }

    /**
     * @inheritDoc
     * @throws \Safe\Exceptions\UrlException
     */
    public function getStatus(GetStatusRequest $request): GetStatusResponse
    {
        $ticket = $request->ticket;
        $cpe = $this->repository->findOneByTicket($ticket);

        if ($cpe === null) {
            throw $this->exceptionCreator->fromCode(ErrorCodeList::TICKET_NOTFOUND);
        }

        $response = new GetStatusResponse();
        $response->status->content = null;

        if ($cpe->getHashCdr() === null) {
            // Sin CDR: en proceso (98) o error de excepción (0100-1999).
            $response->status->statusCode = $cpe->getStateCode() ?? self::STATUS_IN_PROCESS;

            return $response;
        }

        $code = (int) $cpe->getStateCode();
        $response->status->statusCode = (2000 <= $code && $code <= 3999) ? self::STATUS_WITH_ERRORS : '0';
        $response->status->content = $this->fileStore->get(bin2hex(base64_decode($cpe->getHashCdr())).'.xml');

        return $response;
    }

    /**
     * @inheritDoc
     */
    public function sendPack(SendPackRequest $request): SendPackResponse
    {
        throw $this->exceptionCreator->fromCode(ErrorCodeList::XML_CANNOT_PROCESS);
    }

    /**
     * @inheritDoc
     * @throws \Safe\Exceptions\UrlException
     */
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
        $response->statusCdr->content = $this->fileStore->get(bin2hex(base64_decode($cpe->getHashCdr())).'.xml');

        return $response;
    }

    private function createTicket(): string
    {
        // Milisegundos + 2 dígitos aleatorios para evitar colisiones en el mismo milisegundo.
        return sprintf('%d%02d', (int) (microtime(true) * 1000), random_int(0, 99));
    }
}
