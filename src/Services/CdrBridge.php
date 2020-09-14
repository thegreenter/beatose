<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\CpeCdrResult;
use App\Services\Cdr\AppCdrCreatorInterface;
use App\Services\Cdr\CdrOutputInterface;
use App\Services\Cdr\CdrWriterInterface;
use DOMDocument;

class CdrBridge implements CdrOutputInterface
{
    /**
     * @var AppCdrCreatorInterface
     */
    private $appCdrCreator;

    /**
     * @var CdrWriterInterface
     */
    private $cdrWriter;

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