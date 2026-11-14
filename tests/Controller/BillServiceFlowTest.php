<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Model\ErrorCodeList;
use App\Services\Zip\XmlPackZip;
use App\Services\Zip\XmlZipFly;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\ReceivedStamp;
use Symfony\Component\Messenger\Transport\InMemory\InMemoryTransport;

/**
 * Flujos completos con comprobantes firmados (tests/Resources).
 */
class BillServiceFlowTest extends WebTestCase
{
    use BillServiceControllerTrait;

    private const INVOICE = '20200464529-01-F001-697';
    private const SUMMARY = '20000000001-RC-20180126-1';
    private const PACK = '20200464529-LT-20261114-1';

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

    public function testResendBillUpdatesExistingRecord(): void
    {
        $client = static::createClient();
        $envelope = $this->buildEnvelope('sendBill', [
            'fileName' => self::INVOICE.'.zip',
            'contentFile' => $this->zipResource(self::INVOICE),
        ]);

        foreach ([1, 2] as $attempt) {
            $xpath = $this->postSoap($client, $envelope);
            $this->assertSame(200, $client->getResponse()->getStatusCode(), "Envío $attempt");
            $this->assertNotSame('', $xpath->evaluate('string(//ns1:sendBillResponse/applicationResponse)'));
        }

        $connection = static::getContainer()->get('doctrine.dbal.default_connection');
        $this->assertSame(1, (int) $connection->fetchOne(
            'SELECT COUNT(*) FROM cpe_document WHERE name = ?',
            ['20200464529-01-F001-697'],
        ));
    }

    public function testSendSummaryIsProcessedAsynchronously(): void
    {
        $client = static::createClient();
        $client->disableReboot();

        $ticket = $this->sendSummary($client, self::SUMMARY, $this->zipResource(self::SUMMARY));
        $pending = $this->takeQueuedMessages();
        $this->assertCount(1, $pending);

        // Mientras no se procese la cola: "en proceso" y sin CDR.
        [$code, $content] = $this->getStatus($client, $ticket);
        $this->assertSame('98', $code);
        $this->assertSame('', $content);

        $this->handleMessages($pending);

        [$code, $content] = $this->getStatus($client, $ticket);
        $this->assertSame('0', $code);
        $this->assertSame('0', $this->cdrResponseCode(base64_decode($content, true)));
    }

    public function testRejectedSummaryReturnsStatus99WithCdr(): void
    {
        $client = static::createClient();
        $client->disableReboot();

        // XML alterado después de firmarlo: la firma ya no es válida (2334, rechazo).
        $xml = str_replace(
            '<cbc:ReferenceDate>2018-01-26</cbc:ReferenceDate>',
            '<cbc:ReferenceDate>2018-01-25</cbc:ReferenceDate>',
            (string) file_get_contents(__DIR__.'/../Resources/'.self::SUMMARY.'.xml'),
        );
        $zip = base64_encode((new XmlZipFly())->compress(self::SUMMARY.'.xml', $xml)->getContent());

        $ticket = $this->sendSummary($client, self::SUMMARY, $zip);
        $this->handleMessages($this->takeQueuedMessages());

        [$code, $content] = $this->getStatus($client, $ticket);
        $this->assertSame('99', $code);
        $this->assertSame(ErrorCodeList::XML_ALTERADO, $this->cdrResponseCode(base64_decode($content, true)));
    }

    public function testInvalidSummaryReturnsExceptionCode(): void
    {
        $client = static::createClient();
        $client->disableReboot();

        $name = '20000000001-RC-20180126-2';
        $zip = base64_encode((new XmlZipFly())->compress($name.'.xml', '<a/>')->getContent());

        $ticket = $this->sendSummary($client, $name, $zip);
        $this->handleMessages($this->takeQueuedMessages());

        [$code, $content] = $this->getStatus($client, $ticket);
        $this->assertSame(ErrorCodeList::XML_NO_SCHEMA_FILE, $code);
        $this->assertSame('', $content);
    }

