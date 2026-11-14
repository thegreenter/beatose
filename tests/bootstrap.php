<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

if (file_exists(dirname(__DIR__).'/config/bootstrap.php')) {
    require dirname(__DIR__).'/config/bootstrap.php';
} elseif (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}

// Base de datos y archivos limpios para cada ejecución de la suite.
$projectDir = dirname(__DIR__);
@unlink($projectDir.'/var/test.db');
array_map('unlink', glob($projectDir.'/var/test_cpe_files/*') ?: []);

passthru(sprintf(
    '%s %s/bin/console doctrine:migrations:migrate --env=test --no-interaction --quiet',
    escapeshellarg(PHP_BINARY),
    escapeshellarg($projectDir),
), $exitCode);

if ($exitCode !== 0) {
    throw new RuntimeException('No se pudo preparar la base de datos de pruebas.');
}
