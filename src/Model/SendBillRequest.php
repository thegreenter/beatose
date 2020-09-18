<?php

declare(strict_types=1);

namespace App\Model;

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