    public function testSendPackIsProcessedAsynchronously(): void
    {
        $client = static::createClient();
        $client->disableReboot();

        // Segundo comprobante: copia de la factura con otro número; la firma ya no es válida (2334, rechazo).
        $invoice = (string) file_get_contents(__DIR__.'/../Resources/'.self::INVOICE.'.xml');
        $rejected = str_replace('<cbc:ID>F001-697</cbc:ID>', '<cbc:ID>F001-698</cbc:ID>', $invoice);

        $ticket = $this->sendPack($client, self::PACK, [
            self::INVOICE.'.xml' => $invoice,
            '20200464529-01-F001-698.xml' => $rejected,
        ]);
        $pending = $this->takeQueuedMessages();
        $this->assertCount(1, $pending);

        [$code, $content] = $this->getStatus($client, $ticket);
        $this->assertSame('98', $code);
        $this->assertSame('', $content);

        $this->handleMessages($pending);

        // Procesado con un rechazo: 99 y un CDR por comprobante.
        [$code, $content] = $this->getStatus($client, $ticket);
        $this->assertSame('99', $code);
        $cdrs = (new XmlPackZip())->decompress(base64_decode($content, true));
        $this->assertSame(['R-'.self::INVOICE.'.xml', 'R-20200464529-01-F001-698.xml'], array_keys($cdrs));
        $this->assertSame('0', $this->responseCodeFromXml($cdrs['R-'.self::INVOICE.'.xml']));
        $this->assertSame(ErrorCodeList::XML_ALTERADO, $this->responseCodeFromXml($cdrs['R-20200464529-01-F001-698.xml']));

        // Cada comprobante del lote tiene su CDR en getStatusCdr.
        $xpath = $this->postSoap($client, $this->buildEnvelope('getStatusCdr', [
            'rucComprobante' => '20200464529',
            'tipoComprobante' => '01',
            'serieComprobante' => 'F001',
            'numeroComprobante' => '698',
        ]));
        $content = base64_decode($xpath->evaluate('string(//statusCdr/content)'), true);
        $this->assertSame(ErrorCodeList::XML_ALTERADO, $this->cdrResponseCode($content));
    }

    public function testAcceptedPackReturnsStatus0(): void
    {
        $client = static::createClient();
        $client->disableReboot();

        $ticket = $this->sendPack($client, '20200464529-LT-20261114-2', [
            self::INVOICE.'.xml' => (string) file_get_contents(__DIR__.'/../Resources/'.self::INVOICE.'.xml'),
        ]);
        $this->handleMessages($this->takeQueuedMessages());

        [$code, $content] = $this->getStatus($client, $ticket);
        $this->assertSame('0', $code);
        $this->assertSame('0', $this->cdrResponseCode(base64_decode($content, true)));
    }

    public function testPackWithExceptionIsRejectedWithoutCdr(): void
    {
        $client = static::createClient();
        $client->disableReboot();

        $ticket = $this->sendPack($client, '20200464529-LT-20261114-3', [
            self::INVOICE.'.xml' => (string) file_get_contents(__DIR__.'/../Resources/'.self::INVOICE.'.xml'),
            '20200464529-03-B001-1.xml' => '<a/>',
        ]);
        $this->handleMessages($this->takeQueuedMessages());

        [$code, $content] = $this->getStatus($client, $ticket);
        $this->assertSame(ErrorCodeList::XML_NO_SCHEMA_FILE, $code);
        $this->assertSame('', $content);
    }

    /**
     * @return iterable<string, array{string, array<string, string>, string}>
     */
    public static function invalidPacks(): iterable
    {
        $xml = ['20200464529-01-F001-1.xml' => '<a/>'];

        yield 'resumen por sendPack' => ['20200464529-RC-20261114-1', $xml, ErrorCodeList::ZIP_IS_SUMMARY];
        yield 'comprobante por sendPack' => ['20200464529-01-F001-1', $xml, ErrorCodeList::ZIP_INVALID_NAME];
        yield 'ZIP vacío' => [self::PACK, [], ErrorCodeList::ZIP_EMPTY];
        yield 'nombre de XML incorrecto' => [self::PACK, ['factura.xml' => '<a/>'], ErrorCodeList::XML_INVALID_NAME];
        yield 'XML de otro RUC' => [self::PACK, ['20000000001-01-F001-1.xml' => '<a/>'], ErrorCodeList::XML_INVALID_NAME];
        yield 'tipo no permitido en lote' => [self::PACK, ['20200464529-RC-20261114-1.xml' => '<a/>'], ErrorCodeList::XML_INVALID_NAME];
        yield 'XML vacío' => [self::PACK, ['20200464529-03-B001-1.xml' => ''], ErrorCodeList::XML_EMPTY];
    }

