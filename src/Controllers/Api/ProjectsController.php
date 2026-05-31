<?php
declare(strict_types=1);

namespace App\Controllers\Api;

use App\Services\DataService;
use App\Services\ImageService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class ProjectsController
{
    public function list(Request $request, Response $response): Response
    {
        try {
            $data = new DataService();
            $projects = $data->readProjects();
            $response->getBody()->write(json_encode(array_reverse($projects)));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Throwable) {
            $response->getBody()->write(json_encode(['error' => 'Projecten konden niet worden geladen']));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function create(Request $request, Response $response): Response
    {
        try {
            $body = $request->getParsedBody();
            $name = trim($body['name'] ?? '');
            $description = $body['description'] ?? '';
            $paintType = $body['paintType'] ?? [];
            $pictures = $body['pictures'] ?? [];
            $review = $body['review'] ?? null;
            $highlighted = $body['highlighted'] ?? false;

            if (!$name || !$description) {
                $response->getBody()->write(json_encode(['error' => 'Naam en beschrijving zijn verplicht']));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            if (mb_strlen($name) > 200) {
                $response->getBody()->write(json_encode(['error' => 'Projectnaam is te lang (max 200 karakters)']));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            if (mb_strlen($description) > 20000) {
                $response->getBody()->write(json_encode(['error' => 'Beschrijving is te lang (max 20000 karakters)']));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $data = new DataService();
            $projects = $data->readProjects();
            $slug = slugify($name);

            $existingSlugs = array_map(function ($p) {
                return slugify($p['name']);
            }, $projects);

            if (in_array($slug, $existingSlugs, true)) {
                $response->getBody()->write(json_encode(['error' => 'Er bestaat al een project met deze naam']));
                return $response->withStatus(409)->withHeader('Content-Type', 'application/json');
            }

            $isHighlighted = $highlighted === true;
            if ($isHighlighted) {
                foreach ($projects as &$p) {
                    $p['highlighted'] = false;
                }
                unset($p);
            }

            $project = [
                'name' => $name,
                'paintType' => is_array($paintType) ? $paintType : [],
                'description' => sanitizeRichText($description),
                'pictures' => is_array($pictures) ? $pictures : [],
                'highlighted' => $isHighlighted ?: null,
            ];

            if ($review && isset($review['stars']) && is_numeric($review['stars']) && $review['stars'] >= 1 && $review['stars'] <= 5) {
                $project['review'] = [
                    'stars' => (float)$review['stars'],
                    'description' => (string)($review['description'] ?? ''),
                ];
            }

            $projects[] = $project;
            $data->writeProjects($projects);

            $project['slug'] = $slug;
            $response->getBody()->write(json_encode($project));
            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
        } catch (\Throwable) {
            $response->getBody()->write(json_encode(['error' => 'Ongeldig verzoek']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
    }

    public function get(Request $request, Response $response, array $args): Response
    {
        try {
            $data = new DataService();
            $projects = $data->readProjects();
            $slug = $args['slug'] ?? '';

            foreach ($projects as $p) {
                if (slugify($p['name']) === $slug) {
                    $p['slug'] = $slug;
                    $response->getBody()->write(json_encode($p));
                    return $response->withHeader('Content-Type', 'application/json');
                }
            }

            $response->getBody()->write(json_encode(['error' => 'Project niet gevonden']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        } catch (\Throwable) {
            $response->getBody()->write(json_encode(['error' => 'Project kon niet worden geladen']));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        try {
            $body = $request->getParsedBody();
            $data = new DataService();
            $projects = $data->readProjects();
            $slug = $args['slug'] ?? '';

            $index = null;
            foreach ($projects as $i => $p) {
                if (slugify($p['name']) === $slug) {
                    $index = $i;
                    break;
                }
            }

            if ($index === null) {
                $response->getBody()->write(json_encode(['error' => 'Project niet gevonden']));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }

            $name = $body['name'] ?? null;
            $description = $body['description'] ?? null;
            $highlighted = $body['highlighted'] ?? null;

            // Toggle highlight only
            if ($highlighted !== null && !$name) {
                $projects[$index]['highlighted'] = $highlighted ?: null;
                if ($highlighted) {
                    foreach ($projects as $i => &$p) {
                        if ($i !== $index) $p['highlighted'] = false;
                    }
                    unset($p);
                }
                $data->writeProjects($projects);
                $projects[$index]['slug'] = $slug;
                $response->getBody()->write(json_encode($projects[$index]));
                return $response->withHeader('Content-Type', 'application/json');
            }

            // Full update
            if (!$name || !$description) {
                $response->getBody()->write(json_encode(['error' => 'Naam en beschrijving zijn verplicht']));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            if (mb_strlen($name) > 200) {
                $response->getBody()->write(json_encode(['error' => 'Projectnaam is te lang (max 200 karakters)']));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            if (mb_strlen($description) > 20000) {
                $response->getBody()->write(json_encode(['error' => 'Beschrijving is te lang (max 20000 karakters)']));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $newSlug = slugify($name);
            foreach ($projects as $i => $p) {
                if ($i !== $index && slugify($p['name']) === $newSlug) {
                    $response->getBody()->write(json_encode(['error' => 'Er bestaat al een project met deze naam']));
                    return $response->withStatus(409)->withHeader('Content-Type', 'application/json');
                }
            }

            $newHighlighted = $projects[$index]['highlighted'] ?? false;
            if ($highlighted === true) {
                $newHighlighted = true;
                foreach ($projects as $i => &$p) {
                    if ($i !== $index) $p['highlighted'] = false;
                }
                unset($p);
            } elseif ($highlighted === false) {
                $newHighlighted = false;
            }

            $oldPictures = $projects[$index]['pictures'] ?? [];
            $newPictures = is_array($body['pictures'] ?? null) ? $body['pictures'] : [];

            // Clean up removed images
            $imgService = new ImageService();
            foreach ($oldPictures as $url) {
                if (!in_array($url, $newPictures, true)) {
                    $imgService->deleteImage($url);
                }
            }

            $paintType = $body['paintType'] ?? [];
            $review = $body['review'] ?? null;

            $projects[$index] = [
                'name' => $name,
                'paintType' => is_array($paintType) ? $paintType : [],
                'description' => sanitizeRichText($description),
                'pictures' => $newPictures,
                'highlighted' => $newHighlighted ?: null,
            ];

            if ($review && isset($review['stars']) && is_numeric($review['stars']) && $review['stars'] >= 1 && $review['stars'] <= 5) {
                $projects[$index]['review'] = [
                    'stars' => (float)$review['stars'],
                    'description' => (string)($review['description'] ?? ''),
                ];
            } else {
                unset($projects[$index]['review']);
            }

            $data->writeProjects($projects);
            $projects[$index]['slug'] = $newSlug;

            $response->getBody()->write(json_encode($projects[$index]));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Throwable) {
            $response->getBody()->write(json_encode(['error' => 'Ongeldig verzoek']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        $data = new DataService();
        $projects = $data->readProjects();
        $slug = $args['slug'] ?? '';

        $index = null;
        foreach ($projects as $i => $p) {
            if (slugify($p['name']) === $slug) {
                $index = $i;
                break;
            }
        }

        if ($index === null) {
            $response->getBody()->write(json_encode(['error' => 'Project niet gevonden']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        $imgService = new ImageService();
        foreach (($projects[$index]['pictures'] ?? []) as $url) {
            $imgService->deleteImage($url);
        }

        array_splice($projects, $index, 1);
        $data->writeProjects($projects);

        $response->getBody()->write(json_encode(['success' => true]));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
