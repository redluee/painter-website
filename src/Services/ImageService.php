<?php
declare(strict_types=1);

namespace App\Services;

use Intervention\Image\ImageManager;

final class ImageService
{
    private string $imagesDir;
    private ?ImageManager $manager = null;

    public function __construct()
    {
        $this->imagesDir = dirname(__DIR__, 2) . '/public/images';
    }

    private function getManager(): ImageManager
    {
        if ($this->manager === null) {
            $this->manager = ImageManager::gd();
        }
        return $this->manager;
    }

    public function saveImage(string $filePath, string $originalName): string
    {
        if (!is_dir($this->imagesDir)) {
            mkdir($this->imagesDir, 0755, true);
        }

        $ext = pathinfo($originalName, PATHINFO_EXTENSION);
        $name = pathinfo($originalName, PATHINFO_FILENAME);
        $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', substr($name, 0, 50));
        $unique = $safeName . '-' . time() . '.webp';
        $outputPath = $this->imagesDir . '/' . $unique;

        $manager = $this->getManager();
        $image = $manager->read($filePath);
        $image->scaleDown(width: 1920);
        $image->toWebp(80)->save($outputPath);

        return '/images/' . $unique;
    }

    public function deleteImage(string $url): void
    {
        if (!str_starts_with($url, '/images/')) return;
        $filename = basename($url);
        $filePath = $this->imagesDir . '/' . $filename;
        if (file_exists($filePath)) {
            @unlink($filePath);
        }
    }
}
