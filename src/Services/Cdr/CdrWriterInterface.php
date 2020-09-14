<?php

declare(strict_types=1);

namespace App\Services\Cdr;

use App\Entity\ApplicationResponse;

interface CdrWriterInterface
{
    public function write(ApplicationResponse $applicationResponse, string $name): ?string;
}
