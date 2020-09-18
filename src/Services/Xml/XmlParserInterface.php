<?php

declare(strict_types=1);

namespace App\Services\Xml;

use App\Model\MinimalDocInfo;
use DOMDocument;

interface XmlParserInterface
{
    public function parse(DOMDocument $document): MinimalDocInfo;
}