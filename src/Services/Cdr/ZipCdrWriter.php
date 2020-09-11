<?php

declare(strict_types=1);

namespace App\Services\Cdr;

use App\Entity\ApplicationResponse;
use App\Services\Zip\XmlZipInterface;

class ZipCdrWriter implements CdrWriterInterface
{
    /**
     * @var XmlZipInterface
     */
    private $compressor;

    /**
     * @var CdrWriterInterface
     */
    private $xmlCdrWriter;

    /**
     * ZipCdrWriter constructor.
     *
     * @param XmlZipInterface $compressor
     * @param CdrWriterInterface $xmlCdrWriter
     */
    public function __construct(XmlZipInterface $compressor, CdrWriterInterface $xmlCdrWriter)
    {
        $this->compressor = $compressor;
        $this->xmlCdrWriter = $xmlCdrWriter;
    }

    /**
     * @param ApplicationResponse $applicationResponse
     * @param string $name
     * @return string|null
     */
    public function write(ApplicationResponse $applicationResponse, string $name): ?string
    {
        $xmlSigned = $this->xmlCdrWriter->write($applicationResponse, $name);

        return $this->compressor->compress($name.'.xml', $xmlSigned)->getContent();
    }
}