<?php

declare(strict_types=1);

namespace App\Services\Xml;

use App\Model\MinimalDocInfo;
use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;

class DomXmlParser implements XmlParserInterface
{
    public function parse(DOMDocument $document): MinimalDocInfo
    {
        $root = $document->documentElement;
        $xpath = $this->getXpath($document);

        $recipientNode = $this->getNode(
                        $xpath,
                        'cac:AccountingCustomerParty/cac:Party/cac:PartyIdentification/cbc:ID',
                        $root);

        $info =  new MinimalDocInfo();
        if (($idNode = $this->getNode($xpath, 'cbc:ID')) !== null) {
            $info->setId($idNode->nodeValue);
        }

        if ($recipientNode !== null) {
            $info
                ->setRecipientTypeDoc($recipientNode->getAttribute('schemeID'))
                ->setRecipient($recipientNode->nodeValue)
            ;
        }

        return $info;
    }

    private function getXpath(DOMDocument $doc): DOMXPath
    {
        return new DOMXPath($doc);
    }

    private function getNode(DOMXPath $xpath, string $query, DOMNode $context = null): ?DOMElement
    {
        $nodes = $xpath->query($query, $context);
        if ($nodes->length == 0) {
            return null;
        }

        $node = $nodes->item(0);

        return $node instanceof DOMElement ? $node : null;
    }
}