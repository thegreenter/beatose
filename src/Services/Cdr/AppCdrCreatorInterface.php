<?php

declare(strict_types=1);

namespace App\Services\Cdr;

use App\Model\ApplicationResponse;
use App\Model\CpeCdrResult;
use DOMDocument;

interface AppCdrCreatorInterface
{
    /**
     * Create from CPE and code CDR.
     *
     * @param DOMDocument $document XML CPE document
     * @param CpeCdrResult $result CDR validation result
     *
     * @return ApplicationResponse
     */
    public function create(DOMDocument $document, CpeCdrResult $result): ApplicationResponse;
}