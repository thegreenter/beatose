<?php

declare(strict_types=1);

namespace App\Validator;

use App\Model\ValidationError;
use App\Services\Xml\XslDocResolverInterface;
use DOMDocument;
use Greenter\Validator\Entity\CpeError;
use Greenter\Validator\Xml\XslValidatorInterface;

class CpeXslValidator implements XmlValidatorInterface
{
    private XslValidatorInterface $validator;

    private XslDocResolverInterface $xslDocResolver;

    /**
     * CpeXslValidator constructor.
     * @param XslValidatorInterface $validator
     * @param XslDocResolverInterface $xslDocResolver
     */
    public function __construct(XslValidatorInterface $validator, XslDocResolverInterface $xslDocResolver)
    {
        $this->validator = $validator;
        $this->xslDocResolver = $xslDocResolver;
    }

    public function validate(string $filename, DOMDocument $document): array
    {
        $xslDoc = $this->xslDocResolver->fromDoc($document);
        if ($xslDoc === null) {
            return [];
        }

        $this->validator->setXsl($xslDoc);
        $result = $this->validator->validate($filename, $document);
        if (count($result) === 0) {
            return [];
        }

        return array_map(fn($error) => new ValidationError($error->getCode(), $this->getErrorMessage($error)), $result);
    }

    private function getErrorMessage(CpeError $error): string
    {
        return $error->getMessage().' (nodo: "'.$error->getNodePath().'", valor:"'.$error->getNodeValue().'")';
    }
}
