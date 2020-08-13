<?php

declare(strict_types=1);

namespace App\Handler;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface RequestHandlerInterface
{
    public function handle(Request $request): Response;
}