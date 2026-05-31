<?php
declare(strict_types=1);

namespace App\Controllers\Public;

use App\Services\DataService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class ContactController
{
    public function show(Request $request, Response $response): Response
    {
        $data = new DataService();
        $content = $data->readSiteContent();
        $businessInfo = $content['businessInfo'];

        $title = 'Contact';
        $currentPath = '/contact';

        ob_start();
        require dirname(__DIR__, 3) . '/templates/public/contact.php';
        $content = ob_get_clean();
        ob_start();
        require dirname(__DIR__, 3) . '/templates/layouts/layout.php';
        $output = ob_get_clean();
        $response->getBody()->write($output);
        return $response;
    }
}
