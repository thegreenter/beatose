<?php

declare(strict_types=1);

namespace App\Message;

/**
 * Pide procesar (validar y generar el CDR de) un resumen recibido por sendSummary.
 */
final readonly class ProcessSummary
{
    public function __construct(
        public string $ticket,
        public string $fileName,
    ) {
    }
}
