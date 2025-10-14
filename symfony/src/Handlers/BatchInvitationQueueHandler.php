<?php

namespace App\Handlers;

use Psr\Log\LoggerInterface;

class BatchInvitationQueueHandler implements BatchQueueHandlerInterface
{
    public function __construct(
        private readonly LoggerInterface     $logger
    )
    {}

    public function handle(string $content, string $key): void
    {
        $this->logger->debug("BatchInvitationQueueHandler handle method starts.", [[$key, $content]]);
    }
}
