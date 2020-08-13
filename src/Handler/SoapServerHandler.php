<?php

declare(strict_types=1);

namespace App\Handler;

use App\Services\BillServiceInterface;
use SoapServer;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SoapServerHandler implements RequestHandlerInterface
{
    /**
     * @var BillServiceInterface
     */
    private $billService;

    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    /**
     * @var ContainerBagInterface
     */
    private $params;

    /**
     * SoapServerHandler constructor.
     *
     * @param BillServiceInterface $billService
     * @param UrlGeneratorInterface $router
     * @param ContainerBagInterface $params
     */
    public function __construct(BillServiceInterface $billService, UrlGeneratorInterface $router, ContainerBagInterface $params)
    {
        $this->billService = $billService;
        $this->router = $router;
        $this->params = $params;
    }

    public function handle(Request $request): Response
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml; charset=utf-8');
        $endpoint = $this->router->generate('bill_service', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $wsdlPath = $this->params->get('app.soap_wsdl');

        $options = ['uri' => $endpoint];
        $soapServer = new SoapServer($wsdlPath, $options);
        $soapServer->setObject($this->billService);

        ob_start();
        $soapServer->handle();
        $response->setContent(ob_get_clean());

        return $response;
    }
}