<?php

declare(strict_types=1);

namespace App\Model;

class MinimalDocInfo
{
    /**
     * @var string|null
     */
    private $typeDoc;
    /**
     * @var string|null
     */
    private $id;
    /**
     * @var string|null
     */
    private $issuer;
    /**
     * @var string|null
     */
    private $issuerTypeDoc;
    /**
     * @var string|null
     */
    private $recipient;
    /**
     * @var string|null
     */
    private $recipientTypeDoc;

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