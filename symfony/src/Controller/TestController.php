<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
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
}
