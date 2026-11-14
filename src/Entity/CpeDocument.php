<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CpeDocumentRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CpeDocumentRepository::class)]
class CpeDocument
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 64, unique: true)]
    private ?string $name = null;

    #[ORM\Column(type: Types::STRING, length: 16, nullable: true)]
    private ?string $stateCode = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $hashCpe = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $hashCdr = null;

    #[ORM\Column(type: Types::STRING, length: 64, nullable: true)]
    private ?string $ticket = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?DateTime $createDate = null;

    public function getId(): ?int
    {
        return $this->id;
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
