<?php

declare(strict_types=1);

namespace App\Services\Cdr;

use App\Model\ApplicationResponse;

interface CdrWriterInterface
{
    /**
     * Write APP CDR to output format.
     *
     * @param ApplicationResponse $applicationResponse
     *
     * @return string|null
     */
    public function write(ApplicationResponse $applicationResponse): ?string;
}
