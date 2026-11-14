<?php

declare(strict_types=1);

namespace App\Services\Zip;

use App\Model\ValidationError;
use RuntimeException;

/**
 * El ZIP de un lote no se puede leer; lleva el código de error de SUNAT.
 */
final class InvalidZipException extends RuntimeException
{
    public function __construct(private readonly ValidationError $error)
    {
        parent::__construct('ZIP inválido: '.$error->getCode());
    }

    public function getError(): ValidationError
    {
        return $this->error;
    }
}
