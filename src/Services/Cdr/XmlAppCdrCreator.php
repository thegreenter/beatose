<?php

declare(strict_types=1);

namespace App\Services\Cdr;

use App\Entity\ApplicationResponse;
use App\Entity\CpeCdrResult;
use App\Services\Xml\XmlParserInterface;
use DateTime;
use DOMDocument;
use Greenter\Ws\Reader\FilenameExtractorInterface;

class XmlAppCdrCreator implements AppCdrCreatorInterface
{
    /**
     * @var string
     */
    private $oseRuc;

    /**
     * @var FilenameExtractorInterface
     */
    private $filenameResolver;

    /**
     * @var XmlParserInterface
     */
    private $xmlParser;

    /**
     * XmlAppCdrCreator constructor.
     *
     * @param string $oseRuc
     * @param FilenameExtractorInterface $filenameResolver
     * @param XmlParserInterface $xmlParser
     */
    public function __construct(string $oseRuc, FilenameExtractorInterface $filenameResolver, XmlParserInterface $xmlParser)
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
            ->setCodigoRespuesta($result->getCodeResult())
            ->setFechaGeneracion(new DateTime())
            ->setRucEmisorCdr($this->oseRuc)
            ->setRucEmisorCpe(substr($docName, 0, 11))
            ->setTipoDocReceptorCpe($minDoc->getRecipientTypeDoc())
            ->setNroDocReceptorCpe($minDoc->getRecipient())
            ->setCpeId(substr($docName, 15, strlen($docName) - 15))
            ->setCodigoRespuesta($this->getDescription((int)$result->getCodeResult()))
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
        if ($code >= 4000) {
            $state = 'aceptado con observaciones';
        } elseif ($code >= 2000 && $code <= 3999) {
            $state = 'rechazado';
        } else {
            $state = 'aceptado';
        }

        return 'El comprobante ha sido '.$state.'.';
    }
}