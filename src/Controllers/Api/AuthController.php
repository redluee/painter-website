<?php
declare(strict_types=1);

namespace App\Controllers\Api;

use App\Services\AuthService;
use App\Services\DataService;
use App\Services\RateLimiter;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class AuthController
{
    public function login(Request $request, Response $response): Response
    {
        $adminEmail = $_ENV['ADMIN_EMAIL'] ?? '';
        $adminPasswordHash = $_ENV['ADMIN_PASSWORD_HASH'] ?? '';
        $jwtSecret = $_ENV['JWT_SECRET'] ?? '';

        if (!$adminEmail || !$adminPasswordHash || !$jwtSecret) {
            $response->getBody()->write(json_encode(['error' => 'Server configuratie fout']));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }

        $limiter = new RateLimiter();
        $ip = getClientIp($request, true);
        $check = $limiter->check($ip, 5, 15 * 60);

        if (!$check['allowed']) {
            $response->getBody()->write(json_encode(['error' => 'Te veel inlogpogingen. Probeer het later opnieuw.']));
            return $response
                ->withStatus(429)
                ->withHeader('Content-Type', 'application/json')
                ->withHeader('Retry-After', (string)$check['retryAfter']);
        }

        $body = $request->getParsedBody();
        $email = $body['email'] ?? '';
        $password = $body['password'] ?? '';

        if (!$email || !$password) {
            $response->getBody()->write(json_encode(['error' => 'E-mail en wachtwoord zijn verplicht']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        if ($email !== $adminEmail || !password_verify($password, $adminPasswordHash)) {
            $response->getBody()->write(json_encode(['error' => 'Ongeldige e-mail of wachtwoord']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        $limiter->clear($ip);

        $dataService = new DataService();
        $authService = new AuthService($dataService);
        $settings = $dataService->readThemeSettings();
        $token = $authService->createToken($email, $settings['tokenVersion']);

        $response->getBody()->write(json_encode(['success' => true]));
        $secureFlag = (!isset($_ENV['APP_ENV']) || $_ENV['APP_ENV'] !== 'development') ? '; Secure' : '';
        return $response
            ->withHeader('Set-Cookie', sprintf(
                'auth=%s; Path=/; HttpOnly; SameSite=Lax; Max-Age=%d%s',
                $token,
                60 * 60 * 8,
                $secureFlag
            ))
            ->withHeader('Content-Type', 'application/json');
    }

    public function logout(Request $request, Response $response): Response
    {
        $dataService = new DataService();
        $authService = new AuthService($dataService);
        $authService->invalidateTokens();

        $secureFlag = (!isset($_ENV['APP_ENV']) || $_ENV['APP_ENV'] !== 'development') ? '; Secure' : '';
        $response->getBody()->write(json_encode(['success' => true]));
        return $response
            ->withHeader('Set-Cookie', sprintf(
                'auth=; Path=/; Max-Age=0; HttpOnly; SameSite=Lax%s',
                $secureFlag
            ))
            ->withHeader('Content-Type', 'application/json');
    }
}
