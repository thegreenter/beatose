<?php

declare(strict_types=1);

namespace App\Services\Soap;

use RuntimeException;

/**
 * Fault SOAP 1.1 que se devuelve al cliente (reemplaza a \SoapFault de ext-soap).
 *
 * El faultcode es el código de error de SUNAT (p. ej. "0156") o uno de SOAP
 * ("SOAP-ENV:Client", "SOAP-ENV:Server", "SOAP-ENV:VersionMismatch").
 */
final class SoapFault extends RuntimeException
{
    public const CLIENT = 'SOAP-ENV:Client';
    public const SERVER = 'SOAP-ENV:Server';
    public const VERSION_MISMATCH = 'SOAP-ENV:VersionMismatch';

    public function __construct(
        private readonly string $faultCode,
        private readonly string $faultString,
        private readonly ?string $detail = null,
    ) {
        parent::__construct($faultCode.': '.$faultString);
    }

    public function getFaultCode(): string
    {
        return $this->faultCode;
    }

    public function getFaultString(): string
    {
        return $this->faultString;
    }

    public function getDetail(): ?string
    {
        return $this->detail;
    }
}
