<?php

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

// symfony/runtime detecta FRANKENPHP_WORKER y, si está activo, mantiene el
// kernel en memoria atendiendo peticiones en bucle (modo worker). Sin
// FrankenPHP funciona igual que antes (php -S, Apache, PHP-FPM).
return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