    /**
     * @param array<string, string> $files
     */
    #[DataProvider('invalidPacks')]
    public function testInvalidPackIsRejectedOnReceipt(string $name, array $files, string $errorCode): void
    {
        $client = static::createClient();

        $xpath = $this->postSoap($client, $this->buildEnvelope('sendPack', [
            'fileName' => $name.'.zip',
            'contentFile' => base64_encode((new XmlPackZip())->compress($files)),
        ]));

        $this->assertSame(500, $client->getResponse()->getStatusCode());
        $this->assertSame($errorCode, $xpath->evaluate('string(//s:Fault/faultcode)'));
    }

    public function testCorruptPackZip(): void
    {
        $client = static::createClient();

        $xpath = $this->postSoap($client, $this->buildEnvelope('sendPack', [
            'fileName' => self::PACK.'.zip',
            'contentFile' => base64_encode('no es un zip'),
        ]));

        $this->assertSame(ErrorCodeList::ZIP_CORRUPTO, $xpath->evaluate('string(//s:Fault/faultcode)'));
    }

    public function testPackCannotBeSentBySendBill(): void
    {
        $client = static::createClient();

        $xpath = $this->postSoap($client, $this->buildEnvelope('sendBill', [
            'fileName' => self::PACK.'.zip',
            'contentFile' => base64_encode((new XmlPackZip())->compress(['20200464529-01-F001-1.xml' => '<a/>'])),
        ]));

        $this->assertSame(ErrorCodeList::ZIP_IS_PACK, $xpath->evaluate('string(//s:Fault/faultcode)'));
    }

    /**
     * @param array<string, string> $files nombre de cada XML => contenido
     */
    private function sendPack(KernelBrowser $client, string $name, array $files): string
    {
        $xpath = $this->postSoap($client, $this->buildEnvelope('sendPack', [
            'fileName' => $name.'.zip',
            'contentFile' => base64_encode((new XmlPackZip())->compress($files)),
        ]));
        $this->assertSame(200, $client->getResponse()->getStatusCode(), (string) $client->getResponse()->getContent());
        $ticket = $xpath->evaluate('string(//ns1:sendPackResponse/ticket)');
        $this->assertMatchesRegularExpression('/^\d{15}$/', $ticket);

        return $ticket;
    }

    private function sendSummary(KernelBrowser $client, string $name, string $zipBase64): string
    {
        $xpath = $this->postSoap($client, $this->buildEnvelope('sendSummary', [
            'fileName' => $name.'.zip',
            'contentFile' => $zipBase64,
        ]));
        $ticket = $xpath->evaluate('string(//ns1:sendSummaryResponse/ticket)');
        $this->assertMatchesRegularExpression('/^\d{15}$/', $ticket);

        return $ticket;
    }

    /**
     * @return array{string, string} statusCode y content (base64)
     */
    private function getStatus(KernelBrowser $client, string $ticket): array
    {
        $xpath = $this->postSoap($client, $this->buildEnvelope('getStatus', ['ticket' => $ticket]));

        return [
            $xpath->evaluate('string(//ns1:getStatusResponse/status/statusCode)'),
            $xpath->evaluate('string(//ns1:getStatusResponse/status/content)'),
        ];
    }

    /**
     * Retira los mensajes de la cola en memoria (se vacía al reiniciar los servicios en cada petición).
     *
     * @return Envelope[]
     */
    private function takeQueuedMessages(): array
    {
        /** @var InMemoryTransport $transport */
        $transport = static::getContainer()->get('messenger.transport.async');
        $envelopes = [...$transport->get()];
        foreach ($envelopes as $envelope) {
            $transport->ack($envelope);
        }

        return $envelopes;
    }

    /**
     * Ejecuta los handlers como lo haría "messenger:consume async".
     *
     * @param Envelope[] $envelopes
     */
    private function handleMessages(array $envelopes): void
    {
        /** @var MessageBusInterface $bus */
        $bus = static::getContainer()->get('messenger.default_bus');
        foreach ($envelopes as $envelope) {
            $bus->dispatch($envelope->with(new ReceivedStamp('async')));
        }
    }

    public function testGetStatusUnknownTicket(): void
    {
        $client = static::createClient();

        $xpath = $this->postSoap($client, $this->buildEnvelope('getStatus', ['ticket' => '999']));
        $this->assertSame('0127', $xpath->evaluate('string(//s:Fault/faultcode)'));
    }
}
