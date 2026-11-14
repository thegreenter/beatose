<?php

declare(strict_types=1);

namespace App\Services\Soap;

/**
 * Petición SOAP ya leída: operación, credenciales WS-Security y parámetros.
 */
final readonly class SoapRequest
{
    /**
     * @param array<string, string> $parameters hijos directos del elemento de la operación
     */
    public function __construct(
        public string $operation,
        public ?string $username,
        public ?string $password,
        public array $parameters,
    ) {
    }

    public function get(string $name): ?string
    {
        return $this->parameters[$name] ?? null;
    }
}
