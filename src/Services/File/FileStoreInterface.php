<?php

declare(strict_types=1);

namespace App\Services\File;

interface FileStoreInterface
{
    /**
     * Save file
     * @param string $filename
     * @param string|null $content
     */
    public function save(string $filename, ?string $content): void;

    /**
     * Get saved file
     *
     * @param string $filename
     * @return string|null
     */
    public function get(string $filename): ?string;
}