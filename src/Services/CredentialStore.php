<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\SoapCredential;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class CredentialStore
{
    /**
     * @var ContainerBagInterface
     */
    private $params;

    /**
     * CredentialStore constructor.
     * @param ContainerBagInterface $params
     */
    public function __construct(ContainerBagInterface $params)
    {
        $this->params = $params;
    }

    public function get(): SoapCredential
    {
        $credential = new SoapCredential();

        return $credential
                ->setUser($this->params->get('app.soap_user'))
                ->setPassword($this->params->get('app.soap_pass'));
    }
}