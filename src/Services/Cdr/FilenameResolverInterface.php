<?php

declare(strict_types=1);

namespace App\Services\Cdr;

use DOMDocument;

interface FilenameResolverInterface
{
    /**
     * Get Filename from Dom XML.
     *
     * @param DOMDocument $document
     *
     * @return string|null
     */
    public function getFilename(DOMDocument $document): ?string;
}