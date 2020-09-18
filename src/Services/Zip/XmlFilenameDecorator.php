<?php

declare(strict_types=1);

namespace App\Services\Zip;

use App\Model\ValidationError;
use App\Model\ValueResult;

class XmlFilenameDecorator implements XmlZipInterface
{
    /**
     * @var XmlZipInterface
     */
    private $xmlZip;

    /**
     * XmlFilenameDecorator constructor.
     * @param XmlZipInterface $xmlZip
     */
    public function __construct(XmlZipInterface $xmlZip)
    {
        $this->xmlZip = $xmlZip;
    }

    /**
     * @param string $filename
     * @param string|null $content
     * @return ValueResult
     */
    public function compress(string $filename, ?string $content): ValueResult
    {
        $info = pathinfo($filename);

        if (strtoupper($info['extension']) !== 'ZIP') {
            return (new ValueResult())->setError(new ValidationError('0151'));
        }

        $xmlFilename = $info['filename'].'.xml';
        return $this->xmlZip->compress($xmlFilename, $content);
    }

    /**
     * @param string|null $zip
     * @param string $filename
     * @return ValueResult
     */
    public function decompress(?string $zip, string $filename): ValueResult
    {
        $info = pathinfo($filename);

        if (strtoupper($info['extension']) !== 'ZIP') {
            return (new ValueResult())->setError(new ValidationError('0151'));
        }

        $xmlFilename = $info['filename'].'.xml';
        return $this->xmlZip->decompress($zip, $xmlFilename);
    }
}