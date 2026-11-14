<?php

declare(strict_types=1);

namespace App\Model;

use DateTime;

/**
 * Registro de un comprobante recibido (tabla cpe_document).
 *
 * @see \App\Repository\CpeDocumentRepository
 */
class CpeDocument
{
    private ?int $id = null;

    private ?string $name = null;

    private ?string $stateCode = null;

    private ?string $hashCpe = null;

    private ?string $hashCdr = null;

    private ?string $ticket = null;

    private ?DateTime $createDate = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getStateCode(): ?string
    {
        return $this->stateCode;
    }

    public function setStateCode(?string $stateCode): self
    {
        $this->stateCode = $stateCode;

        return $this;
    }

    public function getHashCpe(): ?string
    {
        return $this->hashCpe;
    }

    public function setHashCpe(?string $hashCpe): self
    {
        $this->hashCpe = $hashCpe;

        return $this;
    }

    public function getHashCdr(): ?string
    {
        return $this->hashCdr;
    }

    public function setHashCdr(?string $hashCdr): self
    {
        $this->hashCdr = $hashCdr;

        return $this;
    }

    public function getTicket(): ?string
    {
        return $this->ticket;
    }

    public function setTicket(?string $ticket): self
    {
        $this->ticket = $ticket;

        return $this;
    }

    public function getCreateDate(): ?DateTime
    {
        return $this->createDate;
    }

    public function setCreateDate(?DateTime $createDate): CpeDocument
    {
        $this->createDate = $createDate;
        return $this;
    }
}
