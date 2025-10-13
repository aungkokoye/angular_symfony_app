<?php

namespace App\Service\Security;

use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\HttpFoundation\Request;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    public function onAuthenticationSuccess(Request $request, TokenInterface $token): JsonResponse
    {
        /** @var User $user */
        $user = $token->getUser();

        return new JsonResponse([
            'success'   => true,
            'user'      => [
                                'id'    => $user->getId(),
                                'email' => $user->getEmail(),
                                'roles' => $user->getRoles(),
                                'name'  => $user->getName(),
                            ]
        ]);
    }
}
