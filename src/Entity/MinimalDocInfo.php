<?php

declare(strict_types=1);

namespace App\Entity;

class MinimalDocInfo
{
    /**
     * @var string
     */
    private $typeDoc;
    /**
     * @var string
     */
    private $id;
    /**
     * @var string
     */
    private $issuer;
    /**
     * @var string
     */
    private $issuerTypeDoc;
    /**
     * @var string
     */
    private $recipient;
    /**
     * @var string
     */
    private $recipientTypeDoc;
}