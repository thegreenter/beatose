<?php

declare(strict_types=1);

namespace App\Model;

class ValueResult
{
    private ?ValidationError $error = null;

    private ?string $content = null;

    /**
     * @return ValidationError|null
     */
    public function getError(): ?ValidationError
    {
        return $this->error;
    }

    /**
     * @param ValidationError|null $error
     * @return static
     */
    public function setError(?ValidationError $error): static
    {
        $this->error = $error;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @param string|null $content
     * @return static
     */
    public function setContent(?string $content): static
    {
        $this->content = $content;
        return $this;
    }
}