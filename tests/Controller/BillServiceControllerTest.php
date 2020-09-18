<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use DOMDocument;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BillServiceControllerTest extends WebTestCase
{
    public function testGetWsdl()
    {
        $client = static::createClient();

        $client->request('GET', '/ol-ti-itcpe/billService?wsdl');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $doc = new DOMDocument();
        $doc->loadXML($client->getResponse()->getContent());

        $this->assertStringContainsStringIgnoringCase('wsdl', $doc->documentElement->nodeName);
    }
}