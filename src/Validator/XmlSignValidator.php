<?php

declare(strict_types=1);

namespace App\Validator;

use App\Model\ErrorCodeList;
use App\Model\ValidationError;
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