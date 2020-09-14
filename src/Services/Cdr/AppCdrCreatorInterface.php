<?php

declare(strict_types=1);

namespace App\Services\Cdr;

use App\Entity\ApplicationResponse;
use DateTime;
use DOMDocument;

interface AppCdrCreatorInterface
{
    /**
     * Create from CPE and code CDR.
     *
     * @param DOMDocument $document XML CPE document
     * @param DateTime $received Receive datetime
     * @param string|null $codeResult Code CDR Result
     *
     * @return ApplicationResponse
     */
    public function create(DOMDocument $document, DateTime $received, ?string $codeResult): ApplicationResponse;
}