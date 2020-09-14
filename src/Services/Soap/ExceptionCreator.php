<?php

declare(strict_types=1);

namespace App\Services\Soap;

use App\Entity\ValidationError;
use SoapFault;

class ExceptionCreator
{
    public function fromValidation(ValidationError $error): SoapFault
    {
        return new SoapFault(
            $error->getCode(),
            '--from-xcodes-provider-',
            null,
            $error->getDetail(),
        );
    }
}