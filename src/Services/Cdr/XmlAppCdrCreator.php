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

        return (new ApplicationResponse())
            ->setId($this->createId())
            ->setFechaRecepcion($result->getDateReceived())
            ->setFechaGeneracion(new DateTime())
            ->setRucEmisorCdr($this->oseRuc)
            ->setRucEmisorCpe(substr($docName, 0, 11))
            ->setTipoDocReceptorCpe($minDoc->getRecipientTypeDoc())
            ->setNroDocReceptorCpe($minDoc->getRecipient())
            ->setCpeId(substr($docName, 15, strlen($docName) - 15))
            ->setCodigoRespuesta($result->getCodeResult())
            ->setDescripcionRespuesta($this->getDescription((int)$result->getCodeResult()))
            ->setNotasAsociadas($result->getNotes())
            ->setFilename($docName)
        ;
    }

    private function createId(): string
    {
        return (string)(int)(microtime(true) * 1000);
    }

    private function getDescription(int $code)
    {
        if (2000 <= $code && $code <= 3999) {
            $state = 'rechazado';
        } elseif ($code >= 4000) {
            $state = 'aceptado con observaciones';
        } else {
            $state = 'aceptado';
        }

        return 'El comprobante ha sido '.$state.'.';
    }
}