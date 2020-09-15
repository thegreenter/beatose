<?php

declare(strict_types=1);

namespace App\Validator;

use App\Entity\ErrorCodeList;
use App\Entity\ValidationError;
use DOMDocument;
use Greenter\XMLSecLibs\Sunat\SignedXml;

class XmlSignValidator implements XmlValidatorInterface
{
    public function validate(string $filename, DOMDocument $document): ?ValidationError
    {
        $signValidator = new SignedXml();

        return $signValidator->verify($document) ? null : new ValidationError(ErrorCodeList::XML_ALTERADO);
    }
}