<?php
declare(strict_types=1);

namespace App\Controllers\Api;

use App\Services\DataService;
use App\Services\ImageService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class ContentController
{
    public function get(Request $request, Response $response): Response
    {
        try {
            $data = new DataService();
            $content = $data->readSiteContent();
            $response->getBody()->write(json_encode($content));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Throwable) {
            $response->getBody()->write(json_encode(['error' => 'Inhoud kon niet worden geladen']));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function update(Request $request, Response $response): Response
    {
        try {
            $body = $request->getParsedBody();
            $businessInfo = $body['businessInfo'] ?? null;
            $aboutMe = $body['aboutMe'] ?? '';
            $tarievenContent = $body['tarievenContent'] ?? '';
            $partnersContent = $body['partnersContent'] ?? '';
            $profileImage = $body['profileImage'] ?? '';

            if (!$businessInfo || !trim($aboutMe) || !trim($tarievenContent) || !trim($partnersContent)) {
                $response->getBody()->write(json_encode(['error' => 'Alle velden zijn verplicht']));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $name = trim($businessInfo['name'] ?? '');
            $intro = trim($businessInfo['intro'] ?? '');
            $phone = trim($businessInfo['phone'] ?? '');
            $email = trim($businessInfo['email'] ?? '');
            $location = trim($businessInfo['location'] ?? '');
            $kvk = trim($businessInfo['kvk'] ?? '');

            if (!$name || !$intro || !$phone || !$email || !$location || !$kvk) {
                $response->getBody()->write(json_encode(['error' => 'Alle bedrijfsgegevens zijn verplicht']));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            if (mb_strlen($name) > 200 || mb_strlen($location) > 200 || mb_strlen($intro) > 1000) {
                $response->getBody()->write(json_encode(['error' => 'Een of meer velden zijn te lang']));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            if (mb_strlen($phone) > 20) {
                $response->getBody()->write(json_encode(['error' => 'Telefoonnummer is te lang']));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            if (mb_strlen($email) > 254 || !preg_match('/^[^\s@]+@[^\s@]+\.[^\s@]+$/', $email)) {
                $response->getBody()->write(json_encode(['error' => 'Ongeldig e-mailadres']));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            if (mb_strlen($kvk) > 10 || !preg_match('/^\d+$/', $kvk)) {
                $response->getBody()->write(json_encode(['error' => 'Ongeldig KvK-nummer']));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            if (mb_strlen($aboutMe) > 20000) {
                $response->getBody()->write(json_encode(['error' => '"Over mij" is te lang (max 20000 karakters)']));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            if (mb_strlen($tarievenContent) > 20000) {
                $response->getBody()->write(json_encode(['error' => '"Tarieven" is te lang (max 20000 karakters)']));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            if (mb_strlen($partnersContent) > 20000) {
                $response->getBody()->write(json_encode(['error' => '"Partners" is te lang (max 20000 karakters)']));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $data = new DataService();
            $oldContent = $data->readSiteContent();
            $newProfileImage = is_string($profileImage) && $profileImage ? $profileImage : '/sebastiaan-profiel.jpg';

            if (($oldContent['profileImage'] ?? '') && $oldContent['profileImage'] !== $newProfileImage) {
                $imgService = new ImageService();
                $imgService->deleteImage($oldContent['profileImage']);
            }

            $content = [
                'businessInfo' => [
                    'name' => $name,
                    'phone' => $phone,
                    'email' => $email,
                    'location' => $location,
                    'kvk' => $kvk,
                    'intro' => $intro,
                ],
                'aboutMe' => sanitizeRichText($aboutMe),
                'tarievenContent' => sanitizeRichText($tarievenContent),
                'partnersContent' => sanitizeRichText($partnersContent),
                'profileImage' => $newProfileImage,
            ];

            $data->writeSiteContent($content);
            $response->getBody()->write(json_encode(['success' => true]));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Throwable) {
            $response->getBody()->write(json_encode(['error' => 'Ongeldig verzoek']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
    }
}
