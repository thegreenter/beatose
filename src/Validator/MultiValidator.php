<?php

declare(strict_types=1);

namespace App\Validator;

use App\Model\ValidationError;
use DOMDocument;

class MultiValidator implements XmlValidatorInterface
{
    /**
     * @var XmlValidatorInterface[]
     */
    private $validators;

    /**
     * MultiValidator constructor.
     *
     * @param XmlValidatorInterface[] $validators
     */
    public function __construct(array $validators)
    {
        $this->validators = $validators;
    }

    /**
     * @param string $filename
     * @param DOMDocument $document
     * @return ValidationError|null
     */
    public function validate(string $filename, DOMDocument $document): ?ValidationError
    {
        foreach ($this->validators as $validator) {
            $error = $validator->validate($filename, $document);
            if ($error !== null) {
                return $error;
            }
        }

        return null;
    }
}