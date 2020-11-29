<?php

declare(strict_types=1);

namespace App\Validator;

use DOMDocument;

class MultiValidator implements XmlValidatorInterface
{
    /**
     * MultiValidator constructor.
     *
     * @param XmlValidatorInterface[] $validators
     */
    public function __construct(private array $validators)
    {
    }

    /**
     * @inheritDoc
     */
    public function validate(string $filename, DOMDocument $document): array
    {
        foreach ($this->validators as $validator) {
            $errors = $validator->validate($filename, $document);
            if (count($errors) > 0) {
                return $errors;
            }
        }

        return [];
    }
}