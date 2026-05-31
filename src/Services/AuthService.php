<?php
declare(strict_types=1);

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

final class AuthService
{
    private string $secret;
    private DataService $data;

    public function __construct(DataService $data)
    {
        $secret = $_ENV['JWT_SECRET'] ?? '';
        if (strlen($secret) < 32) {
            throw new \RuntimeException('JWT_SECRET must be at least 32 characters');
        }
        $this->secret = $secret;
        $this->data = $data;
    }

    public function createToken(string $email, int $tokenVersion): string
    {
        $payload = [
            'email' => $email,
            'tokenVersion' => $tokenVersion,
            'iat' => time(),
            'exp' => time() + (60 * 60 * 8),
        ];
        return JWT::encode($payload, $this->secret, 'HS256');
    }

    public function verifyToken(string $token): ?object
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secret, 'HS256'));
            $settings = $this->data->readThemeSettings();
            if (($decoded->tokenVersion ?? null) !== $settings['tokenVersion']) {
                return null;
            }
            return $decoded;
        } catch (\Throwable) {
            return null;
        }
    }

    public function invalidateTokens(): void
    {
        $settings = $this->data->readThemeSettings();
        $settings['tokenVersion']++;
        $this->data->writeThemeSettings($settings);
    }
}
