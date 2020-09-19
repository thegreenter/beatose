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
     * @return CpeCdrResult
     */
    public function setDateReceived(?DateTime $dateReceived): CpeCdrResult
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
     * @return CpeCdrResult
     */
    public function setCodeResult(?string $codeResult): CpeCdrResult
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
     * @return CpeCdrResult
     */
    public function setErrorList(?array $errorList): CpeCdrResult
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
     * @return CpeCdrResult
     */
    public function setTicket(?string $ticket): CpeCdrResult
    {
        $this->ticket = $ticket;
        return $this;
    }
}