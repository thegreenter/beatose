<?php

declare(strict_types=1);

namespace App\Services\Bill;

use App\Model\CpeCdrResult;
use App\Model\CpeDocument;
use App\Model\ErrorCodeList;
use App\Model\ValidationError;
use App\Repository\CpeDocumentRepository;
use App\Services\Cdr\CdrOutputInterface;
use App\Services\File\FileStoreInterface;
use App\Services\Zip\InvalidZipException;
use App\Services\Zip\XmlPackZip;
use App\Validator\XmlValidatorInterface;
use DOMDocument;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * Procesa en segundo plano un lote recibido por sendPack.
 *
 * Cada comprobante se valida y obtiene su propio CDR (consultable también con
 * getStatusCdr). El CDR del lote es un ZIP con un R-{nombre}.xml por comprobante.
 *
 * Estados que ve getStatus, guardados en el registro del lote (cpe_document):
 *  - sin state_code ni CDR: en proceso (98)
 *  - state_code de excepción (0100-1999) sin CDR: un comprobante no se pudo
 *    procesar y el lote completo se rechaza, sin generar ningún CDR
 *  - con CDR: 0 si todos fueron aceptados, 99 si alguno fue rechazado
 *    (state_code es el código del primer rechazo)
 */
class PackProcessor
{
    public function __construct(
        private XmlValidatorInterface $xmlValidator,
        private CdrOutputInterface $cdrOut,
        private CpeDocumentRepository $repository,
        private FileStoreInterface $fileStore,
        private XmlPackZip $packZip,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * Nombre del archivo donde se guarda el ZIP del lote mientras espera su proceso.
     */
    public static function pendingFile(string $ticket): string
    {
        return 'ticket-'.$ticket.'.zip';
    }

    /**
     * Nombre del archivo con el ZIP de CDRs del lote.
     */
    public static function cdrFile(string $hashCdr): string
    {
        return bin2hex((string) base64_decode($hashCdr, true)).'.zip';
    }

    public function process(string $ticket, string $fileName): void
    {
        $cpe = $this->repository->findOneByTicket($ticket);
        if ($cpe === null) {
            $this->logger->warning('Ticket no encontrado al procesar el lote.', ['ticket' => $ticket]);

            return;
        }

        try {
            $zip = $this->fileStore->get(self::pendingFile($ticket));
            if ($zip === null) {
                throw new \RuntimeException('No se encontró el ZIP pendiente del ticket '.$ticket);
            }

            $this->processFiles($cpe, $this->packZip->decompress($zip));
        } catch (InvalidZipException $e) {
            $this->repository->save($cpe->setStateCode($e->getError()->getCode()));
        } catch (Throwable $e) {
            $this->logger->error('Error al procesar el lote.', ['ticket' => $ticket, 'file' => $fileName, 'exception' => $e]);
            $this->repository->save($cpe->setStateCode(ErrorCodeList::XML_CANNOT_PROCESS));
        }

        $this->fileStore->delete(self::pendingFile($ticket));
    }

    /**
     * @param array<string, string> $files nombre de cada XML => contenido
     */
    private function processFiles(CpeDocument $cpe, array $files): void
    {
        // Primero se validan todos: una excepción en cualquiera rechaza el lote sin generar CDRs.
        $validated = [];
        foreach ($files as $name => $xml) {
            $name = (string) $name;
            $doc = $this->loadXml($xml);
            $errors = $this->xmlValidator->validate(pathinfo($name, PATHINFO_FILENAME).'.zip', $doc);

            if (count($errors) > 0 && $errors[0]->getCode() < 2000) {
                $this->logger->info('Lote rechazado por un comprobante.', [
                    'ticket' => $cpe->getTicket(),
                    'file' => $name,
                    'code' => $errors[0]->getCode(),
                ]);
                $this->repository->save($cpe->setStateCode($errors[0]->getCode()));

                return;
            }

            $validated[$name] = [$doc, $errors];
        }

        $cdrs = [];
        $packCode = '0';
        foreach ($validated as $name => [$doc, $errors]) {
            $code = $this->resultCode($errors);
            $result = (new CpeCdrResult())
                ->setDateReceived($cpe->getCreateDate())
                ->setCodeResult($code)
                ->setErrorList($errors);

            $cdrs['R-'.$name] = (string) $this->cdrOut->output($doc, $result);

            if ($packCode === '0' && $this->isRejectCode($code)) {
                $packCode = $code;
            }
        }

        $zip = $this->packZip->compress($cdrs);
        $hashCdr = base64_encode(hash('sha256', $zip, true));
        $this->fileStore->save(self::cdrFile($hashCdr), $zip);

        $this->repository->save($cpe->setStateCode($packCode)->setHashCdr($hashCdr));
    }

    /**
     * @param ValidationError[] $errors
     */
    private function resultCode(array $errors): string
    {
        return count($errors) > 0 ? $errors[0]->getCode() : '0';
    }

    private function isRejectCode(string $code): bool
    {
        return 2000 <= (int) $code && (int) $code <= 3999;
    }

    private function loadXml(string $xml): DOMDocument
    {
        $doc = new DOMDocument();
        $previous = libxml_use_internal_errors(true);
        $doc->loadXML($xml);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        return $doc;
    }
}
