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

    /** @var string|null The proxy selected for the current rotation step */
    protected ?string $lastUsed = null;

    public function __construct()
    {
        $this->enabled = (bool) config('services.proxy.enabled', false);

        if (!$this->enabled) {
            Log::debug('[ProxyManager] Proxy is DISABLED via config.');
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

        Log::info("[ProxyManager] Loading proxies from: {$path}");

        if (!file_exists($path)) {
            Log::warning("[ProxyManager] Proxy file not found at {$path}. Proxy DISABLED.");
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
            if (!preg_match('#^(https?|socks[45][ha]?)://#i', $line)) {
                $line = 'http://' . $line;
            }

            $this->proxies[] = $line;
        }

        if (empty($this->proxies)) {
            Log::warning('[ProxyManager] Proxy file is empty or contains no valid entries. Proxy DISABLED.');
            $this->enabled = false;
            return;
        }

        // Randomize starting position to distribute load across processes
        $this->index = random_int(0, count($this->proxies) - 1);

        Log::info('[ProxyManager] Loaded ' . count($this->proxies) . " proxies. Starting at index {$this->index}.");
    }

    /**
     * Whether proxy rotation is active.
     */
    public function isEnabled(): bool
    {
        return $this->enabled && !empty($this->proxies);
    }

    /**
     * Peek at the next proxy without advancing the index.
     * Useful for logging before the request is made.
     */
    public function current(): ?string
    {
        if (!$this->isEnabled()) {
            return null;
        }

        return $this->proxies[$this->index % count($this->proxies)];
    }

    /**
     * Get the next proxy URI in round-robin order and advance the index.
     */
    public function next(): ?string
    {
        if (!$this->isEnabled()) {
            return null;
        }

        $proxy = $this->proxies[$this->index % count($this->proxies)];
        $this->index++;
        $this->lastUsed = $proxy;

        return $proxy;
    }

    /**
     * Get the last proxy that was returned by next().
     */
    public function getLastUsed(): ?string
    {
        return $this->lastUsed;
    }

    /**
     * Get the current proxy count.
     */
    public function count(): int
    {
        return count($this->proxies);
    }

    /**
     * Get Guzzle-compatible options array for the next proxy.
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
