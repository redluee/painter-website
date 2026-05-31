<?php
declare(strict_types=1);

// Built-in dev server: serve static files directly
if (php_sapi_name() === 'cli-server') {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $file = __DIR__ . $path;
    if (is_file($file)) {
        return false;
    }
}

use Slim\Factory\AppFactory;
use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/helpers.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

$app = AppFactory::create();

$app->setBasePath('');

$app->addBodyParsingMiddleware();
$displayErrors = ($_ENV['APP_ENV'] ?? 'production') === 'development';
$app->addErrorMiddleware($displayErrors, true, true);

// Security headers middleware (applied to all responses)
$app->add(function (Request $request, RequestHandler $handler): \Psr\Http\Message\ResponseInterface {
    $response = $handler->handle($request);

    $csp = "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; img-src 'self' data:; font-src 'self' https://fonts.gstatic.com; connect-src 'self'; frame-ancestors 'none'; base-uri 'self'; form-action 'self'; object-src 'none'";

    return $response
        ->withHeader('X-Content-Type-Options', 'nosniff')
        ->withHeader('X-Frame-Options', 'DENY')
        ->withHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
        ->withHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), interest-cohort=()')
        ->withHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload')
        ->withHeader('Content-Security-Policy', $csp);
});

// Auth middleware applied to admin routes
$app->add(new \App\Middleware\AuthMiddleware());

// Routes
require __DIR__ . '/../src/routes.php';

$app->run();
