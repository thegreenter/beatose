<?php

declare(strict_types=1);

namespace App\Message;

/**
 * Pide procesar (validar y generar los CDR de) un lote recibido por sendPack.
 */
final readonly class ProcessPack
{
    public function __construct(
        public string $ticket,
        public string $fileName,
    ) {
    }
}
