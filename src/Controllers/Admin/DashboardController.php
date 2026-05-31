<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class DashboardController
{
    public function show(Request $request, Response $response): Response
    {
        $title = 'Dashboard';
        $currentPath = $request->getUri()->getPath();
        ob_start();
        require dirname(__DIR__, 3) . '/templates/admin/dashboard.php';
        $content = ob_get_clean();
        $html = $this->renderAdminLayout($title, $content);
        $response->getBody()->write($html);
        return $response;
    }

    private function renderAdminLayout(string $title, string $content): string
    {
        ob_start();
        require dirname(__DIR__, 3) . '/templates/layouts/admin-layout.php';
        return ob_get_clean();
    }
}
