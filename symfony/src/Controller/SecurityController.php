<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class SecurityController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(): RedirectResponse
    {
        /** redirect to Angular FE */
        return new RedirectResponse('/');
    }

    #[Route('/login', name: 'app_login', methods: ['POST'])]
    public function login(): Response
    {
        /** redirect to App\Service\Security\LoginSuccessHandler::onAuthenticationSuccess() */
        return $this->json([
            'message' => 'Login successful.',
        ]);
    }

    #[Route('/logout', name: 'api_logout', methods: ['POST'])]
    public function logout(Request $request, TokenStorageInterface $tokenStorage): JsonResponse
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->json(['message' => 'Full authentication required!'], 401);
        }

        // Manually clear the token and invalidate the session
        $tokenStorage->setToken(null);
        $request->getSession()->invalidate();

        return $this->json(['success' => true, 'message' => 'Logged out successfully.']);
    }

    #[Route('/me', name: 'app_me', methods: ['GET'])]
    public function me(Security $security): JsonResponse
    {
        /** @var User $user */
        $user = $security->getUser();

        if (empty($user->getId())) {
            return $this->json([]);
        }

        return $this->json([
            'id'    => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
            'name'  => $user->getName(),
        ]);
    }
}
