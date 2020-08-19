<?php

declare(strict_types=1);

namespace App\Entity;

class SendSummaryRequest
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