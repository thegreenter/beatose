<?php

declare(strict_types=1);

namespace App\Services\Soap;

use App\Entity\ValidationError;
use Greenter\Validator\ErrorCodeProviderInterface;
use SoapFault;

class ExceptionCreator
{
    /**
     * @var ErrorCodeProviderInterface
     */
    private $codeResolver;

    /**
     * ExceptionCreator constructor.
     *
     * @param ErrorCodeProviderInterface $codeResolver
     */
    public function __construct(ErrorCodeProviderInterface $codeResolver)
    {
        $this->codeResolver = $codeResolver;
    }

    public function fromValidation(ValidationError $error): SoapFault
    {
        return new SoapFault(
            $error->getCode(),
            $this->codeResolver->getValue($error->getCode()),
            null,
            $error->getDetail(),
        );
    }
}