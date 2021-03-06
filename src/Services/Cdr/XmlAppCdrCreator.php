<?php

declare(strict_types=1);

namespace App\Services\Cdr;

use App\Model\ApplicationResponse;
use App\Model\CpeCdrResult;
use App\Model\ValidationError;
use App\Services\Xml\XmlParserInterface;
use DateTime;
use DOMDocument;
use Greenter\Validator\ErrorCodeProviderInterface;
use Greenter\Ws\Reader\FilenameExtractorInterface;

class XmlAppCdrCreator implements AppCdrCreatorInterface
{
    /**
     * XmlAppCdrCreator constructor.
     * @param string|null $oseRuc
     * @param FilenameExtractorInterface $filenameResolver
     * @param XmlParserInterface $xmlParser
     * @param ErrorCodeProviderInterface $errorCodes
     */
    public function __construct(private ?string $oseRuc, private FilenameExtractorInterface $filenameResolver, private XmlParserInterface $xmlParser, private ErrorCodeProviderInterface $errorCodes)
    {
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
            ->setCpeId($minDoc->getId())
            ->setCodigoRespuesta($this->isObsCode($numCode) ? '0' : $result->getCodeResult())
            ->setDescripcionRespuesta($this->getDescription($numCode))
            ->setNotasAsociadas($this->getNotes($result->getErrorList()))
            ->setFilename($docName)
        ;
    }

    private function createId(): string
    {
        return (string)(int)(microtime(true) * 1000);
    }

    /**
     * @param ValidationError[] $errorList
     * @return string[]
     */
    private function getNotes(?array $errorList): array
    {
        if ($errorList === null || count($errorList) === 0) {
            return [];
        }

        return array_map(fn($error) =>
            implode(' - ', [
                $error->getCode(),
                $this->errorCodes->getValue($error->getCode()),
                $error->getDetail(),
            ]),
            $errorList,
        );
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