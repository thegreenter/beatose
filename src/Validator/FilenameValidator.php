<?php

declare(strict_types=1);

namespace App\Validator;

class FilenameValidator
{
    private const TYPE_CODE_INDEX = 1;

    public function isAllow(?string $filename, array $allowTypes): bool
    {
        $parts = explode('-', $filename);

        if (count($parts) !== 4) return false;

        $cpeType = $parts[self::TYPE_CODE_INDEX];

        return in_array($cpeType, $allowTypes);
    }
}