<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\ApplicationResponse;
use App\Entity\GetStatusResponse;
use App\Entity\SendBillResponse;
use App\Entity\SendPackResponse;
use App\Entity\SendSummaryResponse;
use App\Entity\StatusResponse;
use DateTime;
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
     * @var Signer
     */
    private $signer;

    /**
     * BillService constructor.
     * @param LoggerInterface $logger
     * @param Environment $twig
     * @param Signer $signer
     */
    public function __construct(LoggerInterface $logger, Environment $twig, Signer $signer)
    {
        $this->logger = $logger;
        $this->twig = $twig;
        $this->signer = $signer;
    }

    public function sendBill(object $request): SendBillResponse
    {
        $dateReceived = new DateTime();
        $zipFile = new ZipFile();
        $zipFile->openFromString($request->contentFile);

        $docName = str_replace('.ZIP', '', strtoupper($request->fileName));
        $xmlPath = $docName.'.xml';
        if (!$zipFile->hasEntry($xmlPath)) {
            throw new SoapFault('0157', 'El archivo ZIP no contiene comprobantes');
        }
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

        $cdr = (new ApplicationResponse())
            ->setId((string)(int)(microtime(true) * 1000))
            ->setFechaRecepcion($dateReceived)
            ->setFechaGeneracion(new DateTime())
            ->setRucEmisorCdr('20000000001')
            ->setRucEmisorCpe(substr($docName, 0, 11))
            ->setTipoDocReceptorCpe('6')
            ->setNroDocReceptorCpe('20000000002')
            ->setCpeId(substr($docName, 15, strlen($docName) - 15))
            ->setCodigoRespuesta('0')
            ->setCodigoRespuesta('El comprobante ha sido aceptado')
            ->setNotasAsociadas([])
        ;
        $xmlResponse = $this->twig->render('ApplicationResponse.xml.twig', ['doc' => $cdr]);

        $zipFile = new ZipFile();
        $zipFile->addFromString('R-'.$xmlPath, $this->signer->sign($xmlResponse));
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
        $obj->ticket = (string)(int)(microtime(true) * 1000);

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