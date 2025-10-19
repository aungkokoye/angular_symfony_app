<?php

namespace App\Infrastructure\MessagePublisher;

use App\Domain\Message\MessageInterface;
use App\Domain\Student\Message\StudentMessage;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class StudentMessagePublisher
{

    public function __construct(
        private readonly MessageBusInterface $messageBus
    ) {}

    /**
     * Publish notification message with routing key pattern: messenger.notification.{student}.{welcome}
     * @throws ExceptionInterface
     */
    public function publishNotification(string $name, string $type, MessageInterface $message): void
    {
        // Create routing key: messenger.notification.{student}.{welcome}
        $routingKey = "messenger.notification.{$name}.{$type}";

        $this->messageBus->dispatch(
            (new Envelope($message))
                ->with(new AmqpStamp($routingKey))
        );
    }


}
