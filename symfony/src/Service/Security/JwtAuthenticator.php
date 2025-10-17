<?php

namespace App\Service\Security;

use App\Service\Auth\JwtService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

class JwtAuthenticator extends AbstractAuthenticator
{
    public function __construct(private readonly JwtService $jwtService, private readonly UserRepository $users) {}

    public function supports(Request $request): ?bool
    {
        if($request->headers->has('Authorization') &&
            str_starts_with($request->headers->get('Authorization'), 'Bearer ')
        ) {
            return true;
        }

        throw new AuthenticationException('No Authorization header provided');
    }

    public function authenticate(Request $request): Passport
    {
        $authHeader = $request->headers->get('Authorization', '');
        if (empty($authHeader)) {
            throw new AuthenticationException('No Authorization header provided');
        }

        // Remove "Bearer" prefix
        $jwt = preg_replace('/^Bearer\s*/i', '', $authHeader);

        if (!$jwt) {
            throw new AuthenticationException('Invalid Authorization header format');
        }

        try {
            $data = $this->jwtService->decodeToken($jwt);
        } catch (\Exception $e) {
            throw new AuthenticationException('Invalid or expired token');
        }

        $userId = isset($data['sub']) ? (int)$data['sub'] : null;

        if (!$userId) {
            throw new AuthenticationException('Invalid token payload');
        }

        return new SelfValidatingPassport(
            new UserBadge($userId, function ($id) {
                return $this->users->find($id);
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, $token, string $firewallName): ?Response
    {
        return null; // continue request
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse(['error'=>$exception->getMessageKey()], Response::HTTP_UNAUTHORIZED);
    }
}
