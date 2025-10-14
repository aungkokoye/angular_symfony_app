<?php

namespace App\EventListeners;

use Symfony\Contracts\EventDispatcher\Event;

class BatchQueueEvent extends Event implements BatchQueueEventInterface
{
    public const NAME = self::USER_BATCH_NOTIFICATION;

    public function __construct(
        private readonly string $content,
        private readonly string $key
    ){}

    public function getData(): string
    {
        $data = [
            self::DATA_KEY_NAME     => $this->getKey(),
            self::DATA_CONTENT_NAME => $this->content
        ];

        return json_encode($data);
    }

    public function getKey(): string
    {
        return $this->key;
    }
}
