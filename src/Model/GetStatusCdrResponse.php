<?php

declare(strict_types=1);

namespace App\Model;

class GetStatusCdrResponse
{
    /**
     * @var StatusCdrResponse|null
     */
    public ?StatusCdrResponse $statusCdr;

    public function __construct()
    {
        $this->statusCdr = new StatusCdrResponse();
    }
}