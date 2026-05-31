<?php
declare(strict_types=1);

namespace App\Services;

final class RateLimiter
{
    private string $storageDir;

    public function __construct()
    {
        $this->storageDir = dirname(__DIR__, 2) . '/data/rate-logs';
        if (!is_dir($this->storageDir)) {
            @mkdir($this->storageDir, 0755, true);
        }
    }

    public function check(string $ip, int $maxRequests, int $windowSeconds): array
    {
        $file = $this->storageDir . '/' . md5($ip) . '.json';
        $now = time();

        $data = ['count' => 0, 'resetAt' => $now + $windowSeconds];
        if (file_exists($file)) {
            $raw = file_get_contents($file);
            $saved = json_decode($raw, true);
            if ($saved && $saved['resetAt'] > $now) {
                $data = $saved;
            }
        }

        $data['count']++;

        if ($data['count'] > $maxRequests) {
            $retryAfter = $data['resetAt'] - $now;
            return ['allowed' => false, 'retryAfter' => max(1, $retryAfter)];
        }

        file_put_contents($file, json_encode($data));
        return ['allowed' => true, 'retryAfter' => 0];
    }

    public function clear(string $ip): void
    {
        $file = $this->storageDir . '/' . md5($ip) . '.json';
        if (file_exists($file)) {
            @unlink($file);
        }
    }
}
