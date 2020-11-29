<?php

declare(strict_types=1);

namespace App\Services\Cdr;

use App\Model\ApplicationResponse;
use Greenter\XMLSecLibs\Sunat\SignedXml;
use Twig\Environment;

class XmlCdrWriter implements CdrWriterInterface
{
    private SignedXml $signer;

    private Environment $twig;

    /**
     * XmlCdrWriter constructor.
     *
     * @param SignedXml $signer
     * @param Environment $twig
     */
    public function __construct(SignedXml $signer, Environment $twig)
    {
        $this->signer = $signer;
        $this->twig = $twig;
    }

    public function write(ApplicationResponse $applicationResponse): ?string
    {
        $xml = $this->twig->render('ApplicationResponse.xml.twig', ['doc' => $applicationResponse]);

        return $this->signer->signXml($xml);
    }
}