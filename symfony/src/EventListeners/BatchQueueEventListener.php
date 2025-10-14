<?php

namespace App\EventListeners;

use App\Service\Queue\BatchQueueService;
use Psr\Log\LoggerInterface;

final class BatchQueueEventListener
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly BatchQueueService $service
    ) {}

    public function process(BatchQueueEventInterface $event): void
    {
        try{
            $this->service->publish($event);

            $this->logger->debug("Queue publish process start for batch queue event.", [
                '$event' => get_class($event),
                '$eventName' => $event::NAME
            ]);

        } catch (\Throwable $e){
            // Catch any exception to avoid breaking the event dispatching flow
            $this->logger->error("Error logging batch queue event start: " . $e->getMessage());
        }

    }
}
