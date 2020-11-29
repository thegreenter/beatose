<?php

declare(strict_types=1);

namespace App\Services;

use App\Model\SoapCredential;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CredentialStore
{
    /**
     * CredentialStore constructor.
     * @param ParameterBagInterface $params
     */
    public function __construct(private ParameterBagInterface $params)
    {
    }

    public function get(): SoapCredential
    {
        $credential = new SoapCredential();

        return $credential
                ->setUser($this->params->get('app.soap_user'))
                ->setPassword($this->params->get('app.soap_pass'));
    }
}