<?php

declare(strict_types=1);

namespace App\Services\Soap;

use App\Model\ValidationError;
use Greenter\Validator\ErrorCodeProviderInterface;

class ExceptionCreator
{
    /**
     * ExceptionCreator constructor.
     *
     * @param ErrorCodeProviderInterface $codeResolver
     */
    public function __construct(private ErrorCodeProviderInterface $codeResolver)
    {
    }

    public function fromValidation(ValidationError $error): SoapFault
    {
        return new SoapFault(
            $error->getCode(),
            (string) $this->codeResolver->getValue($error->getCode()),
            $error->getDetail(),
        );
    }

    public function fromCode(string $code): SoapFault
    {
        return new SoapFault(
            $code,
            (string) $this->codeResolver->getValue($code),
        );
    }
}