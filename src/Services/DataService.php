<?php
declare(strict_types=1);

namespace App\Services;

final class DataService
{
    private string $dataDir;

    public function __construct()
    {
        $this->dataDir = dirname(__DIR__, 2) . '/data';
    }

    public function readProjects(): array
    {
        try {
            $raw = file_get_contents($this->dataDir . '/projects.json');
            $items = json_decode($raw, true) ?? [];
        } catch (\Throwable) {
            $items = [];
        }
        return array_map(function ($p) {
            $p['slug'] = slugify($p['name']);
            return $p;
        }, $items);
    }

    public function writeProjects(array $projects): void
    {
        $raw = array_map(function ($p) {
            unset($p['slug']);
            return $p;
        }, $projects);
        $tmpPath = $this->dataDir . '/projects.json.tmp';
        $targetPath = $this->dataDir . '/projects.json';
        file_put_contents($tmpPath, json_encode(array_values($raw), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        rename($tmpPath, $targetPath);
    }

    public function readSiteContent(): array
    {
        try {
            $raw = file_get_contents($this->dataDir . '/content.json');
            return json_decode($raw, true) ?? [];
        } catch (\Throwable) {
            return [];
        }
    }

    public function writeSiteContent(array $content): void
    {
        $tmpPath = $this->dataDir . '/content.json.tmp';
        $targetPath = $this->dataDir . '/content.json';
        file_put_contents($tmpPath, json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        rename($tmpPath, $targetPath);
    }

    public function readThemeSettings(): array
    {
        $defaults = [
            'accent1' => '#1C2B1E',
            'accent2' => '#2a3d2c',
            'sectionBg' => '#F0F5EE',
            'navbarBg' => '#F0F5EE',
            'tokenVersion' => 1,
        ];
        try {
            $raw = file_get_contents($this->dataDir . '/settings.json');
            $data = json_decode($raw, true) ?? [];
            return array_merge($defaults, $data);
        } catch (\Throwable) {
            return $defaults;
        }
    }

    public function writeThemeSettings(array $settings): void
    {
        $tmpPath = $this->dataDir . '/settings.json.tmp';
        $targetPath = $this->dataDir . '/settings.json';
        file_put_contents($tmpPath, json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        rename($tmpPath, $targetPath);
    }

    public function appendContactSubmission(array $submission): void
    {
        $filePath = $this->dataDir . '/contact-submissions.json';
        $submissions = [];
        if (file_exists($filePath)) {
            $raw = file_get_contents($filePath);
            $submissions = json_decode($raw, true) ?? [];
        }
        $submissions[] = $submission;
        $tmpPath = $filePath . '.tmp';
        file_put_contents($tmpPath, json_encode($submissions, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        rename($tmpPath, $filePath);
    }
}
