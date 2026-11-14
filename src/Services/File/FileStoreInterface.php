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

    /**
     * Delete saved file (no-op if it doesn't exist)
     *
     * @param string $filename
     */
    public function delete(string $filename): void;
}