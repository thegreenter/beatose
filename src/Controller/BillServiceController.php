<?php

declare(strict_types=1);

namespace App\Controller;

use App\Handler\RequestHandlerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BillServiceController extends AbstractController
{
    private RequestHandlerInterface $handler;

    public function __construct(RequestHandlerInterface $handler)
    {
        $this->handler = $handler;
    }

    public function index(Request $request): Response
    {
        return $this->handler->handle($request);
    }
}