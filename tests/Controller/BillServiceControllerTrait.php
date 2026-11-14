<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Services\Zip\XmlZipFly;
use DOMDocument;
use DOMXPath;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

trait BillServiceControllerTrait
{
    private function buildSoapRequest(array $parameters)
    {
        $template = file_get_contents(__DIR__.'/../Resources/send_bill_template.xml');
        $search = array_keys($parameters);
        $search = array_map(fn ($key) => "%$key%", $search);

        return str_replace($search, array_values($parameters), $template);
    }

    /**
     * Sobre SOAP 1.1 con WS-Security para cualquier operación del billService.
     *
     * @param array<string, string> $params
     */
    private function buildEnvelope(string $operation, array $params, string $user = '20123456789MODDATOS', string $pass = 'moddatos'): string
    {
        $body = '';
        foreach ($params as $name => $value) {
            $body .= sprintf('<%1$s>%2$s</%1$s>', $name, htmlspecialchars($value, ENT_XML1));
        }

        return <<<XML
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://service.sunat.gob.pe" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
  <soapenv:Header>
    <wsse:Security>
      <wsse:UsernameToken>
        <wsse:Username>$user</wsse:Username>
        <wsse:Password>$pass</wsse:Password>
      </wsse:UsernameToken>
    </wsse:Security>
  </soapenv:Header>
  <soapenv:Body>
    <ser:$operation>$body</ser:$operation>
  </soapenv:Body>
</soapenv:Envelope>
XML;
    }

    /** ZIP en base64 con el XML de tests/Resources/{$name}.xml */
    private function zipResource(string $name): string
    {
        $xml = file_get_contents(__DIR__.'/../Resources/'.$name.'.xml');

        return base64_encode((new XmlZipFly())->compress($name.'.xml', $xml)->getContent());
    }

    private function postSoap(KernelBrowser $client, string $envelope): DOMXPath
    {
        $client->request(
            'POST',
            '/ol-ti-itcpe/billService',
            [],
            [],
            ['CONTENT_TYPE' => 'text/xml;charset=UTF-8'],
            $envelope,
        );

        $doc = new DOMDocument();
        $this->assertTrue($doc->loadXML((string) $client->getResponse()->getContent()), 'La respuesta no es XML');
        $xpath = new DOMXPath($doc);
        $xpath->registerNamespace('s', 'http://schemas.xmlsoap.org/soap/envelope/');
        $xpath->registerNamespace('ns1', 'http://service.sunat.gob.pe');

        return $xpath;
    }

    /** Descomprime el ZIP del CDR y devuelve su ResponseCode. */
    private function cdrResponseCode(string $zip): string
    {
        $tmp = tempnam(sys_get_temp_dir(), 'cdr');
        file_put_contents($tmp, $zip);
        $archive = new \ZipArchive();
        $this->assertTrue($archive->open($tmp) === true, 'El CDR no es un ZIP válido');
        $xml = $archive->getFromIndex(0);
        $archive->close();
        unlink($tmp);

        return $this->responseCodeFromXml((string) $xml);
    }

    /** ResponseCode de un CDR en XML. */
    private function responseCodeFromXml(string $xml): string
    {
        $doc = new DOMDocument();
        $doc->loadXML($xml);
        $xpath = new DOMXPath($doc);
        $xpath->registerNamespace('cbc', 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2');

        return $xpath->evaluate('string(//cbc:ResponseCode)');
    }
}
