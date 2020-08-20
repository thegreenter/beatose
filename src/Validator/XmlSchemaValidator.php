<?php

declare(strict_types=1);

namespace App\Validator;

use App\Entity\ValidationError;
use DOMDocument;
use Greenter\Ubl\Resolver\PathResolverInterface;
use Greenter\Ubl\SchemaValidatorInterface;
use Greenter\Ubl\XmlError;

class XmlSchemaValidator implements XmlValidatorInterface
{
    /**
     * @var SchemaValidatorInterface
     */
    private $schema;

    /**
     * @var PathResolverInterface
     */
    private $pathResolver;

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

    public function validate(string $filename, DOMDocument $document): ?ValidationError
    {
        if (empty($document->documentElement)) {
            return new ValidationError('0300');
        }

        $schemaPath = $this->pathResolver->getPath($document);
        if ($this->schema->validate($document, $schemaPath)) {
            return null;
        }

        if (empty($path) || !file_exists($path)) {
            new ValidationError('0304');
        }

        return new ValidationError('0306', $this->getErrorMessage($this->schema->getErrors()));
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