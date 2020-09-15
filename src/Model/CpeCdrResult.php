<?php

declare(strict_types=1);

namespace App\Model;

use DateTime;

class CpeCdrResult
{
    /**
     * @var DateTime|null
     */
    private $dateReceived;
    /**
     * @var string|null
     */
    private $codeResult;
    /**
     * @var array|null
     */
    private $notes;

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
     * @return array|null
     */
    public function getNotes(): ?array
    {
        return $this->notes;
    }

    /**
     * @param array|null $notes
     * @return CpeCdrResult
     */
    public function setNotes(?array $notes): CpeCdrResult
    {
        $this->notes = $notes;
        return $this;
    }
}