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
     * @return ValueResult
     */
    public function setError(?ValidationError $error): self
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
     * @return ValueResult
     */
    public function setContent(?string $content): self
    {
        $this->content = $content;
        return $this;
    }
}