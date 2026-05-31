<?php
declare(strict_types=1);

namespace App\Controllers\Api;

use App\Services\DataService;
use App\Services\EmailService;
use App\Services\RateLimiter;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class ContactController
{
    public function submit(Request $request, Response $response): Response
    {
        $limiter = new RateLimiter();
        $ip = getClientIp($request, true);
        $check = $limiter->check($ip, 3, 60);

        if (!$check['allowed']) {
            $response->getBody()->write(json_encode(['error' => 'Te veel verzoeken. Probeer het later opnieuw.']));
            return $response
                ->withStatus(429)
                ->withHeader('Content-Type', 'application/json')
                ->withHeader('Retry-After', (string)$check['retryAfter']);
        }

        try {
            $body = $request->getParsedBody();
            $name = trim($body['name'] ?? '');
            $email = trim($body['email'] ?? '');
            $subject = $body['subject'] ?? 'offerte';
            $message = trim($body['message'] ?? '');

            if (!$name) {
                $response->getBody()->write(json_encode(['error' => 'Naam is verplicht']));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            if (!$email || !preg_match('/^[^\s@]+@[^\s@]+\.[^\s@]+$/', $email)) {
                $response->getBody()->write(json_encode(['error' => 'Ongeldig e-mailadres']));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            if (!$message) {
                $response->getBody()->write(json_encode(['error' => 'Bericht is verplicht']));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            if (mb_strlen($name) > 200 || mb_strlen($email) > 254 || mb_strlen($message) > 10000) {
                $response->getBody()->write(json_encode(['error' => 'Een of meer velden zijn te lang']));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $submission = [
                'name' => $name,
                'email' => $email,
                'subject' => $subject,
                'message' => $message,
                'createdAt' => date('c'),
            ];

            $data = new DataService();
            $data->appendContactSubmission($submission);

            try {
                $emailService = new EmailService();
                $emailService->sendContactNotification($submission);
            } catch (\Throwable) {
                // best-effort
            }

            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Bedankt! Uw bericht is ontvangen.',
            ]));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Throwable) {
            $response->getBody()->write(json_encode(['error' => 'Ongeldig verzoek']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
    }
}
