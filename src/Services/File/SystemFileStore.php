<?php

declare(strict_types=1);

namespace App\Services\File;

use Psr\Log\LoggerInterface;
use Safe\Exceptions\FilesystemException;
use function Safe\file_get_contents;
use function Safe\file_put_contents;

class SystemFileStore implements FileStoreInterface
{
    /**
     * SystemFileStore constructor.
     * @param string $uploadDir
     * @param LoggerInterface $logger
     */
    public function __construct(private string $uploadDir, private LoggerInterface $logger)
    {
    }

    /**
     * @inheritDoc
     */
    public function save(string $filename, ?string $content): void
    {
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }

        $fullPath = $this->uploadDir.DIRECTORY_SEPARATOR.$filename;
        file_put_contents($fullPath, $content);
    }

    /**
     * @inheritDoc
     */
    public function get(string $filename): ?string
    {
        $fullPath = $this->uploadDir.DIRECTORY_SEPARATOR.$filename;
        if (!file_exists($fullPath)) {
            $this->logger->warning('File not found: '.$fullPath);
            return null;
        }

        try {
            return file_get_contents($fullPath);
        } catch (FilesystemException $e) {
            $this->logger->warning('File cannot read: '.$fullPath);
        }

        return null;
    }
}