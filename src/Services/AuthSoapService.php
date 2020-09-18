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

class AuthSoapService
{
    private BillServiceInterface $billService;

    private Mapper $mapper;

    private CredentialStore $credentialStore;

    private ExceptionCreator $exceptionCretor;

    private bool $isAuthenticated = false;

    /**
     * AuthSoapService constructor.
     * @param BillServiceInterface $billService
     * @param Mapper $mapper
     * @param CredentialStore $credentialStore
     * @param ExceptionCreator $exceptionCretor
     */
    public function __construct(BillServiceInterface $billService, Mapper $mapper, CredentialStore $credentialStore, ExceptionCreator $exceptionCretor)
    {
        $this->billService = $billService;
        $this->mapper = $mapper;
        $this->credentialStore = $credentialStore;
        $this->exceptionCretor = $exceptionCretor;
    }

    /**
     * Validate security header (username, password).
     *
     * @param object $header
     */
    public function security(object $header): void
    {
        $credential = $this->credentialStore->get();

        $user = $header->UsernameToken;

        if ($user->Username === $credential->getUser() &&
            $user->Password === $credential->getPassword()) {
            $this->isAuthenticated = true;
        }
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

    private function ensureAuthenticated()
    {
        if (!$this->isAuthenticated) {
            throw $this->exceptionCretor->fromValidation(new ValidationError(ErrorCodeList::INVALID_USER));
        }
    }
}