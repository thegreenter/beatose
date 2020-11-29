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
     * @return SoapCredential
     */
    public function setUser(?string $user): self
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
     * @return SoapCredential
     */
    public function setPassword(?string $password): self
    {
        $this->password = $password;
        return $this;
    }
}