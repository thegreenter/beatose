<?php

declare(strict_types=1);

namespace App\Controller;

use App\Services\BillServiceInterface;
use SoapServer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class BillServiceController extends AbstractController
{
    /**
     * @var BillServiceInterface
     */
    private $billService;

    /**
     * BillServiceController constructor.
     *
     * @param BillServiceInterface $billService
     */
    public function __construct(BillServiceInterface $billService)
    {
        $this->billService = $billService;
    }

    public function index(Request $request)
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml; charset=utf-8');
        $endpoint = $this->generateUrl('bill_service', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $wsdlPath = __DIR__.'/../../public/billService.wsdl';

        if ($request->query->has('wsdl')) {
            $wsdl = file_get_contents($wsdlPath);

            return $response->setContent(str_replace('%URL_SERVICE%', $endpoint, $wsdl));
        }

        $options = ['uri' => $endpoint];
        $soapServer = new SoapServer($wsdlPath, $options);
        $soapServer->setObject($this->billService);

        ob_start();
        $soapServer->handle();
        $response->setContent(ob_get_clean());

        return $response;
    }
}