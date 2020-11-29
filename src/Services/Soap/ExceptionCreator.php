<?php

declare(strict_types=1);

namespace App\Services\Soap;

use App\Model\ValidationError;
use Greenter\Validator\ErrorCodeProviderInterface;
use SoapFault;
use stdClass;

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
        $detail = null;
        if ($error->getDetail() !== null) {
            $detail = new stdClass();
            $detail->message = $error->getDetail();
        }

        return new SoapFault(
            $error->getCode(),
            $this->codeResolver->getValue($error->getCode()),
            detail: $detail,
        );
    }

    public function fromCode(string $code): SoapFault
    {
        return new SoapFault(
            $code,
            $this->codeResolver->getValue($code),
        );
    }
}