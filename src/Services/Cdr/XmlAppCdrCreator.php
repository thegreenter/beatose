<?php

declare(strict_types=1);

namespace App\Services\Cdr;

use App\Entity\ApplicationResponse;
use App\Entity\CpeCdrResult;
use DateTime;
use DOMDocument;

class XmlAppCdrCreator implements AppCdrCreatorInterface
{
    /**
     * @var FilenameResolverInterface
     */
    private $filenameResolver;

    /**
     * @var string
     */
    private $oseRuc;

    /**
     * XmlAppCdrCreator constructor.
     *
     * @param string $oseRuc
     * @param FilenameResolverInterface $filenameResolver
     */
    public function __construct(string $oseRuc, FilenameResolverInterface $filenameResolver)
    {
        $this->oseRuc = $oseRuc;
        $this->filenameResolver = $filenameResolver;
    }

    public function create(DOMDocument $document, CpeCdrResult $result): ApplicationResponse
    {
        $docName = $this->filenameResolver->getFilename($document);
        return (new ApplicationResponse())
            ->setId($this->createId())
            ->setFechaRecepcion($result->getDateReceived())
            ->setCodigoRespuesta($result->getCodeResult())
            ->setFechaGeneracion(new DateTime())
            ->setRucEmisorCdr($this->oseRuc)
            ->setRucEmisorCpe(substr($docName, 0, 11))
            ->setTipoDocReceptorCpe('6')
            ->setNroDocReceptorCpe('20000000002')
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