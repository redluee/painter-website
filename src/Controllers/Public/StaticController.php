<?php
declare(strict_types=1);

namespace App\Controllers\Public;

use App\Services\DataService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class StaticController
{
    public function privacy(Request $request, Response $response): Response
    {
        $title = 'Privacy beleid';
        $currentPath = '/privacy';
        ob_start();
        require dirname(__DIR__, 3) . '/templates/public/privacy.php';
        $content = ob_get_clean();
        ob_start();
        require dirname(__DIR__, 3) . '/templates/layouts/layout.php';
        $output = ob_get_clean();
        $response->getBody()->write($output);
        return $response;
    }

    public function voorwaarden(Request $request, Response $response): Response
    {
        $data = new DataService();
        $siteContent = $data->readSiteContent();
        $kvk = $siteContent['businessInfo']['kvk'] ?? '';

        $title = 'Algemene voorwaarden';
        $currentPath = '/algemene-voorwaarden';
        ob_start();
        require dirname(__DIR__, 3) . '/templates/public/voorwaarden.php';
        $content = ob_get_clean();
        ob_start();
        require dirname(__DIR__, 3) . '/templates/layouts/layout.php';
        $output = ob_get_clean();
        $response->getBody()->write($output);
        return $response;
    }

    public function notFound(Request $request, Response $response): Response
    {
        $title = '404';
        $currentPath = '/404';
        ob_start();
        require dirname(__DIR__, 3) . '/templates/public/404.php';
        $content = ob_get_clean();
        ob_start();
        require dirname(__DIR__, 3) . '/templates/layouts/layout.php';
        $output = ob_get_clean();
        $response->getBody()->write($output);
        return $response->withStatus(404);
    }
}
