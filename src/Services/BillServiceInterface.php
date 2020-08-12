<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\GetStatusResponse;
use App\Entity\SendBillResponse;
use App\Entity\SendPackResponse;
use App\Entity\SendSummaryResponse;

interface BillServiceInterface
{
    /**
     * Validate security header (username, password).
     *
     * @param object $header
     */
    public function Security(object $header): void;

    public function sendBill(object $request): SendBillResponse;

    public function sendSummary(object $request): SendSummaryResponse;

    public function getStatus(object $request): GetStatusResponse;

    public function sendPack(object $request): SendPackResponse;
}