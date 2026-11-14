<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\ProcessPack;
use App\Services\Bill\PackProcessor;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class ProcessPackHandler
{
    public function __construct(private PackProcessor $processor)
    {
    }

    public function __invoke(ProcessPack $message): void
    {
        $this->processor->process($message->ticket, $message->fileName);
    }
}
