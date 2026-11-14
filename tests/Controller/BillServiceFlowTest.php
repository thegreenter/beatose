<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Flujos completos con comprobantes firmados (tests/Resources).
 */
class BillServiceFlowTest extends WebTestCase
{
    use BillServiceControllerTrait;

    private const INVOICE = '20200464529-01-F001-697';
    private const SUMMARY = '20000000001-RC-20180126-1';

    public function testSendBillReturnsAcceptedCdr(): void
    {
        $client = static::createClient();

        $xpath = $this->postSoap($client, $this->buildEnvelope('sendBill', [
            'fileName' => self::INVOICE.'.zip',
            'contentFile' => $this->zipResource(self::INVOICE),
        ]));

        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $cdr = base64_decode($xpath->evaluate('string(//ns1:sendBillResponse/applicationResponse)'), true);
        $this->assertNotFalse($cdr);
        $this->assertSame('0', $this->cdrResponseCode($cdr));

        // El CDR queda disponible en getStatusCdr
        $xpath = $this->postSoap($client, $this->buildEnvelope('getStatusCdr', [
            'rucComprobante' => '20200464529',
            'tipoComprobante' => '01',
            'serieComprobante' => 'F001',
            'numeroComprobante' => '697',
        ]));
        $this->assertSame('0', $xpath->evaluate('string(//ns1:getStatusCdrResponse/statusCdr/statusCode)'));
        $content = base64_decode($xpath->evaluate('string(//statusCdr/content)'), true);
        $this->assertSame('0', $this->cdrResponseCode($content));
    }

    public function testSendSummaryAndGetStatus(): void
    {
        $client = static::createClient();

        $xpath = $this->postSoap($client, $this->buildEnvelope('sendSummary', [
            'fileName' => self::SUMMARY.'.zip',
            'contentFile' => $this->zipResource(self::SUMMARY),
        ]));
        $ticket = $xpath->evaluate('string(//ns1:sendSummaryResponse/ticket)');
        $this->assertMatchesRegularExpression('/^\d+$/', $ticket);

        $xpath = $this->postSoap($client, $this->buildEnvelope('getStatus', ['ticket' => $ticket]));
        $this->assertSame('0', $xpath->evaluate('string(//ns1:getStatusResponse/status/statusCode)'));
        $content = base64_decode($xpath->evaluate('string(//status/content)'), true);
        $this->assertSame('0', $this->cdrResponseCode($content));
    }

    public function testGetStatusUnknownTicket(): void
    {
        $client = static::createClient();

        $xpath = $this->postSoap($client, $this->buildEnvelope('getStatus', ['ticket' => '999']));
        $this->assertSame('0127', $xpath->evaluate('string(//s:Fault/faultcode)'));
    }
}
