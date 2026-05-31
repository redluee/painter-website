<?php
declare(strict_types=1);

namespace App\Controllers\Public;

use App\Services\DataService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class HomeController
{
    public function show(Request $request, Response $response): Response
    {
        $data = new DataService();
        $siteContent = $data->readSiteContent();
        $projects = $data->readProjects();
        $projects = array_reverse($projects);
        $heroProject = null;
        foreach ($projects as $p) {
            if (!empty($p['highlighted'])) { $heroProject = $p; break; }
        }
        if (!$heroProject && isset($projects[0])) $heroProject = $projects[0];
        $recentProjects = array_slice($projects, 0, 3);
        $hasProjects = count($projects) > 0;

        $businessInfo = $siteContent['businessInfo'];
        $DEFAULT_IMAGE = '/images/default-image.webp';

        ob_start();
        require dirname(__DIR__, 3) . '/templates/public/home.php';
        $content = ob_get_clean();
        ob_start();
        require dirname(__DIR__, 3) . '/templates/layouts/layout.php';
        $output = ob_get_clean();
        $response->getBody()->write($output);
        return $response;
    }
}
