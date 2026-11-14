<?php

declare(strict_types=1);

namespace App\Handler;

use App\Services\Soap\BillServiceEndpoint;
use App\Services\Soap\EnvelopeReader;
use App\Services\Soap\EnvelopeWriter;
use App\Services\Soap\SoapFault;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Servidor SOAP 1.1 del billService, sin ext-soap.
 *
 * El WSDL es fijo, así que el sobre se lee y se escribe directamente con DOM.
 * A diferencia de \SoapServer, nunca llama a exit() ni escribe en la salida
 * estándar (seguro en el modo worker de FrankenPHP), devuelve HTTP 500 en
 * los faults (SOAP 1.1 §6.2, como SUNAT) y controla exactamente qué se
 * responde ante sobres inválidos o errores inesperados.
 */
class SoapServerHandler implements RequestHandlerInterface
{
    public function __construct(
        private EnvelopeReader $reader,
        private EnvelopeWriter $writer,
        private BillServiceEndpoint $endpoint,
        private LoggerInterface $logger,
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $soapRequest = $this->reader->read($request->getContent());
            $payload = $this->endpoint->handle($soapRequest);

            return $this->xml($this->writer->response($soapRequest->operation, $payload), Response::HTTP_OK);
        } catch (SoapFault $fault) {
            return $this->xml($this->writer->fault($fault), Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (Throwable $e) {
            $this->logger->error('Error inesperado en billService.', ['exception' => $e]);
            $fault = new SoapFault(SoapFault::SERVER, 'Error interno del servidor');

            return $this->xml($this->writer->fault($fault), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function xml(string $content, int $status): Response
    {
        return new Response($content, $status, ['Content-Type' => 'text/xml; charset=utf-8']);
    }
}
