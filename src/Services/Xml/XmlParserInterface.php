<?php

declare(strict_types=1);

namespace App\Services\Xml;

use App\Entity\MinimalDocInfo;
use DOMDocument;

interface XmlParserInterface
{
    public function parse(DOMDocument $document): MinimalDocInfo;
}