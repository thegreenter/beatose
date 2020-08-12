<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\GetStatusResponse;
use App\Entity\SendBillResponse;
use App\Entity\SendPackResponse;
use App\Entity\SendSummaryResponse;
use SoapFault;

class AuthenticationFilter implements BillServiceInterface
{
    /**
     * @var BillServiceInterface
     */
    private $service;

    /**
     * @var CredentialStore
     */
    private $credentialStore;

    /**
     * @var bool
     */
    private $isAuthenticated = false;

    /**
     * AuthenticationFilter constructor.
     * @param BillServiceInterface $service
     * @param CredentialStore $credentialStore
     */
    public function __construct(BillServiceInterface $service, CredentialStore $credentialStore)
    {
        $this->service = $service;
        $this->credentialStore = $credentialStore;
    }

    /**
     * Validate security header (username, password).
     *
     * @param object $header
     */
    public function Security(object $header): void
    {
        $credential = $this->credentialStore->get();

        $user = $header->UsernameToken;

        if ($user->Username === $credential->getUser() &&
            $user->Password === $credential->getPassword()) {
            $this->isAuthenticated = true;
        }
    }

    public function sendBill(object $request): SendBillResponse
    {
        $this->ensureAuthenticated();

        return $this->service->sendBill($request);
    }

    public function sendSummary(object $request): SendSummaryResponse
    {
        $this->ensureAuthenticated();

        return $this->service->sendSummary($request);
    }

    public function getStatus(object $request): GetStatusResponse
    {
        $this->ensureAuthenticated();

        return $this->service->getStatus($request);
    }

    public function sendPack(object $request): SendPackResponse
    {
        $this->ensureAuthenticated();

        return $this->service->sendPack($request);
    }

    private function ensureAuthenticated()
    {
        if (!$this->isAuthenticated) {
            throw new SoapFault("0102", "Usuario o contraseña incorrectos");
        }
    }
}