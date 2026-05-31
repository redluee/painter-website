<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class LoginController
{
    public function show(Request $request, Response $response): Response
    {
        $html = $this->renderLoginPage();
        $response->getBody()->write($html);
        return $response;
    }

    private function renderLoginPage(): string
    {
        ob_start();
        require dirname(__DIR__, 3) . '/templates/admin/login.php';
        return ob_get_clean();
    }
}
