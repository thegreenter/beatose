<?php

declare(strict_types=1);

namespace App\Model;

class SoapCredential
{
    private ?string $user = null;
    private ?string $password = null;

    /**
     * @return string|null
     */
    public function getUser(): ?string
    {
        return $this->user;
    }

    /**
     * @param string|null $user
     * @return static
     */
    public function setUser(?string $user): static
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string|null $password
     * @return static
     */
    public function setPassword(?string $password): static
    {
        $this->password = $password;
        return $this;
    }
}