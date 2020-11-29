<?php

declare(strict_types=1);

namespace App\Model;

class SendBillRequest
{
    public ?string $fileName;

    public ?string $contentFile;
}