<?php
declare(strict_types=1);

namespace App\Controllers\Public;

use App\Services\DataService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class ProjectsController
{
    public const DEFAULT_IMAGE = '/images/default-image.webp';

    public function index(Request $request, Response $response): Response
    {
        $data = new DataService();
        $projects = array_reverse($data->readProjects());
        $firstProject = $projects[0] ?? null;
        $restProjects = array_slice($projects, 1);

        $title = 'Projecten';
        $description = 'Overzicht van mijn recente schilderwerk.';
        $currentPath = '/projecten';

        ob_start();
        require dirname(__DIR__, 3) . '/templates/public/projecten.php';
        $content = ob_get_clean();
        ob_start();
        require dirname(__DIR__, 3) . '/templates/layouts/layout.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response;
    }

    public function detail(Request $request, Response $response, array $args): Response
    {
        $slug = $args['slug'] ?? '';
        $data = new DataService();
        $projects = $data->readProjects();
        $project = null;
        foreach ($projects as $p) {
            if (slugify($p['name']) === $slug) {
                $p['slug'] = $slug;
                $project = $p;
                break;
            }
        }

        if (!$project) {
            return $response->withHeader('Location', '/404')->withStatus(302);
        }

        $title = $project['name'];
        $description = strip_tags(substr($project['description'] ?? '', 0, 160));
        $currentPath = '/projecten/' . $slug;

        ob_start();
        require dirname(__DIR__, 3) . '/templates/public/project-detail.php';
        $content = ob_get_clean();
        ob_start();
        require dirname(__DIR__, 3) . '/templates/layouts/layout.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response;
    }
}
