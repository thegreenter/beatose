<?php

declare(strict_types=1);

namespace App\Services\Bill;

use App\Model\CpeCdrResult;
use App\Model\ErrorCodeList;
use App\Repository\CpeDocumentRepository;
use App\Services\Cdr\CdrOutputInterface;
use App\Services\File\FileStoreInterface;
use App\Validator\XmlValidatorInterface;
use DOMDocument;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * Procesa en segundo plano un resumen (RC, RA, RR) recibido por sendSummary.
 *
 * Estados que ve getStatus, guardados en cpe_document:
 *  - sin state_code ni CDR: en proceso (98)
 *  - state_code de excepción (0100-1999) sin CDR: error de proceso
 *  - con CDR: aceptado (0) o rechazado (99) según state_code
 */
class SummaryProcessor
{
    public function __construct(
        private XmlValidatorInterface $xmlValidator,
        private CdrOutputInterface $cdrOut,
        private CpeDocumentRepository $repository,
        private FileStoreInterface $fileStore,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * Nombre del archivo donde se guarda el XML mientras espera su proceso.
     */
    public static function pendingFile(string $ticket): string
    {
        return 'ticket-'.$ticket.'.xml';
    }

    public function process(string $ticket, string $fileName): void
    {
        $cpe = $this->repository->findOneByTicket($ticket);
        if ($cpe === null) {
            $this->logger->warning('Ticket no encontrado al procesar el resumen.', ['ticket' => $ticket]);

            return;
        }

        try {
            $xml = $this->fileStore->get(self::pendingFile($ticket));
            if ($xml === null) {
                throw new \RuntimeException('No se encontró el XML pendiente del ticket '.$ticket);
            }

            $doc = new DOMDocument();
            $previous = libxml_use_internal_errors(true);
            $doc->loadXML($xml);
            libxml_clear_errors();
            libxml_use_internal_errors($previous);

            $errors = $this->xmlValidator->validate($fileName, $doc);
            if (count($errors) > 0 && $errors[0]->getCode() < 2000) {
                $this->repository->save($cpe->setStateCode($errors[0]->getCode()));
            } else {
                $result = (new CpeCdrResult())
                    ->setDateReceived($cpe->getCreateDate())
                    ->setCodeResult(count($errors) > 0 ? $errors[0]->getCode() : '0')
                    ->setErrorList($errors)
                    ->setTicket($ticket);
                $this->cdrOut->output($doc, $result);
            }
        } catch (Throwable $e) {
            $this->logger->error('Error al procesar el resumen.', ['ticket' => $ticket, 'exception' => $e]);
            $this->repository->save($cpe->setStateCode(ErrorCodeList::XML_CANNOT_PROCESS));
        }

        $this->fileStore->delete(self::pendingFile($ticket));
    }
}
