<?php

declare(strict_types=1);

namespace App\Entity;

class SendBillRequest
{
    /**
     * @var string
     */
    public $fileName;

    /**
     * @var string
     */
    public $contentFile;
}