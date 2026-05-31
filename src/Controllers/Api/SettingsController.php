<?php
declare(strict_types=1);

namespace App\Controllers\Api;

use App\Services\DataService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class SettingsController
{
    public function get(Request $request, Response $response): Response
    {
        $data = new DataService();
        $settings = $data->readThemeSettings();
        $response->getBody()->write(json_encode($settings));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function update(Request $request, Response $response): Response
    {
        try {
            $body = $request->getParsedBody();
            $accent1 = $body['accent1'] ?? '';
            $accent2 = $body['accent2'] ?? '';
            $sectionBg = $body['sectionBg'] ?? '';
            $navbarBg = $body['navbarBg'] ?? '';

            if (!$accent1 || !$accent2 || !$sectionBg || !$navbarBg) {
                $response->getBody()->write(json_encode(['error' => 'Alle kleuren zijn verplicht.']));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $hexRegex = '/^#[0-9a-fA-F]{6}$/';
            $checks = ['accent1' => $accent1, 'accent2' => $accent2, 'sectionBg' => $sectionBg, 'navbarBg' => $navbarBg];

            foreach ($checks as $key => $value) {
                if (!preg_match($hexRegex, $value)) {
                    $response->getBody()->write(json_encode(['error' => "'{$key}' is geen geldige hex-kleur (bijv. #1C2B1E)."]));
                    return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
                }
            }

            $data = new DataService();
            $current = $data->readThemeSettings();

            $data->writeThemeSettings([
                ...$current,
                'accent1' => $accent1,
                'accent2' => $accent2,
                'sectionBg' => $sectionBg,
                'navbarBg' => $navbarBg,
            ]);

            $response->getBody()->write(json_encode(['success' => true]));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Throwable) {
            $response->getBody()->write(json_encode(['error' => 'Ongeldige JSON.']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
    }
}
