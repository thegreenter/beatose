<?php

declare(strict_types=1);

namespace App\Services\Zip;

use App\Model\ErrorCodeList;
use App\Model\ValidationError;
use App\Model\ValueResult;
use PhpZip\Exception\ZipException;
use PhpZip\ZipFile;

class XmlZipFly implements XmlZipInterface
{
    public function compress(string $filename, ?string $content): ValueResult
    {
        $zipFile = new ZipFile();
        $zipFile->addFromString($filename, $content);
        $zip = $zipFile->outputAsString();
        $zipFile->close();

        return (new ValueResult())->setContent($zip);
    }

    public function decompress(?string $zip, string $filename): ValueResult
    {
        $zipFile = $this->openZip($zip);
        $result = new ValueResult();
        if ($zipFile === null) {
            return $result->setError(new ValidationError(ErrorCodeList::ZIP_CORRUPTO));
        }

        if ($zipFile->count() === 0) {
            return $result->setError(new ValidationError(ErrorCodeList::ZIP_EMPTY));
        }

        if (!$zipFile->hasEntry($filename)) {
            return $result->setError(new ValidationError(ErrorCodeList::ZIP_SIN_CPE));
        }

        try {
            $xml = $zipFile->getEntryContents($filename);
            return $result->setContent($xml);
        } catch (ZipException $e) {
            return $result->setError(new ValidationError(ErrorCodeList::ZIP_ERROR_UNZIP));
        } finally {
            $zipFile->close();
        }
    }

    private function openZip(?string $zip): ?ZipFile
    {
        try {
            $zipFile = new ZipFile();
            return $zipFile->openFromString($zip);
        } catch (ZipException $e) {
            return null;
        }
    }
}