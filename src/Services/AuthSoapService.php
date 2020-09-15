<?php

declare(strict_types=1);

namespace App\Services;

use App\Model\GetStatusCdrRequest;
use App\Model\GetStatusRequest;
use App\Model\SendBillRequest;
use App\Model\SendPackRequest;
use App\Model\SendSummaryRequest;
use SoapFault;

class AuthSoapService
{
    /**
     * @var BillServiceInterface
     */
    private $billService;

    /**
     * @var Mapper
     */
    private $mapper;

    /**
     * @var CredentialStore
     */
    private $credentialStore;

    /**
     * @var bool
     */
    private $isAuthenticated = false;

    /**
     * SoapService constructor
     *
     * @param BillServiceInterface $billService
     * @param Mapper $mapper
     * @param CredentialStore $credentialStore
     */
    public function __construct(BillServiceInterface $billService, Mapper $mapper, CredentialStore $credentialStore)
    {
        $this->billService = $billService;
        $this->mapper = $mapper;
        $this->credentialStore = $credentialStore;
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
            throw new SoapFault("0102", "Usuario o contraseña incorrectos");
        }
    }
}