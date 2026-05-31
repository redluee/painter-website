<?php
declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;

use App\Controllers\Api as Api;
use App\Controllers\Admin as Admin;
use App\Controllers\Public as Pub;

// ── Health / monitoring ──
$app->get('/health', function (Request $request, Response $response): Response {
    $response->getBody()->write(json_encode(['status' => 'ok', 'timestamp' => date('c')]));
    return $response->withHeader('Content-Type', 'application/json');
});

// ── Public pages ──
$app->get('/', [Pub\HomeController::class, 'show']);
$app->get('/projecten', [Pub\ProjectsController::class, 'index']);
$app->get('/projecten/{slug}', [Pub\ProjectsController::class, 'detail']);
$app->get('/over-mij', [Pub\AboutController::class, 'show']);
$app->get('/tarieven', [Pub\TarievenController::class, 'show']);
$app->get('/partners', [Pub\PartnersController::class, 'show']);
$app->get('/contact', [Pub\ContactController::class, 'show']);
$app->get('/privacy', [Pub\StaticController::class, 'privacy']);
$app->get('/algemene-voorwaarden', [Pub\StaticController::class, 'voorwaarden']);
$app->get('/404', [Pub\StaticController::class, 'notFound']);
$app->get('/sitemap.xml', [Pub\SitemapController::class, 'generate']);

// ── API routes (public) ──
$app->post('/api/contact', [Api\ContactController::class, 'submit']);
$app->get('/api/theme.css', [Api\ThemeCssController::class, 'serve']);
$app->post('/api/auth/login', [Api\AuthController::class, 'login']);
$app->post('/api/auth/logout', [Api\AuthController::class, 'logout']);

// ── Admin API (behind auth middleware) ──
$app->group('/api/admin', function (RouteCollectorProxy $group) {
    $group->get('/content', [Api\ContentController::class, 'get']);
    $group->post('/content', [Api\ContentController::class, 'update']);
    $group->get('/settings', [Api\SettingsController::class, 'get']);
    $group->post('/settings', [Api\SettingsController::class, 'update']);
    $group->post('/upload', [Api\UploadController::class, 'upload']);
    $group->get('/projects', [Api\ProjectsController::class, 'list']);
    $group->post('/projects', [Api\ProjectsController::class, 'create']);
    $group->get('/projects/{slug}', [Api\ProjectsController::class, 'get']);
    $group->put('/projects/{slug}', [Api\ProjectsController::class, 'update']);
    $group->delete('/projects/{slug}', [Api\ProjectsController::class, 'delete']);
});

// ── Admin pages (behind auth middleware) ──
$app->group('/admin', function (RouteCollectorProxy $group) {
    $group->get('', [Admin\DashboardController::class, 'show']);
    $group->get('/dashboard', [Admin\DashboardController::class, 'show']);
    $group->get('/content', [Admin\ContentController::class, 'show']);
    $group->get('/instellingen', [Admin\SettingsController::class, 'show']);
    $group->get('/projects', [Admin\ProjectsController::class, 'index']);
    $group->get('/projects/new', [Admin\ProjectsController::class, 'new']);
    $group->get('/projects/edit/{slug}', [Admin\ProjectsController::class, 'edit']);
    $group->get('/login', [Admin\LoginController::class, 'show']);
});
