<?php

declare(strict_types=1);

namespace App\Validator;

use App\Model\ValidationError;
use App\Services\Xml\XslDocResolverInterface;
use DOMDocument;
use Greenter\Validator\Entity\CpeError;
use Greenter\Validator\Xml\XslValidator;

class CpeXslValidator implements XmlValidatorInterface
{
    private XslValidator $validator;

    private XslDocResolverInterface $xslDocResolver;

    /**
     * CpeXslValidator constructor.
     *
     * @param XslValidator $validator
     * @param XslDocResolverInterface $xslDocResolver
     */
    public function __construct(XslValidator $validator, XslDocResolverInterface $xslDocResolver)
    {
        $this->validator = $validator;
        $this->xslDocResolver = $xslDocResolver;
    }

    public function validate(string $filename, DOMDocument $document): ?ValidationError
    {
        $xslDoc = $this->xslDocResolver->fromDoc($document);
        if ($xslDoc === null) {
            return null;
        }

        $this->validator->setXsl($xslDoc);
        $result = $this->validator->validate($filename, $document);
        if (count($result) === 0) {
            return null;
        }

        $mapErrors = array_map(fn($error) => new ValidationError($error->getCode(), $this->getErrorMessage($error)), $result);

        return $mapErrors[0];
    }

    private function getErrorMessage(CpeError $error): string
    {
        return $error->getMessage().' (Nodo: '.$error->getNodePath().'='.$error->getNodeValue().').';
    }
}
