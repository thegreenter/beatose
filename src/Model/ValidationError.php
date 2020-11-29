<?php

declare(strict_types=1);

namespace App\Model;

class ValidationError
{
    /**
     * ValidationError constructor.
     * @param string $code
     * @param string|null $detail
     */
    public function __construct(private string $code, private ?string $detail = null)
    {
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     * @return static
     */
    public function setCode(string $code): static
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDetail(): ?string
    {
        return $this->detail;
    }

    /**
     * @param string|null $detail
     * @return static
     */
    public function setDetail(?string $detail): static
    {
        $this->detail = $detail;
        return $this;
    }
}