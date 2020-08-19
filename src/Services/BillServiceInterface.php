<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\GetStatusCdrRequest;
use App\Entity\GetStatusCdrResponse;
use App\Entity\GetStatusRequest;
use App\Entity\GetStatusResponse;
use App\Entity\SendBillRequest;
use App\Entity\SendBillResponse;
use App\Entity\SendPackRequest;
use App\Entity\SendPackResponse;
use App\Entity\SendSummaryRequest;
use App\Entity\SendSummaryResponse;

interface BillServiceInterface
{
    public function sendBill(SendBillRequest $request): SendBillResponse;

    public function sendSummary(SendSummaryRequest $request): SendSummaryResponse;

    public function getStatus(GetStatusRequest $request): GetStatusResponse;

    public function sendPack(SendPackRequest $request): SendPackResponse;

    public function getStatusCdr(GetStatusCdrRequest $request): GetStatusCdrResponse;
}