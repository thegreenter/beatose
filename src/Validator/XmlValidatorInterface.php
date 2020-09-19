<?php

namespace App\Validator;

use App\Model\ValidationError;
use DOMDocument;

interface XmlValidatorInterface
{
    /**
     * Validate Document.
     *
     * @param string $filename
     * @param DOMDocument $document
     *
     * @return ValidationError[]
     */
    public function validate(string $filename, DOMDocument $document): array;
}
