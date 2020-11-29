<?php

declare(strict_types=1);

namespace App\Services\Cdr;

use App\Model\ApplicationResponse;
use App\Services\Zip\XmlZipInterface;

class ZipCdrWriter implements CdrWriterInterface
{
    /**
     * ZipCdrWriter constructor.
     *
     * @param XmlZipInterface $compressor
     * @param CdrWriterInterface $xmlCdrWriter
     */
    public function __construct(private XmlZipInterface $compressor, private CdrWriterInterface $xmlCdrWriter)
    {
    }

    /**
     * @param ApplicationResponse $applicationResponse
     *
     * @return string|null
     */
    public function write(ApplicationResponse $applicationResponse): ?string
    {
        $xmlSigned = $this->xmlCdrWriter->write($applicationResponse);
        $zipFilename = 'R-' . $applicationResponse->getFilename() . '.zip';

        return $this->compressor->compress($zipFilename, $xmlSigned)->getContent();
    }
}