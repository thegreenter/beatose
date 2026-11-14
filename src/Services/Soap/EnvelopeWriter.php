<?php

declare(strict_types=1);

namespace App\Services\Soap;

use DOMDocument;
use DOMElement;

/**
 * Genera las respuestas SOAP 1.1 del billService (mismo formato que producía ext-soap).
 */
class EnvelopeWriter
{
    private const SERVICE_NS = 'http://service.sunat.gob.pe';

    /**
     * @param array<string, mixed> $payload hijos de {operation}Response; los null se omiten
     *                                      (minOccurs=0) y los arrays se anidan
     */
    public function response(string $operation, array $payload): string
    {
        [$doc, $body] = $this->envelope();
        $doc->documentElement->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:ns1', self::SERVICE_NS);

        $response = $doc->createElementNS(self::SERVICE_NS, 'ns1:'.$operation.'Response');
        $body->appendChild($response);
        // Los elementos locales del XSD del servicio no están calificados (sin namespace).
        $this->append($doc, $response, $payload);

        return $doc->saveXML();
    }

    public function fault(SoapFault $fault): string
    {
        [$doc, $body] = $this->envelope();

        $node = $doc->createElementNS(EnvelopeReader::SOAP11_NS, 'SOAP-ENV:Fault');
        $body->appendChild($node);
        $detail = $fault->getDetail() === null ? null : ['message' => $fault->getDetail()];
        $this->append($doc, $node, [
            'faultcode' => $fault->getFaultCode(),
            'faultstring' => $fault->getFaultString(),
            'detail' => $detail,
        ]);

        return $doc->saveXML();
    }

    /**
     * @return array{DOMDocument, DOMElement}
     */
    private function envelope(): array
    {
        $doc = new DOMDocument('1.0', 'UTF-8');
        $envelope = $doc->createElementNS(EnvelopeReader::SOAP11_NS, 'SOAP-ENV:Envelope');
        $doc->appendChild($envelope);
        $body = $doc->createElementNS(EnvelopeReader::SOAP11_NS, 'SOAP-ENV:Body');
        $envelope->appendChild($body);

        return [$doc, $body];
    }

    /**
     * @param array<string, mixed> $values
     */
    private function append(DOMDocument $doc, DOMElement $parent, array $values): void
    {
        foreach ($values as $name => $value) {
            if ($value === null) {
                continue;
            }

            $element = $doc->createElement($name);
            $parent->appendChild($element);

            if (is_array($value)) {
                $this->append($doc, $element, $value);
            } else {
                $element->appendChild($doc->createTextNode((string) $value));
            }
        }
    }
}
