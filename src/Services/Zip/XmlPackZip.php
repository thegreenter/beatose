<?php

declare(strict_types=1);

namespace App\Services\Zip;

use App\Model\ErrorCodeList;
use App\Model\ValidationError;
use PhpZip\Constants\ZipCompressionMethod;
use PhpZip\Exception\InvalidArgumentException;
use PhpZip\Exception\ZipException;
use PhpZip\ZipFile;

/**
 * ZIP con varios archivos: el lote que recibe sendPack (un XML por comprobante)
 * y la respuesta de su getStatus (un CDR por comprobante).
 */
class XmlPackZip
{
    /**
     * @return array<string, string> nombre de cada archivo => contenido, en el orden del ZIP
     *
     * @throws InvalidZipException
     */
    public function decompress(?string $zip): array
    {
        try {
            $zipFile = (new ZipFile())->openFromString((string) $zip);
        } catch (ZipException | InvalidArgumentException) {
            // InvalidArgumentException: contenido vacío.
            throw new InvalidZipException(new ValidationError(ErrorCodeList::ZIP_CORRUPTO));
        }

        try {
            if ($zipFile->count() === 0) {
                throw new InvalidZipException(new ValidationError(ErrorCodeList::ZIP_EMPTY));
            }

            $files = [];
            foreach ($zipFile->getEntries() as $entry) {
                if ($entry->isDirectory()) {
                    continue;
                }
                $files[$entry->getName()] = $zipFile->getEntryContents($entry->getName());
            }
        } catch (ZipException) {
            throw new InvalidZipException(new ValidationError(ErrorCodeList::ZIP_ERROR_UNZIP));
        } finally {
            $zipFile->close();
        }

        if (count($files) === 0) {
            throw new InvalidZipException(new ValidationError(ErrorCodeList::ZIP_SIN_CPE));
        }

        return $files;
    }

    /**
     * @param array<string, string> $files nombre de cada archivo => contenido
     */
    public function compress(array $files): string
    {
        $zipFile = new ZipFile();
        foreach ($files as $name => $content) {
            $zipFile->addFromString($name, $content, ZipCompressionMethod::DEFLATED);
        }
        $zip = $zipFile->outputAsString();
        $zipFile->close();

        return $zip;
    }
}
