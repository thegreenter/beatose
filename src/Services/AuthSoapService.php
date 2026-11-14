<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\Bill\BillServiceInterface;
use App\Services\Util\Mapper;
use App\Model\{
    ErrorCodeList,
    GetStatusCdrRequest,
    GetStatusRequest,
    SendBillRequest,
    SendPackRequest,
    SendSummaryRequest,
    ValidationError,
};
use App\Services\Soap\ExceptionCreator;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

/**
 * Objeto que atiende las operaciones SOAP de una única petición.
 *
 * Guarda el resultado de la cabecera WS-Security, así que NO es compartido
 * (shared: false): cada petición SOAP debe usar una instancia nueva, creada
 * por {@see \App\Handler\SoapServerHandler}. Si se reutilizara la misma
 * instancia (p. ej. en el modo worker de FrankenPHP), una petición
 * autenticada dejaría autenticadas a las siguientes.
 */
#[Autoconfigure(shared: false)]
class AuthSoapService
{
    private bool $isAuthenticated = false;

    /**
     * AuthSoapService constructor.
     * @param BillServiceInterface $billService
     * @param Mapper $mapper
     * @param CredentialStore $credentialStore
     * @param ExceptionCreator $exceptionCreator
     */
    public function __construct(private BillServiceInterface $billService, private Mapper $mapper, private CredentialStore $credentialStore, private ExceptionCreator $exceptionCreator)
    {
    }

    /**
     * Validate security header (username, password).
     *
     * @param object $header
     */
    public function security(object $header): void
    {
        $credential = $this->credentialStore->get();
        $user = $header->UsernameToken ?? null;

        $this->isAuthenticated = is_object($user)
            && $this->equals($credential->getUser(), $user->Username ?? null)
            && $this->equals($credential->getPassword(), $user->Password ?? null);
    }

    public function sendBill(object $request): object
    {
        $this->ensureAuthenticated();
        $requestType = $this->mapper->map($request, SendBillRequest::class);

        return $this->billService->sendBill($requestType);
    }

    public function sendSummary(object $request): object
    {
        $this->ensureAuthenticated();
        $requestType = $this->mapper->map($request, SendSummaryRequest::class);

        return $this->billService->sendSummary($requestType);
    }

    public function getStatus(object $request): object
    {
        $this->ensureAuthenticated();
        $requestType = $this->mapper->map($request, GetStatusRequest::class);

        return $this->billService->getStatus($requestType);
    }

    public function sendPack(object $request): object
    {
        $this->ensureAuthenticated();
        $requestType = $this->mapper->map($request, SendPackRequest::class);

        return $this->billService->sendPack($requestType);
    }

    public function getStatusCdr(object $request): object
    {
        $this->ensureAuthenticated();
        $requestType = $this->mapper->map($request, GetStatusCdrRequest::class);

        return $this->billService->getStatusCdr($requestType);
    }

    private function ensureAuthenticated(): void
    {
        if (!$this->isAuthenticated) {
            throw $this->exceptionCreator->fromValidation(new ValidationError(ErrorCodeList::INVALID_USER));
        }
    }

    /**
     * Comparación en tiempo constante; un valor ausente nunca coincide.
     */
    private function equals(?string $expected, mixed $given): bool
    {
        return $expected !== null && is_string($given) && hash_equals($expected, $given);
    }
}
