<?php

declare(strict_types=1);

namespace App\Services\Xml;

use DOMDocument;

interface XslDocResolverInterface
{
    public function fromDoc(DOMDocument $document): ?DOMDocument;
}
