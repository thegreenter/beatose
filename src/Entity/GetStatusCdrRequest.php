<?php

declare(strict_types=1);

namespace App\Entity;

class GetStatusCdrRequest
{
    /**
     * @var string
     */
    public $rucComprobante;

    /**
     * @var string
     */
    public $tipoComprobante;

    /**
     * @var string
     */
    public $serieComprobante;

    /**
     * @var int
     */
    public $numeroComprobante;
}