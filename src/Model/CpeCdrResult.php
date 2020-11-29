<?php

declare(strict_types=1);

namespace App\Model;

use DateTime;

class CpeCdrResult
{
    private ?DateTime $dateReceived = null;

    private ?string $codeResult = null;

    /**
     * @var ValidationError[]|null
     */
    private ?array $errorList = null;

    private ?string $ticket = null;

    /**
     * @return DateTime|null
     */
    public function getDateReceived(): ?DateTime
    {
        return $this->dateReceived;
    }

    /**
     * @param DateTime|null $dateReceived
     * @return static
     */
    public function setDateReceived(?DateTime $dateReceived): static
    {
        $this->dateReceived = $dateReceived;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCodeResult(): ?string
    {
        return $this->codeResult;
    }

    /**
     * @param string|null $codeResult
     * @return static
     */
    public function setCodeResult(?string $codeResult): static
    {
        $this->codeResult = $codeResult;
        return $this;
    }

    /**
     * @return ValidationError[]|null
     */
    public function getErrorList(): ?array
    {
        return $this->errorList;
    }

    /**
     * @param ValidationError[]|null $errorList
     * @return static
     */
    public function setErrorList(?array $errorList): static
    {
        $this->errorList = $errorList;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTicket(): ?string
    {
        return $this->ticket;
    }

    /**
     * @param string|null $ticket
     * @return static
     */
    public function setTicket(?string $ticket): static
    {
        $this->ticket = $ticket;
        return $this;
    }
}