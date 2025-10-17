<?php

namespace App\Controller;

use App\Entity\JwtRefreshToken;
use App\Entity\User;
use App\Repository\JwtRefreshTokenRepository;
use App\Repository\UserRepository;
use App\Service\Auth\JwtService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;


/*
 * Prefix all routes with /api
 */
class ApiAuthController extends AbstractController
{
    public function __construct(
        private readonly UserRepository              $users,
        private readonly UserPasswordHasherInterface $hasher,
        private readonly JwtService                  $jwtService,
        private readonly EntityManagerInterface      $em,
        private readonly JwtRefreshTokenRepository   $repo
    ) {}

    #[Route('/jwt-login', name:'api_jwt_login', methods:['POST'])]
    public function login(Request $req): JsonResponse
    {
        $data = json_decode($req->getContent(), true);
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        $user = $this->users->findOneBy(['email' => $email]);
        if (!$user || !$this->hasher->isPasswordValid($user, $password)) {
            return new JsonResponse(['error'=>'Invalid credentials'], 401);
        }

        // create JWT
        $jwt = $this->generateJwtAccessToken($user);

        // create refresh token
        $refresh = new JwtRefreshToken();
        $refresh->setUser($user);
        $refresh->setRevoked(false);
        $refresh->setToken(Uuid::v4()->toRfc4122()); // or random_bytes + bin2hex
        $this->em->persist($refresh);
        $this->em->flush();

        return new JsonResponse([
            'access_token' => $jwt,
            'refresh_token' => $refresh->getToken(),
        ]);
    }

    #[Route('/jwt-refresh', name:'api_jwt_refresh', methods:['POST'])]
    public function refresh(Request $req): JsonResponse
    {
        $data = json_decode($req->getContent(), true);
        $refreshTokenValue = $data['refresh_token'] ?? null;
        if (!$refreshTokenValue) {
            return new JsonResponse(['error' => 'Missing refresh_token'], Response::HTTP_BAD_REQUEST);
        }

        $token = $this->repo->findOneBy(['token'=>$refreshTokenValue]);
        if (!$token || $token->isRevoked() || $token->getExpiresAt() < new \DateTimeImmutable()) {
            return new JsonResponse(['error'=>'Invalid refresh token'], Response::HTTP_UNAUTHORIZED);
        }

        $user = $token->getUser();
        $token->setRevoked(true);
        $this->em->persist($token);

        $newJwt = $this->generateJwtAccessToken($user);

        // create rotated refresh token
        $newRt = new JwtRefreshToken();
        $newRt->setUser($user);
        $newRt->setRevoked(false);
        $newRt->setToken(Uuid::v4()->toRfc4122());
        $this->em->persist($newRt);

        $this->em->flush();

        return new JsonResponse([
            'access_token' => $newJwt,
            'refresh_token' => $newRt->getToken(),
        ]);
    }

    private function generateJwtAccessToken(User $user): string
    {
        return $this->jwtService->generateToken([
            'sub' => (string)$user->getId(),
            'roles' => $user->getRoles(),
            'user_email' => $user->getEmail(),
        ]);
    }
}
