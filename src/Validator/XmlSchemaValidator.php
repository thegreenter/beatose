<?php

declare(strict_types=1);

namespace App\Validator;

use App\Model\ErrorCodeList;
use App\Model\ValidationError;
use DOMDocument;
use Greenter\Ubl\Resolver\PathResolverInterface;
use Greenter\Ubl\SchemaValidatorInterface;
use Greenter\Ubl\XmlError;

class XmlSchemaValidator implements XmlValidatorInterface
{
    /**
     * @var SchemaValidatorInterface
     */
    private SchemaValidatorInterface $schema;

    /**
     * @var PathResolverInterface
     */
    private PathResolverInterface $pathResolver;

    /**
     * XmlSchemaValidator constructor.
     * @param SchemaValidatorInterface $schema
     * @param PathResolverInterface $pathResolver
     */
    public function __construct(SchemaValidatorInterface $schema, PathResolverInterface $pathResolver)
    {
        $this->schema = $schema;
        $this->pathResolver = $pathResolver;
    }

    public function validate(string $filename, DOMDocument $document): array
    {
        if (empty($document->documentElement)) {
            return [new ValidationError(ErrorCodeList::XML_NO_RAIZ)];
        }

        $schemaPath = $this->pathResolver->getPath($document);
        if ($this->schema->validate($document, $schemaPath)) {
            return [];
        }

        if (empty($path) || !file_exists($path)) {
            new ValidationError(ErrorCodeList::XML_NO_SCHEMA_FILE);
        }

        return [new ValidationError(ErrorCodeList::XML_NO_PARSE, $this->getErrorMessage($this->schema->getErrors()))];
    }

    /**
     * @param XmlError[] $errors
     * @return string
     */
    private function getErrorMessage($errors)
    {
        $lines = [];
        foreach ($errors as $error) {
            $lines[] = (string) $error;
        }

        return join(PHP_EOL, $lines);
    }
}