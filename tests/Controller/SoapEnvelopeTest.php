<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Model\ErrorCodeList;
use App\Services\Zip\XmlZipFly;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Lectura y escritura del sobre SOAP 1.1 (sin ext-soap).
 */
class SoapEnvelopeTest extends WebTestCase
{
    use BillServiceControllerTrait;

    public function testMalformedXmlReturnsClientFault(): void
    {
        $client = static::createClient();

        // \SoapServer respondía "Bad Request" y terminaba el proceso con exit().
        $xpath = $this->postSoap($client, 'esto no es xml');

        $this->assertSame(500, $client->getResponse()->getStatusCode());
        $this->assertSame('text/xml; charset=utf-8', $client->getResponse()->headers->get('Content-Type'));
        $this->assertSame('SOAP-ENV:Client', $xpath->evaluate('string(/s:Envelope/s:Body/s:Fault/faultcode)'));
    }

    public function testDoctypeIsRejected(): void
    {
        $client = static::createClient();

        $xml = '<?xml version="1.0"?><!DOCTYPE x [<!ENTITY e SYSTEM "file:///etc/passwd">]>'
            .$this->buildEnvelope('getStatus', ['ticket' => '&e;']);
        $xpath = $this->postSoap($client, $xml);

        $this->assertSame('SOAP-ENV:Client', $xpath->evaluate('string(//s:Fault/faultcode)'));
        $this->assertStringNotContainsString('root:', (string) $client->getResponse()->getContent());
    }

    public function testSoap12IsVersionMismatch(): void
    {
        $client = static::createClient();

        $xml = str_replace(
            'http://schemas.xmlsoap.org/soap/envelope/',
            'http://www.w3.org/2003/05/soap-envelope',
            $this->buildEnvelope('getStatus', ['ticket' => '1']),
        );
        $xpath = $this->postSoap($client, $xml);

        $this->assertSame('SOAP-ENV:VersionMismatch', $xpath->evaluate('string(//s:Fault/faultcode)'));
    }

    public function testUnknownOperation(): void
    {
        $client = static::createClient();

        $xpath = $this->postSoap($client, $this->buildEnvelope('deleteBill', []));

        $this->assertSame('SOAP-ENV:Client', $xpath->evaluate('string(//s:Fault/faultcode)'));
        $this->assertStringContainsString('deleteBill', $xpath->evaluate('string(//s:Fault/faultstring)'));
    }

    public function testMissingSecurityHeader(): void
    {
        $client = static::createClient();

        $xml = preg_replace('#<soapenv:Header>.*</soapenv:Header>#s', '', $this->buildEnvelope('getStatus', ['ticket' => '1']));
        $xpath = $this->postSoap($client, $xml);

        $this->assertSame(ErrorCodeList::INVALID_USER, $xpath->evaluate('string(//s:Fault/faultcode)'));
    }

    public function testAcceptsAnyNamespacePrefixes(): void
    {
        $client = static::createClient();

        $xml = <<<XML
<S:Envelope xmlns:S="http://schemas.xmlsoap.org/soap/envelope/">
  <S:Header>
    <o:Security xmlns:o="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
      <o:UsernameToken><o:Username>20123456789MODDATOS</o:Username><o:Password>moddatos</o:Password></o:UsernameToken>
    </o:Security>
  </S:Header>
  <S:Body><getStatus xmlns="http://service.sunat.gob.pe"><ticket xmlns="">999</ticket></getStatus></S:Body>
</S:Envelope>
XML;
        $xpath = $this->postSoap($client, $xml);

        // Autenticado: el error es de negocio (ticket inexistente), no de credenciales.
        $this->assertSame(ErrorCodeList::TICKET_NOTFOUND, $xpath->evaluate('string(//s:Fault/faultcode)'));
    }

    public function testFaultIncludesValidationDetail(): void
    {
        $client = static::createClient();

        $name = '20200464529-01-F001-2';
        $xml = '<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2"'
            .' xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">'
            .'<cbc:UBLVersionID>2.1</cbc:UBLVersionID><Foo/></Invoice>';
        $zip = base64_encode((new XmlZipFly())->compress($name.'.xml', $xml)->getContent());

        $xpath = $this->postSoap($client, $this->buildEnvelope('sendBill', [
            'fileName' => $name.'.zip',
            'contentFile' => $zip,
        ]));

        $this->assertSame(ErrorCodeList::XML_NO_PARSE, $xpath->evaluate('string(//s:Fault/faultcode)'));
        $this->assertNotSame('', $xpath->evaluate('string(//s:Fault/faultstring)'));
        $this->assertNotSame('', $xpath->evaluate('string(//s:Fault/detail/message)'));
    }

    public function testGetRequestWithoutWsdlIsClientFault(): void
    {
        $client = static::createClient();

        $client->request('GET', '/ol-ti-itcpe/billService');

        $this->assertSame(500, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('<faultcode>SOAP-ENV:Client</faultcode>', (string) $client->getResponse()->getContent());
    }
}
