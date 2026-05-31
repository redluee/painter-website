<?php
declare(strict_types=1);

function slugify(string $name): string {
    $slug = mb_strtolower($name, 'UTF-8');
    $slug = preg_replace('/[^\w\s-]/u', '', $slug);
    $slug = preg_replace('/\s+/', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    return trim($slug, '-');
}

function sanitizeRichText(string $html): string {
    $allowedTags = '<p><br><strong><b><em><i><u><s><sub><sup><a><ul><ol><li>';
    return strip_tags($html, $allowedTags);
}

function getClientIp(Psr\Http\Message\ServerRequestInterface $request): string {
    $serverParams = $request->getServerParams();
    $headers = $request->getHeaders();

    if (isset($headers['X-Forwarded-For'][0])) {
        return trim(explode(',', $headers['X-Forwarded-For'][0])[0]);
    }
    if (isset($headers['X-Real-Ip'][0])) {
        return $headers['X-Real-Ip'][0];
    }
    return $serverParams['REMOTE_ADDR'] ?? 'unknown';
}

function escapeHtml(string $text): string {
    return htmlspecialchars($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}
