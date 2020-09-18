<?php

declare(strict_types=1);

namespace App\Handler;

use App\Services\AuthSoapService;
use SoapServer;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SoapServerHandler implements RequestHandlerInterface
{
    /**
     * @var AuthSoapService
     */
    private $service;

    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    /**
     * @var ParameterBagInterface
     */
    private $params;

    /**
     * SoapServerHandler constructor.
     *
     * @param AuthSoapService $service
     * @param UrlGeneratorInterface $router
     * @param ParameterBagInterface $params
     */
    public function __construct(AuthSoapService $service, UrlGeneratorInterface $router, ParameterBagInterface $params)
    {
        $this->service = $service;
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
        $soapServer->setObject($this->service);

        ob_start();
        $soapServer->handle($request->getContent());
        $response->setContent(ob_get_clean());
        ob_clean();

        return $response;
    }
}