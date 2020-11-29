<?php

declare(strict_types=1);

namespace App\Services\Xml;

use DOMDocument;
use Greenter\Validator\Resolver\RuleResolverInterface;
use Greenter\Validator\Resolver\TypeResolverInterface;

class FileSystemXslDocResolver implements XslDocResolverInterface
{
    /**
     * FileSystemXslDocResolver constructor.
     *
     * @param TypeResolverInterface $typeResolver
     * @param RuleResolverInterface $ruleResolver
     */
    public function __construct(private TypeResolverInterface $typeResolver, private RuleResolverInterface $ruleResolver)
    {
    }

    public function fromDoc(DOMDocument $document): ?DOMDocument
    {
        $type = $this->typeResolver->getType($document);
        $path = $this->ruleResolver->getPath($type);
        if ($path === null || !file_exists($path)) {
            return null;
        }

        $xslDoc = new DOMDocument();
        $xslDoc->load($path);

        return $xslDoc;
    }
}