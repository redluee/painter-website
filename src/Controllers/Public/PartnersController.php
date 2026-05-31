<?php
declare(strict_types=1);

namespace App\Controllers\Public;

use App\Services\DataService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class PartnersController
{
    public function show(Request $request, Response $response): Response
    {
        $data = new DataService();
        $content = $data->readSiteContent();
        $partnersContent = $content['partnersContent'] ?? '';
        $businessInfo = $content['businessInfo'];

        $title = 'Partners';
        $currentPath = '/partners';

        ob_start();
        require dirname(__DIR__, 3) . '/templates/public/partners.php';
        $content = ob_get_clean();
        ob_start();
        require dirname(__DIR__, 3) . '/templates/layouts/layout.php';
        $output = ob_get_clean();
        $response->getBody()->write($output);
        return $response;
    }
}
