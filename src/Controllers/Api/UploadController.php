<?php
declare(strict_types=1);

namespace App\Controllers\Api;

use App\Services\ImageService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class UploadController
{
    private const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/webp', 'image/avif', 'image/heic', 'image/heif'];
    private const MAX_SIZE = 10 * 1024 * 1024; // 10 MB

    public function upload(Request $request, Response $response): Response
    {
        try {
            $uploadedFiles = $request->getUploadedFiles();
            $file = $uploadedFiles['image'] ?? null;

            if (!$file || $file->getError() !== UPLOAD_ERR_OK) {
                $response->getBody()->write(json_encode(['error' => 'Geen afbeelding geüpload']));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $mimeType = $file->getClientMediaType();
            if (!in_array($mimeType, self::ALLOWED_TYPES, true)) {
                $response->getBody()->write(json_encode(['error' => 'Ongeldig bestandstype.']));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $size = $file->getSize();
            if ($size > self::MAX_SIZE) {
                $response->getBody()->write(json_encode(['error' => 'Bestand is te groot (max 10 MB).']));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $tempPath = $file->getStream()->getMetadata('uri') ?? $file->getFilePath();

            // Server-side MIME validation
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $detectedMime = $finfo->file($tempPath);
            if (!in_array($detectedMime, self::ALLOWED_TYPES, true)) {
                $response->getBody()->write(json_encode(['error' => 'Ongeldig bestandstype.']));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $imgService = new ImageService();
            $url = $imgService->saveImage($tempPath, $file->getClientFilename());

            $response->getBody()->write(json_encode(['url' => $url]));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Throwable $e) {
            error_log('Upload handler error: ' . $e->getMessage());
            $response->getBody()->write(json_encode([
                'error' => 'Upload mislukt. Probeer het opnieuw.',
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
}
