<?php

declare(strict_types=1);

namespace App\Services\Cdr;

use App\Model\ApplicationResponse;
use App\Model\CpeCdrResult;
use App\Services\Xml\XmlParserInterface;
use DateTime;
use DOMDocument;
use Greenter\Ws\Reader\FilenameExtractorInterface;

class XmlAppCdrCreator implements AppCdrCreatorInterface
{
    private ?string $oseRuc;

    private FilenameExtractorInterface $filenameResolver;

    private XmlParserInterface $xmlParser;

    /**
     * XmlAppCdrCreator constructor.
     *
     * @param string|null $oseRuc
     * @param FilenameExtractorInterface $filenameResolver
     * @param XmlParserInterface $xmlParser
     */
    public function __construct(?string $oseRuc, FilenameExtractorInterface $filenameResolver, XmlParserInterface $xmlParser)
    {
        $this->oseRuc = $oseRuc;
        $this->filenameResolver = $filenameResolver;
        $this->xmlParser = $xmlParser;
    }

    public function create(DOMDocument $document, CpeCdrResult $result): ApplicationResponse
    {
        $docName = $this->filenameResolver->getFilename($document);
        $minDoc = $this->xmlParser->parse($document);
        $numCode = (int)$result->getCodeResult();

        return (new ApplicationResponse())
            ->setId($this->createId())
            ->setFechaRecepcion($result->getDateReceived())
            ->setFechaGeneracion(new DateTime())
            ->setRucEmisorCdr($this->oseRuc)
            ->setRucEmisorCpe(substr($docName, 0, 11))
            ->setTipoDocReceptorCpe($minDoc->getRecipientTypeDoc())
            ->setNroDocReceptorCpe($minDoc->getRecipient())
            ->setCpeId(substr($docName, 15, strlen($docName) - 15))
            ->setCodigoRespuesta($this->isObsCode($numCode) ? '0' : $result->getCodeResult())
            ->setDescripcionRespuesta($this->getDescription($numCode))
            ->setNotasAsociadas($result->getNotes())
            ->setFilename($docName)
        ;
    }

    private function createId(): string
    {
        return (string)(int)(microtime(true) * 1000);
    }

    private function getDescription(int $code): string
    {
        if ($this->isRejectCode($code)) {
            $state = 'rechazado';
        } elseif ($this->isObsCode($code)) {
            $state = 'aceptado con observaciones';
        } else {
            $state = 'aceptado';
        }

        return 'El comprobante ha sido '.$state.'.';
    }

    private function isRejectCode(int $code): bool
    {
        return 2000 <= $code && $code <= 3999;
    }

    private function isObsCode(int $code): bool
    {
        return $code >= 4000;
    }
}