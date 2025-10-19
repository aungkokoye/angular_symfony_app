<?php

namespace App\Application\Student\MessageHandler;

use App\Domain\Student\Message\StudentMessage;
use App\Repository\UserRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class StudentMessageHandler
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly UserRepository $repo
    ) {}

    public function __invoke(StudentMessage $message): void
    {
        $this->logger->info('Processing Student Message Handler Start!', ['Type' => $message->getType(), 'Name' => $message->getName()]);

        if (method_exists($this, $message->getType())) {
            $this->{$message->getType()}($message);
        }

        $this->logger->info('Processing Student Message Handler End!');
    }

    private function welcome(StudentMessage $message): void
    {
        $userID = $message->getStudentId();
        $user = $this->repo->findOneBy(['id' => $userID]);
        $userName = $user ? $user->getName() : 'Student';

        $this->logger->info('Welcome message sent to ' . $userName . ' !');
    }
}
