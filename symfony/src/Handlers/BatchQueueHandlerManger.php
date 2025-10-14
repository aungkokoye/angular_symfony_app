<?php

namespace App\Handlers;

use App\EventListeners\BatchQueueEventInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

class BatchQueueHandlerManger
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly BatchInvitationQueueHandler $userInvitationQueueHandler
    ){}

    public function handle(AMQPMessage $msg): void
    {
        $this->logger->debug("BatchQueueHandlerManger handler method starts.");
        $object = json_decode($msg->getBody());
        $key = $object->{BatchQueueEventInterface::DATA_KEY_NAME};
        $content = $object->{BatchQueueEventInterface::DATA_CONTENT_NAME};
        $handler = $this->getHandler($key);
        if ($handler instanceof BatchQueueHandlerInterface){
            $handler->handle($content, $key);
        }
    }

    public function getHandler(string $key): ?BatchQueueHandlerInterface
    {
        $handler = null;
        switch ($key) {
            case BatchQueueEventInterface::USER_BATCH_NOTIFICATION:
                $handler = $this->userInvitationQueueHandler;
                break;
            default:
                $this->logger->error("Key is not valid for BatchQueueHandler class!", ['key' => $key]);
        }
        return $handler;
    }
}
