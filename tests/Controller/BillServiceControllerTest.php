<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Model\ErrorCodeList;
use App\Services\Zip\XmlZipFly;
use DOMDocument;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BillServiceControllerTest extends WebTestCase
{
    use BillServiceControllerTrait;

    public function testGetWsdl()
    {
        $client = static::createClient();
        $client->catchExceptions(false);

        $client->request('GET', '/ol-ti-itcpe/billService?wsdl');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $doc = new DOMDocument();
        $doc->loadXML($client->getResponse()->getContent());

        $this->assertStringContainsStringIgnoringCase('wsdl', $doc->documentElement->nodeName);
    }

    public function testAuthNoValidSoap()
    {
        $soapContent = $this->buildSoapRequest([
            'user' => '2222',
            'pass' => '323',
            'filename' => '20123456789-01-F001-84.zip',
            'content' => 'xxxxxxxx'
        ]);

        $client = static::createClient();
        $client->catchExceptions(false);

        $client->request(
            'POST',
            '/ol-ti-itcpe/billService',
            [],
            [],
            ['CONTENT_TYPE' => 'text/xml;charset=UTF-8'],
            $soapContent,
        );

        $errorCode = ErrorCodeList::INVALID_USER;
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsStringIgnoringCase(
            "<faultcode>$errorCode</faultcode>",
            $client->getResponse()->getContent()
        );
    }

    public function testInvalidDocSendBillSoap()
    {
        $soapContent = $this->buildSoapRequest([
            'user' => '20123456789MODDATOS',
            'pass' => 'moddatos',
            'filename' => '20123456789-RC-F001-84.zip',
            'content' => 'xxxxxxxx'
        ]);

        $client = static::createClient();
        $client->catchExceptions(false);

        $client->request(
            'POST',
            '/ol-ti-itcpe/billService',
            [],
            [],
            ['CONTENT_TYPE' => 'text/xml;charset=UTF-8'],
            $soapContent,
        );

        $errorCode = ErrorCodeList::ZIP_INVALID_NAME;
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsStringIgnoringCase(
            "<faultcode>$errorCode</faultcode>",
            $client->getResponse()->getContent()
        );
    }

    public function testInvalidZipSendBillSoap()
    {
        $soapContent = $this->buildSoapRequest([
            'user' => '20123456789MODDATOS',
            'pass' => 'moddatos',
            'filename' => '20123456789-01-F001-1.zip',
            'content' => 'xxxxxxxx'
        ]);

        $client = static::createClient();
        $client->catchExceptions(false);

        $client->request(
            'POST',
            '/ol-ti-itcpe/billService',
            [],
            [],
            ['CONTENT_TYPE' => 'text/xml;charset=UTF-8'],
            $soapContent,
        );

        $errorCode = ErrorCodeList::ZIP_CORRUPTO;
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsStringIgnoringCase(
            "<faultcode>$errorCode</faultcode>",
            $client->getResponse()->getContent()
        );
    }

//    public function testSendBillSoap()
//    {
//        $filename = '20123456789-01-F001-1';
//        $xml = file_get_contents(__DIR__.'/../Resources/20123456789-01-F001-1.xml');
//
//        $zip = new XmlZipFly();
//        $xmlZip = $zip->compress($filename.'.xml', $xml)->getContent();
//
//        $soapContent = $this->buildSoapRequest([
//            'user' => '20123456789MODDATOS',
//            'pass' => 'moddatos',
//            'filename' => $filename.'.zip',
//            'content' => base64_encode($xmlZip),
//        ]);
//
//        $client = static::createClient();
//        $client->catchExceptions(false);
//        $client->request(
//            'POST',
//            '/ol-ti-itcpe/billService',
//            [],
//            [],
//            ['CONTENT_TYPE' => 'text/xml;charset=UTF-8'],
//            $soapContent,
//        );
//
//        $this->assertTrue(true);
//    }
}