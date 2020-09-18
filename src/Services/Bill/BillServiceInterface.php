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
use SoapFault;

interface BillServiceInterface
{
    /**
     * @param SendBillRequest $request
     * @return SendBillResponse
     * @throws SoapFault
     */
    public function sendBill(SendBillRequest $request): SendBillResponse;

    /**
     * @param SendSummaryRequest $request
     * @return SendSummaryResponse
     * @throws SoapFault
     */
    public function sendSummary(SendSummaryRequest $request): SendSummaryResponse;

    /**
     * @param GetStatusRequest $request
     * @return GetStatusResponse
     * @throws SoapFault
     */
    public function getStatus(GetStatusRequest $request): GetStatusResponse;

    /**
     * @param SendPackRequest $request
     * @return SendPackResponse
     * @throws SoapFault
     */
    public function sendPack(SendPackRequest $request): SendPackResponse;

    /**
     * @param GetStatusCdrRequest $request
     * @return GetStatusCdrResponse
     * @throws SoapFault
     */
    public function getStatusCdr(GetStatusCdrRequest $request): GetStatusCdrResponse;
}