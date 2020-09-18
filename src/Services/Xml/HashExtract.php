<?php

declare(strict_types=1);

namespace App\Services\Xml;

use DOMDocument;
use DOMXPath;

class HashExtract
{
    private const DIGEST_QUERY = 'ext:UBLExtensions/ext:UBLExtension/ext:ExtensionContent/ds:Signature/ds:SignedInfo/ds:Reference/ds:DigestValue';

    public function fromXml(?string $xml): ?string
    {
        $doc = new DOMDocument();
        $doc->loadXML($xml);

        return $this->fromDoc($doc);
    }

    public function fromDoc(DOMDocument $document): ?string
    {
        $xpath = new DOMXPath($document);
        $node = $xpath->query(self::DIGEST_QUERY, $document->documentElement);
        if ($node === false || $node->count() === 0) {
            return null;
        }

        return $node->item(0)->nodeValue;
    }
}