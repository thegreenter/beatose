<?php

declare(strict_types=1);

namespace App\Services\Cdr;

use App\Model\CpeCdrResult;
use DOMDocument;

class CdrBridge implements CdrOutputInterface
{
    private AppCdrCreatorInterface $appCdrCreator;

    private CdrWriterInterface $cdrWriter;

    /**
     * CdrBridge constructor.
     *
     * @param AppCdrCreatorInterface $appCdrCreator
     * @param CdrWriterInterface $cdrWriter
     */
    public function __construct(AppCdrCreatorInterface $appCdrCreator, CdrWriterInterface $cdrWriter)
    {
        $this->appCdrCreator = $appCdrCreator;
        $this->cdrWriter = $cdrWriter;
    }

    public function output(DOMDocument $document, CpeCdrResult $result): ?string
    {
        $appCdr = $this->appCdrCreator->create($document, $result);

        return $this->cdrWriter->write($appCdr);
    }
}