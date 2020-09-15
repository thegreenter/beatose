<?php

declare(strict_types=1);

namespace App\Services\Bill;

use App\Model\{
    GetStatusCdrRequest,
    GetStatusCdrResponse,
    GetStatusRequest,
    GetStatusResponse,
    SendBillRequest,
    SendBillResponse,
    SendPackRequest,
    SendPackResponse,
    SendSummaryRequest,
    SendSummaryResponse,
};

interface BillServiceInterface
{
    public function sendBill(SendBillRequest $request): SendBillResponse;

    public function sendSummary(SendSummaryRequest $request): SendSummaryResponse;

    public function getStatus(GetStatusRequest $request): GetStatusResponse;

    public function sendPack(SendPackRequest $request): SendPackResponse;

    public function getStatusCdr(GetStatusCdrRequest $request): GetStatusCdrResponse;
}