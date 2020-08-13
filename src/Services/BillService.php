<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\GetStatusResponse;
use App\Entity\SendBillResponse;
use App\Entity\SendPackResponse;
use App\Entity\SendSummaryResponse;
use App\Entity\StatusResponse;
use Greenter\Ubl\UblValidator;
use PhpZip\ZipFile;
use Psr\Log\LoggerInterface;
use SoapFault;
use Twig\Environment;

class BillService implements BillServiceInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Environment
     */
    private $twig;

    /**
     * BillService constructor.
     * @param LoggerInterface $logger
     * @param Environment $twig
     */
    public function __construct(LoggerInterface $logger, Environment $twig)
    {
        $this->logger = $logger;
        $this->twig = $twig;
    }

    public function sendBill(object $request): SendBillResponse
    {
        $zipFile = new ZipFile();
        $zipFile->openFromString($request->contentFile);

        $xmlPath = str_replace('.ZIP', '', strtoupper($request->fileName)).'.xml';
        $xml = $zipFile->getEntryContents($xmlPath);

        $zipFile->close();

        $validator = new UblValidator();

        if (!$validator->isValid($xml)) {
            throw new SoapFault(
                '0306',
                'No se puede leer (parsear) el archivo XML',
                null, $validator->getError(),
            );
        }

        $xmlResponse = $this->twig->render('ApplicationResponse.xml.twig');

        $zipFile = new ZipFile();
        $zipFile->addFromString('R-'.$xmlPath, $xmlResponse);
        $zip = $zipFile->outputAsString();
        $zipFile->close();

        $obj = new SendBillResponse();
        $obj->applicationResponse = $zip;

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