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
     * XmlSchemaValidator constructor.
     * @param SchemaValidatorInterface $schema
     * @param PathResolverInterface $pathResolver
     */
    public function __construct(private SchemaValidatorInterface $schema, private PathResolverInterface $pathResolver)
    {
    }

    public function validate(string $filename, DOMDocument $document): array
    {
        if (empty($document->documentElement)) {
            return [new ValidationError(ErrorCodeList::XML_NO_RAIZ)];
        }

        $schemaPath = $this->pathResolver->getPath($document);
        if (empty($schemaPath) || !file_exists($schemaPath)) {
            return [new ValidationError(ErrorCodeList::XML_NO_SCHEMA_FILE)];
        }

        if ($this->schema->validate($document, $schemaPath)) {
            return [];
        }

        return [new ValidationError(ErrorCodeList::XML_NO_PARSE, $this->getErrorMessage($this->schema->getErrors()))];
    }

    /**
     * @param XmlError[] $errors
     * @return string
     */
    private function getErrorMessage(array $errors): string
    {
        $lines = [];
        foreach ($errors as $error) {
            $lines[] = (string) $error;
        }

        return join(PHP_EOL, $lines);
    }
}