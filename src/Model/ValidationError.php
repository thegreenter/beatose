<?php

declare(strict_types=1);

namespace App\Model;

class ValidationError
{
    private ?string $code;

    private ?string $detail;

    /**
     * ValidationError constructor.
     * @param string $code
     * @param string|null $detail
     */
    public function __construct(string $code, ?string $detail = null)
    {
        $this->code = $code;
        $this->detail = $detail;
    }

    /**
     * @return string|null
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @param string|null $code
     * @return ValidationError
     */
    public function setCode(?string $code): self
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
     * @return ValidationError
     */
    public function setDetail(?string $detail): self
    {
        $this->detail = $detail;
        return $this;
    }
}