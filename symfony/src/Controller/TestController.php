<?php

namespace App\Controller;

use App\EventListeners\BatchQueueEvent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class TestController extends AbstractController
{
    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/test', name: 'app_test')]
    public function index(): Response
    {
        return $this->json([
            'message' => 'Test endpoint is working!',
            'status' => 'success',
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/notification', name: 'app_notification')]
    public function notification(MailerInterface $mailer): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $email = (new Email())
            ->from('hello@example.com')
            ->to('sender@example.com')
            ->subject('Time for Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->html('<p>See Twig integration for better HTML integration!</p>');

        $mailer->send($email);

        return $this->json([
            'message' => 'Test Symfony mailer sending email endpoint is working!',
            'status' => 'success',
        ]);
    }

    #[Route('/batch-notification', name: 'app_batch_notification')]
    public function batchNotification(EventDispatcherInterface $dispatcher): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $event = new BatchQueueEvent('This is a test batch notification', BatchQueueEvent::NAME);
        $dispatcher->dispatch($event, BatchQueueEvent::NAME);

        return $this->json([
            'message' => 'Test Symfony batch notification is working!',
            'status' => 'success',
        ]);
    }
}
