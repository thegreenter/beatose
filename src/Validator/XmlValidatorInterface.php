<?php

namespace App\Validator;

use App\Entity\ValidationError;
use DOMDocument;

interface XmlValidatorInterface
{
    public function validate(string $filename, DOMDocument $document): ?ValidationError;
}
