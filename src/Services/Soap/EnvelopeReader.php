<?php

declare(strict_types=1);

namespace App\Services\Soap;

use DOMDocument;
use DOMElement;
use DOMXPath;

/**
 * Lee un sobre SOAP 1.1 (operación, UsernameToken y parámetros) con DOM.
 */
class EnvelopeReader
{
    public const SOAP11_NS = 'http://schemas.xmlsoap.org/soap/envelope/';
    private const SOAP12_NS = 'http://www.w3.org/2003/05/soap-envelope';

    /**
     * @throws SoapFault si el sobre no es válido
     */
    public function read(string $xml): SoapRequest
    {
        if (trim($xml) === '') {
            throw new SoapFault(SoapFault::CLIENT, 'Bad Request: cuerpo vacío');
        }

        // SOAP no admite DTD; rechazarla antes de parsear evita XXE y expansión de entidades.
        if (stripos($xml, '<!DOCTYPE') !== false) {
            throw new SoapFault(SoapFault::CLIENT, 'Bad Request: DTD no permitida');
        }

        $doc = new DOMDocument();
        $previous = libxml_use_internal_errors(true);
        // PARSEHUGE: comprobantes extensos llegan como nodos base64 de varios MB.
        $loaded = $doc->loadXML($xml, LIBXML_NONET | LIBXML_PARSEHUGE);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $envelope = $doc->documentElement;
        if (!$loaded || $envelope === null || $envelope->localName !== 'Envelope') {
            throw new SoapFault(SoapFault::CLIENT, 'Bad Request: el contenido no es un sobre SOAP');
        }

        if ($envelope->namespaceURI === self::SOAP12_NS) {
            throw new SoapFault(SoapFault::VERSION_MISMATCH, 'Solo se admite SOAP 1.1');
        }
        if ($envelope->namespaceURI !== self::SOAP11_NS) {
            throw new SoapFault(SoapFault::VERSION_MISMATCH, 'Namespace de Envelope inválido');
        }

        $xpath = new DOMXPath($doc);
        $xpath->registerNamespace('env', self::SOAP11_NS);

        $operation = $xpath->query('/env:Envelope/env:Body/*[1]')->item(0);
        if (!$operation instanceof DOMElement) {
            throw new SoapFault(SoapFault::CLIENT, 'Bad Request: Body sin operación');
        }

        // WS-Security: se busca por nombre local para aceptar cualquier prefijo o versión del namespace.
        $token = '/env:Envelope/env:Header/*[local-name()="Security"]/*[local-name()="UsernameToken"]';

        return new SoapRequest(
            $operation->localName,
            $this->text($xpath, $token.'/*[local-name()="Username"]'),
            $this->text($xpath, $token.'/*[local-name()="Password"]'),
            $this->parameters($operation),
        );
    }

    private function text(DOMXPath $xpath, string $query): ?string
    {
        $node = $xpath->query($query)->item(0);

        return $node?->textContent;
    }

    /**
     * @return array<string, string>
     */
    private function parameters(DOMElement $operation): array
    {
        $parameters = [];
        foreach ($operation->childNodes as $child) {
            if ($child instanceof DOMElement) {
                $parameters[$child->localName] = $child->textContent;
            }
        }

        return $parameters;
    }
}
