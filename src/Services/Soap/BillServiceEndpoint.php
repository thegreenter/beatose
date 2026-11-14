<?php

declare(strict_types=1);

namespace App\Services\Soap;

use App\Model\{
    ErrorCodeList,
    GetStatusCdrRequest,
    GetStatusRequest,
    SendBillRequest,
    SendPackRequest,
    SendSummaryRequest,
};
use App\Services\Bill\BillServiceInterface;
use App\Services\CredentialStore;

/**
 * Operaciones del billService: autentica (WS-Security UsernameToken), convierte
 * los parámetros del sobre en los DTO del servicio y su resultado en el payload
 * de la respuesta.
 *
 * No guarda estado entre peticiones: es seguro como servicio compartido en modo worker.
 */
class BillServiceEndpoint
{
    public function __construct(
        private BillServiceInterface $billService,
        private CredentialStore $credentialStore,
        private ExceptionCreator $exceptionCreator,
    ) {
    }

    /**
     * @return array<string, mixed> payload de {operation}Response
     *
     * @throws SoapFault
     */
    public function handle(SoapRequest $request): array
    {
        $call = match ($request->operation) {
            'sendBill' => $this->sendBill(...),
            'sendSummary' => $this->sendSummary(...),
            'sendPack' => $this->sendPack(...),
            'getStatus' => $this->getStatus(...),
            'getStatusCdr' => $this->getStatusCdr(...),
            default => throw new SoapFault(SoapFault::CLIENT, sprintf('Operación "%s" no existe', $request->operation)),
        };

        if (!$this->isAuthenticated($request)) {
            throw $this->exceptionCreator->fromCode(ErrorCodeList::INVALID_USER);
        }

        return $call($request);
    }

    /**
     * @return array<string, mixed>
     */
    private function sendBill(SoapRequest $request): array
    {
        $dto = new SendBillRequest();
        $dto->fileName = $request->get('fileName');
        $dto->contentFile = $this->decodeBase64($request->get('contentFile'));

        return ['applicationResponse' => $this->encodeBase64($this->billService->sendBill($dto)->applicationResponse)];
    }

    /**
     * @return array<string, mixed>
     */
    private function sendSummary(SoapRequest $request): array
    {
        $dto = new SendSummaryRequest();
        $dto->fileName = $request->get('fileName');
        $dto->contentFile = $this->decodeBase64($request->get('contentFile'));

        return ['ticket' => $this->billService->sendSummary($dto)->ticket];
    }

    /**
     * @return array<string, mixed>
     */
    private function sendPack(SoapRequest $request): array
    {
        $dto = new SendPackRequest();
        $dto->fileName = $request->get('fileName');
        $dto->contentFile = $this->decodeBase64($request->get('contentFile'));

        return ['ticket' => $this->billService->sendPack($dto)->ticket];
    }

    /**
     * @return array<string, mixed>
     */
    private function getStatus(SoapRequest $request): array
    {
        $dto = new GetStatusRequest();
        $dto->ticket = $request->get('ticket');
        $status = $this->billService->getStatus($dto)->status;

        // Orden de la secuencia statusResponse del XSD: content, statusCode.
        return ['status' => [
            'content' => $this->encodeBase64($status?->content),
            'statusCode' => $status?->statusCode,
        ]];
    }

    /**
     * @return array<string, mixed>
     */
    private function getStatusCdr(SoapRequest $request): array
    {
        $dto = new GetStatusCdrRequest();
        $dto->rucComprobante = $request->get('rucComprobante');
        $dto->tipoComprobante = $request->get('tipoComprobante');
        $dto->serieComprobante = $request->get('serieComprobante');
        $dto->numeroComprobante = filter_var($request->get('numeroComprobante'), FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        $status = $this->billService->getStatusCdr($dto)->statusCdr;

        // Orden de la secuencia statusCdrResponse del XSD: content, statusCode, statusMessage.
        return ['statusCdr' => [
            'content' => $this->encodeBase64($status?->content),
            'statusCode' => $status?->statusCode,
            'statusMessage' => $status?->statusMessage,
        ]];
    }

    private function isAuthenticated(SoapRequest $request): bool
    {
        $credential = $this->credentialStore->get();

        return $this->equals($credential->getUser(), $request->username)
            && $this->equals($credential->getPassword(), $request->password);
    }

    /**
     * Comparación en tiempo constante; un valor ausente nunca coincide.
     */
    private function equals(?string $expected, ?string $given): bool
    {
        return $expected !== null && $given !== null && hash_equals($expected, $given);
    }

    /**
     * xs:base64Binary: tolera saltos de línea y espacios, como ext-soap.
     */
    private function decodeBase64(?string $value): ?string
    {
        return $value === null ? null : base64_decode($value);
    }

    private function encodeBase64(?string $value): ?string
    {
        return $value === null ? null : base64_encode($value);
    }
}
