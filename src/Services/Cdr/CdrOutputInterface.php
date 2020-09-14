<?php

declare(strict_types=1);

namespace App\Services\Cdr;

use App\Entity\CpeCdrResult;
use DOMDocument;

interface CdrOutputInterface
{
    public function output(DOMDocument $document, CpeCdrResult $result): ?string;
}