<?php

declare(strict_types=1);

namespace App\Model;

class GetStatusCdrRequest
{
    public ?string $rucComprobante;

    public ?string $tipoComprobante;

    public ?string $serieComprobante;

    public ?int $numeroComprobante;
}