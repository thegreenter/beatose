<?php

declare(strict_types=1);

namespace App\Handler;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class WsdlHandler implements RequestHandlerInterface
{
    /**
     * WsdlHandler constructor.
     *
     * @param RequestHandlerInterface $next
     * @param UrlGeneratorInterface $router
     * @param ParameterBagInterface $params
     */
    public function __construct(private RequestHandlerInterface $next, private UrlGeneratorInterface $router, private ParameterBagInterface $params)
    {
    }

    public function handle(Request $request): Response
    {
        if (!$request->query->has('wsdl')) {

            return $this->next->handle($request);
        }

        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml; charset=utf-8');
        $endpoint = $this->router->generate('bill_service', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $wsdlPath = $this->params->get('app.soap_wsdl');
        $wsdl = file_get_contents($wsdlPath);

        return $response->setContent(str_replace('%URL_SERVICE%', $endpoint, $wsdl));
    }
}