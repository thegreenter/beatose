<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BillServiceController extends AbstractController
{
    public function index()
    {
        return $this->json(['message' => 'Soap Server']);
    }
}