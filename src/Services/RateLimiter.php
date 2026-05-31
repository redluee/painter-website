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
        // Probabilistic garbage collection (5% of calls)
        if (random_int(1, 100) <= 5) {
            $this->gc();
        }

        $file = $this->storageDir . '/' . md5($ip) . '.json';
        $now = time();

        $data = ['count' => 0, 'resetAt' => $now + $windowSeconds];
        $fh = fopen($file, 'c+');
        if ($fh) {
            flock($fh, LOCK_EX);

            $raw = stream_get_contents($fh);
            if ($raw !== false) {
                $saved = json_decode($raw, true);
                if ($saved && $saved['resetAt'] > $now) {
                    $data = $saved;
                }
            }

            $data['count']++;

            if ($data['count'] > $maxRequests) {
                $retryAfter = $data['resetAt'] - $now;
                ftruncate($fh, 0);
                rewind($fh);
                fwrite($fh, json_encode($data));
                flock($fh, LOCK_UN);
                fclose($fh);
                return ['allowed' => false, 'retryAfter' => max(1, $retryAfter)];
            }

            ftruncate($fh, 0);
            rewind($fh);
            fwrite($fh, json_encode($data));
            flock($fh, LOCK_UN);
            fclose($fh);
        }

        return ['allowed' => true, 'retryAfter' => 0];
    }

    public function clear(string $ip): void
    {
        $file = $this->storageDir . '/' . md5($ip) . '.json';
        if (file_exists($file)) {
            @unlink($file);
        }
    }

    private function gc(): void
    {
        $maxAge = 2 * 60 * 60; // 2 hours — well beyond any rate-limit window
        $now = time();
        $files = glob($this->storageDir . '/*.json');
        if (!$files) return;
        foreach ($files as $file) {
            if ($now - filemtime($file) > $maxAge) {
                @unlink($file);
            }
        }
    }
}
