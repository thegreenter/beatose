<?php

declare(strict_types=1);

namespace App\Validator;

use App\Entity\ErrorCodeList;
use App\Entity\ValidationError;
use DOMDocument;
use Greenter\XMLSecLibs\Sunat\SignedXml;

class XmlSignValidator implements XmlValidatorInterface
{
    /**
     * @var SignedXml
     */
    private $signer;

    /**
     * XmlSignValidator constructor.
     * @param SignedXml $signer
     */
    public function __construct(SignedXml $signer)
    {
        $this->signer = $signer;
    }

    public function validate(string $filename, DOMDocument $document): ?ValidationError
    {
        return $this->signer->verify($document) ? null : new ValidationError(ErrorCodeList::XML_ALTERADO);
    }
}