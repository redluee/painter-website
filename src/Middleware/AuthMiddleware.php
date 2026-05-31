<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Services\AuthService;
use App\Services\DataService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;

final class AuthMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $path = $request->getUri()->getPath();

        $protectedPaths = ['/admin', '/api/admin'];
        $loginPath = '/admin/login';

        $isProtected = false;
        foreach ($protectedPaths as $p) {
            if (str_starts_with($path, $p)) {
                $isProtected = true;
                break;
            }
        }

        if ($isProtected && $path !== $loginPath) {
            // CSRF check for state-changing admin API requests
            if (str_starts_with($path, '/api/admin') && !in_array($request->getMethod(), ['GET', 'HEAD'])) {
                $origin = $request->getHeaderLine('Origin');
                $host = $request->getHeaderLine('Host');
                if ($origin && $host) {
                    $originParts = parse_url($origin);
                    $originHost = $originParts['host'] ?? '';
                    $requestHost = explode(':', $host)[0];
                    if ($originHost !== $requestHost) {
                        $response = new Response();
                        $response->getBody()->write(json_encode(['error' => 'Ongeldige aanvraag']));
                        return $response->withStatus(403)->withHeader('Content-Type', 'application/json');
                    }
                }
            }

            $cookies = $request->getCookieParams();
            $token = $cookies['auth'] ?? null;

            if (!$token) {
                return $this->redirect($loginPath);
            }

            $dataService = new DataService();
            $authService = new AuthService($dataService);
            $decoded = $authService->verifyToken($token);

            if (!$decoded) {
                $response = new Response();
                return $response
                    ->withHeader('Set-Cookie', 'auth=; Path=/; Max-Age=0; HttpOnly; SameSite=Lax')
                    ->withHeader('Location', $loginPath)
                    ->withStatus(302);
            }
        }

        return $handler->handle($request);
    }

    private function redirect(string $path): ResponseInterface
    {
        $response = new Response();
        return $response
            ->withHeader('Location', $path)
            ->withStatus(302);
    }
}
