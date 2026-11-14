<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\ProcessSummary;
use App\Services\Bill\SummaryProcessor;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class ProcessSummaryHandler
{
    public function __construct(private SummaryProcessor $processor)
    {
    }

    public function __invoke(ProcessSummary $message): void
    {
        $this->processor->process($message->ticket, $message->fileName);
    }
}
