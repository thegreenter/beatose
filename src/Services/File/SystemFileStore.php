<?php

declare(strict_types=1);

namespace App\Services\File;

use Psr\Log\LoggerInterface;

class SystemFileStore implements FileStoreInterface
{
    /**
     * Directory to save files.
     *
     * @var string
     */
    private string $uploadDir;

    private LoggerInterface $logger;

    /**
     * SystemFileStore constructor.
     * @param string $uploadDir
     * @param LoggerInterface $logger
     */
    public function __construct(string $uploadDir, LoggerInterface $logger)
    {
        $this->uploadDir = $uploadDir;
        $this->logger = $logger;
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

        $content = file_get_contents($fullPath);
        if ($content === false) {
            $this->logger->warning('File cannot read: '.$fullPath);
            return null;
        }

        return $content;
    }
}