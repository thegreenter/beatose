<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\GetStatusResponse;
use App\Entity\SendBillResponse;
use App\Entity\SendPackResponse;
use App\Entity\SendSummaryResponse;
use App\Entity\StatusResponse;
use Psr\Log\LoggerInterface;
use SoapFault;

class BillService implements BillServiceInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * BillService constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function sendBill(object $request): SendBillResponse
    {
        file_put_contents($request->fileName, $request->contentFile);

        $obj = new SendBillResponse();
        $obj->applicationResponse = 'xxxx';

        return $obj;
    }

    public function sendSummary(object $request): SendSummaryResponse
    {
        file_put_contents($request->fileName, $request->contentFile);

        $obj = new SendSummaryResponse();
        $obj->ticket = '1597268056006';

        return $obj;
    }

    public function getStatus(object $request): GetStatusResponse
    {
        $ticket = $request->ticket;
        $this->logger->info('Ticket '.$ticket);

        $obj = new GetStatusResponse();
        $obj->status = new StatusResponse();
        $obj->status->content = 'xxxxxx';
        $obj->status->statusCode = $ticket;

        return $obj;
    }

    /**
     * @param object $request
     * @return SendPackResponse
     *
     * @throws SoapFault
     */
    public function sendPack(object $request): SendPackResponse
    {
        throw new SoapFault('0000', 'NO IMPLEMENTADO');
    }
}