<?php

declare(strict_types=1);

namespace App\Services;

use Greenter\XMLSecLibs\Certificate\X509Certificate;
use Greenter\XMLSecLibs\Certificate\X509ContentType;
use Greenter\XMLSecLibs\Sunat\SignedXml;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class Signer
{
    /**
     * @var ContainerBagInterface
     */
    private $params;

    /**
     * Signer constructor.
     * @param ContainerBagInterface $params
     */
    public function __construct(ContainerBagInterface $params)
    {
        $this->params = $params;
    }

    public function sign(string $xml): string
    {
        $pfxCert = $this->params->get('app.sign_cert');
        $password = $this->params->get('app.sign_pass');

        $certificate = new X509Certificate($pfxCert, $password);
        $pem = $certificate->export(X509ContentType::PEM);
        $signer = new SignedXml();
        $signer->setCertificate($pem);

        return $signer->signXml($xml);
    }
}