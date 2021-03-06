<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AbstractController
{
    /**
     * HomeController constructor.
     * @param ParameterBagInterface $params
     */
    public function __construct(private ParameterBagInterface $params)
    {
    }

    public function index(): Response
    {
        $hash = $this->params->get('container.build_hash');
        return $this->redirectToRoute('bill_service', ['wsdl' => '', 'hash' => $hash]);
    }
}