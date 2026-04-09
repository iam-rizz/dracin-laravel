<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ApiService
{
    protected string $baseUrl = 'https://api.sansekai.my.id/api';
    protected int $cacheTtl  = 1800; // 30 menit

    public function __construct(protected ProxyManager $proxy) {}

    /**
     * Build an HTTP PendingRequest with optional proxy support.
     */
    protected function makeRequest(int $timeout = 15): \Illuminate\Http\Client\PendingRequest
    {
        $request = Http::timeout($timeout)->retry(2, 300);

        if ($this->proxy->isEnabled()) {
            $request = $request->withOptions($this->proxy->getOptions());
        }

        return $request;
    }

    /**
     * GET dengan caching + stale fallback.
     * Jika API gagal, data lama (stale) tetap dikembalikan daripada kosong.
     */
    public function get(string $endpoint, array $params = [], ?int $ttl = null): array
    {
        $ttl = $ttl ?? $this->cacheTtl;
        $cacheKey      = 'api_' . md5($endpoint . json_encode($params));
        $staleCacheKey = 'api_stale_' . md5($endpoint . json_encode($params));

        // Cek cache reguler dulu
        $cached = Cache::get($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        // Cache miss → hit API
        $proxyUsed = null;
        try {
            $proxyUsed = $this->proxy->isEnabled() ? $this->proxy->current() : 'direct';

            Log::info("[ApiService] GET {$endpoint}", [
                'proxy'  => $proxyUsed,
                'params' => $params,
            ]);

            $response = $this->makeRequest(15)
                ->get($this->baseUrl . $endpoint, $params);

            if ($response->successful()) {
                $json = $response->json() ?? [];
                $result = $this->normalize($json);

                Log::info("[ApiService] OK {$endpoint}", [
                    'proxy'  => $proxyUsed,
                    'status' => $response->status(),
                    'size'   => strlen($response->body()),
                ]);

                // Simpan cache reguler + stale (tidak pernah expire)
                Cache::put($cacheKey, $result, $ttl);
                Cache::forever($staleCacheKey, $result);

                return $result;
            }

            Log::warning("[ApiService] FAIL {$endpoint}", [
                'proxy'  => $proxyUsed,
                'status' => $response->status(),
                'body'   => mb_substr($response->body(), 0, 200),
            ]);
        } catch (\Throwable $e) {
            Log::error("[ApiService] ERROR {$endpoint}", [
                'proxy'   => $proxyUsed,
                'message' => $e->getMessage(),
            ]);
        }

        // Fallback ke stale data jika tersedia
        $stale = Cache::get($staleCacheKey);
        if ($stale !== null) {
            Log::info("[ApiService] STALE fallback for {$endpoint}");
            Cache::put($cacheKey, $stale, 300);
            return $stale;
        }

        return ['error' => 'API unavailable'];
    }

    /**
     * GET tanpa cache (untuk stream URL, dsb).
     */
    public function getNoCache(string $endpoint, array $params = []): array
    {
        $proxyUsed = null;
        try {
            $proxyUsed = $this->proxy->isEnabled() ? $this->proxy->current() : 'direct';

            Log::info("[ApiService] GET (no-cache) {$endpoint}", [
                'proxy'  => $proxyUsed,
                'params' => $params,
            ]);

            $response = $this->makeRequest(20)
                ->get($this->baseUrl . $endpoint, $params);

            if ($response->successful()) {
                Log::info("[ApiService] OK (no-cache) {$endpoint}", [
                    'proxy'  => $proxyUsed,
                    'status' => $response->status(),
                ]);
                return $this->normalize($response->json() ?? []);
            }

            Log::warning("[ApiService] FAIL (no-cache) {$endpoint}", [
                'proxy'  => $proxyUsed,
                'status' => $response->status(),
            ]);
            return ['error' => 'API request failed', 'status' => $response->status()];
        } catch (\Throwable $e) {
            Log::error("[ApiService] ERROR (no-cache) {$endpoint}", [
                'proxy'   => $proxyUsed,
                'message' => $e->getMessage(),
            ]);
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Normalize: array top-level → wrap ke ['data' => $arr].
     */
    private function normalize(mixed $json): array
    {
        if (!is_array($json)) return [];
        if (array_is_list($json)) return ['data' => $json];
        return $json;
    }

    public function clearCache(string $endpoint, array $params = []): void
    {
        Cache::forget('api_' . md5($endpoint . json_encode($params)));
    }
}
