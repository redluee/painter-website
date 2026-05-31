<?php
declare(strict_types=1);

namespace App\Controllers\Public;

use App\Services\DataService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class SitemapController
{
    public function generate(Request $request, Response $response): Response
    {
        $siteUrl = 'https://sebastiaanpeters.nl';
        $staticPages = ['', 'projecten', 'over-mij', 'tarieven', 'partners', 'contact', 'privacy', 'algemene-voorwaarden'];

        $data = new DataService();
        $projects = $data->readProjects();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($staticPages as $page) {
            $xml .= '  <url><loc>' . $siteUrl . '/' . $page . '</loc></url>' . "\n";
        }

        foreach ($projects as $p) {
            $xml .= '  <url><loc>' . $siteUrl . '/projecten/' . slugify($p['name']) . '</loc></url>' . "\n";
        }

        $xml .= '</urlset>';

        $response->getBody()->write($xml);
        return $response->withHeader('Content-Type', 'application/xml; charset=utf-8');
    }
}
