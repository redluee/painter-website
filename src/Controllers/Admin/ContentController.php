<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class ContentController
{
    public function show(Request $request, Response $response): Response
    {
        $title = 'Inhoud';
        ob_start();
        require dirname(__DIR__, 3) . '/templates/admin/content.php';
        $content = ob_get_clean();
        ob_start();
        require dirname(__DIR__, 3) . '/templates/layouts/admin-layout.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response;
    }
}
