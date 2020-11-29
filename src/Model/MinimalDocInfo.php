<?php

declare(strict_types=1);

namespace App\Model;

class MinimalDocInfo
{
    private ?string $typeDoc = null;
    private ?string $id = null;
    private ?string $issuer = null;
    private ?string $issuerTypeDoc = null;
    private ?string $recipient = null;
    private ?string $recipientTypeDoc = null;

    /**
     * @return string|null
     */
    public function getTypeDoc(): ?string
    {
        return $this->typeDoc;
    }

    /**
     * @param string|null $typeDoc
     * @return MinimalDocInfo
     */
    public function setTypeDoc(?string $typeDoc): MinimalDocInfo
    {
        $this->typeDoc = $typeDoc;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param string|null $id
     * @return MinimalDocInfo
     */
    public function setId(?string $id): MinimalDocInfo
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getIssuer(): ?string
    {
        return $this->issuer;
    }

    /**
     * @param string|null $issuer
     * @return MinimalDocInfo
     */
    public function setIssuer(?string $issuer): MinimalDocInfo
    {
        $this->issuer = $issuer;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getIssuerTypeDoc(): ?string
    {
        return $this->issuerTypeDoc;
    }

    /**
     * @param string|null $issuerTypeDoc
     * @return MinimalDocInfo
     */
    public function setIssuerTypeDoc(?string $issuerTypeDoc): MinimalDocInfo
    {
        $this->issuerTypeDoc = $issuerTypeDoc;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRecipient(): ?string
    {
        return $this->recipient;
    }

    /**
     * @param string|null $recipient
     * @return MinimalDocInfo
     */
    public function setRecipient(?string $recipient): MinimalDocInfo
    {
        $this->recipient = $recipient;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRecipientTypeDoc(): ?string
    {
        return $this->recipientTypeDoc;
    }

    /**
     * @param string|null $recipientTypeDoc
     * @return MinimalDocInfo
     */
    public function setRecipientTypeDoc(?string $recipientTypeDoc): MinimalDocInfo
    {
        $this->recipientTypeDoc = $recipientTypeDoc;
        return $this;
    }
}