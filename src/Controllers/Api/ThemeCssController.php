<?php
declare(strict_types=1);

namespace App\Controllers\Api;

use App\Services\DataService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class ThemeCssController
{
    public function serve(Request $request, Response $response): Response
    {
        $data = new DataService();
        $settings = $data->readThemeSettings();

        $css = sprintf(
            ':root {
  --theme-accent-1: %s;
  --theme-accent-2: %s;
  --theme-section-bg: %s;
  --theme-navbar-bg: %s;
}',
            $settings['accent1'],
            $settings['accent2'],
            $settings['sectionBg'],
            $settings['navbarBg']
        );

        $response->getBody()->write($css);
        return $response
            ->withHeader('Content-Type', 'text/css; charset=utf-8')
            ->withHeader('Cache-Control', 'no-cache, no-store, must-revalidate');
    }
}
