<?php

namespace App\Service\Auth;


use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use DateTimeImmutable;

class JwtService
{
    const HASH_ALGORITHM= 'HS256';
    public function __construct(private readonly ParameterBagInterface $params) {}

    public function generateToken(array $payload): string
    {
        $now = new DateTimeImmutable();
        $ttl = (int)$this->params->get('app.jwt_token_ttl') ?: 900;
        $secret = $this->params->get('app.jwt_secret_key');

        $claims = array_merge([
            'iat' => $now->getTimestamp(),
            'nbf' => $now->getTimestamp(),
            'exp' => $now->getTimestamp() + $ttl,
        ], $payload);

        return JWT::encode($claims, $secret, self::HASH_ALGORITHM);
    }

    public function decodeToken(string $jwt): array
    {
        $secret = $this->params->get('app.jwt_secret_key');
        // throws exceptions on invalid/expired tokens
        $decoded = JWT::decode($jwt, new Key($secret, self::HASH_ALGORITHM));
        return (array) $decoded;
    }
}
