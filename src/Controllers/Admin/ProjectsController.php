<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class ProjectsController
{
    public function index(Request $request, Response $response): Response
    {
        $title = 'Projecten';
        $currentPath = $request->getUri()->getPath();
        ob_start();
        require dirname(__DIR__, 3) . '/templates/admin/projects/index.php';
        $content = ob_get_clean();
        ob_start();
        require dirname(__DIR__, 3) . '/templates/layouts/admin-layout.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response;
    }

    public function new(Request $request, Response $response): Response
    {
        $title = 'Nieuw project';
        $currentPath = $request->getUri()->getPath();
        ob_start();
        require dirname(__DIR__, 3) . '/templates/admin/projects/new.php';
        $content = ob_get_clean();
        ob_start();
        require dirname(__DIR__, 3) . '/templates/layouts/admin-layout.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response;
    }

    public function edit(Request $request, Response $response, array $args): Response
    {
        $title = 'Project bewerken';
        $slug = $args['slug'] ?? '';
        $currentPath = $request->getUri()->getPath();
        ob_start();
        require dirname(__DIR__, 3) . '/templates/admin/projects/edit.php';
        $content = ob_get_clean();
        ob_start();
        require dirname(__DIR__, 3) . '/templates/layouts/admin-layout.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response;
    }
}
