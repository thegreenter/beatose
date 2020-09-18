<?php

declare(strict_types=1);

namespace App\Model;

class GetStatusResponse
{
    /**
     * @var StatusResponse|null
     */
    public ?StatusResponse $status;

    public function __construct()
    {
        $this->status = new StatusResponse();
    }
}