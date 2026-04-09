<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * Manages a pool of HTTP proxies loaded from a text file.
 *
 * - Proxies rotate in round-robin order per process lifetime.
 * - Supports HTTP, HTTPS, and SOCKS5 proxy URIs.
 * - Controlled via PROXY_ENABLED and PROXY_FILE in .env.
 *
 * Proxy file format (one per line):
 *   http://user:pass@host:port
 *   socks5://host:port
 *   host:port              (treated as http://host:port)
 *   # comment lines and blank lines are ignored
 */
class ProxyManager
{
    /** @var string[] */
    protected array $proxies = [];

    /** @var int Round-robin index */
    protected int $index = 0;

    protected bool $enabled;

    public function __construct()
    {
        $this->enabled = (bool) config('services.proxy.enabled', false);

        if (!$this->enabled) {
            return;
        }

        $this->loadProxies();
    }

    /**
     * Load proxy list from the configured file path.
     */
    protected function loadProxies(): void
    {
        $path = config('services.proxy.file', storage_path('proxy.txt'));

        if (!file_exists($path)) {
            Log::warning("ProxyManager: proxy file not found at {$path}. Proxy disabled.");
            $this->enabled = false;
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            $line = trim($line);

            // Skip comments and blank lines
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            // Normalize bare host:port to http://host:port
            if (!preg_match('#^(https?|socks[45])://#i', $line)) {
                $line = 'http://' . $line;
            }

            $this->proxies[] = $line;
        }

        if (empty($this->proxies)) {
            Log::warning("ProxyManager: proxy file is empty or contains no valid entries.");
            $this->enabled = false;
            return;
        }

        // Randomize starting position to distribute load across processes
        $this->index = random_int(0, count($this->proxies) - 1);

        Log::info("ProxyManager: loaded " . count($this->proxies) . " proxies from {$path}.");
    }

    /**
     * Whether proxy rotation is active.
     */
    public function isEnabled(): bool
    {
        return $this->enabled && !empty($this->proxies);
    }

    /**
     * Get the next proxy URI in round-robin order.
     */
    public function next(): ?string
    {
        if (!$this->isEnabled()) {
            return null;
        }

        $proxy = $this->proxies[$this->index % count($this->proxies)];
        $this->index++;

        return $proxy;
    }

    /**
     * Get the current proxy count.
     */
    public function count(): int
    {
        return count($this->proxies);
    }

    /**
     * Get Guzzle-compatible options array for the current proxy.
     * Returns an empty array if proxy is disabled.
     */
    public function getOptions(): array
    {
        $proxy = $this->next();

        if ($proxy === null) {
            return [];
        }

        return [
            'proxy' => $proxy,
        ];
    }
}
