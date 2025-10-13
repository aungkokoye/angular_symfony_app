<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

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

    #[Route('/notification', name: 'app_notification')]
    public function notification(MailerInterface $mailer): Response
    {
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
}
