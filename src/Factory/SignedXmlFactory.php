<?php

declare(strict_types=1);

namespace App\Factory;

use Greenter\XMLSecLibs\Certificate\X509Certificate;
use Greenter\XMLSecLibs\Certificate\X509ContentType;
use Greenter\XMLSecLibs\Sunat\SignedXml;

class SignedXmlFactory
{
    public static function createSignedXml(?string $pfxCert, ?string $password): SignedXml
    {
        $certificate = new X509Certificate($pfxCert, $password);
        $pem = $certificate->export(X509ContentType::PEM);
        $signer = new SignedXml();
        $signer->setCertificate($pem);

        return $signer;
    }
}