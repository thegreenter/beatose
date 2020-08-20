<?php

declare(strict_types=1);

namespace App\Services\Zip;

use App\Entity\ValueResult;

interface XmlZipInterface
{
    /**
     * @param string $filename xml filename
     * @param string|null $content xml content to compress
     * @return ValueResult
     */
    public function compress(string $filename, ?string $content): ValueResult;

    /**
     * @param string|null $zip zip content
     * @param string $filename xml filename to decompress
     * @return ValueResult
     */
    public function decompress(?string $zip, string $filename): ValueResult;
}
