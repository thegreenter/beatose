<?php

declare(strict_types=1);

namespace App\Validator;

use App\Entity\ValidationError;
use DOMDocument;

class CpeXslValidator implements XmlValidatorInterface
{
    public function validate(string $filename, DOMDocument $document): ?ValidationError
    {
        return null;
    }
}
