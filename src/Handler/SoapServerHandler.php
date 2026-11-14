<?php

declare(strict_types=1);

namespace App\Handler;

use App\Services\AuthSoapService;
use Closure;
use SoapServer;
use Symfony\Component\DependencyInjection\Attribute\AutowireServiceClosure;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SoapServerHandler implements RequestHandlerInterface
{
    /**
     * SoapServerHandler constructor.
     *
     * @param Closure(): AuthSoapService $serviceFactory devuelve una instancia nueva en cada llamada
     *                                                  (AuthSoapService no es un servicio compartido)
     * @param UrlGeneratorInterface $router
     * @param ParameterBagInterface $params
     */
    public function __construct(
        #[AutowireServiceClosure(AuthSoapService::class)]
        private Closure $serviceFactory,
        private UrlGeneratorInterface $router,
        private ParameterBagInterface $params,
    ) {
    }

    public function handle(Request $request): Response
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml; charset=utf-8');
        $endpoint = $this->router->generate('bill_service', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $wsdlPath = $this->params->get('app.soap_wsdl');

        $options = ['uri' => $endpoint];
        $soapServer = new SoapServer($wsdlPath, $options);
        // Estado de autenticación aislado por petición.
        $soapServer->setObject(($this->serviceFactory)());

        ob_start();
        $soapServer->handle($request->getContent());
        $response->setContent(ob_get_clean());

        return $response;
    }
}
