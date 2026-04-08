<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ApiService
{
    protected string $baseUrl = 'https://api.sansekai.my.id/api';
    protected int $cacheTtl  = 1800; // 30 menit (naik dari 10 menit)

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
        try {
            $response = Http::timeout(15)
                ->retry(2, 300)
                ->get($this->baseUrl . $endpoint, $params);

            if ($response->successful()) {
                $json = $response->json() ?? [];
                $result = $this->normalize($json);

                // Simpan cache reguler + stale (tidak pernah expire)
                Cache::put($cacheKey, $result, $ttl);
                Cache::forever($staleCacheKey, $result); // stale backup

                return $result;
            }
        } catch (\Throwable $e) {
            // API gagal — coba pakai stale cache
        }

        // Fallback ke stale data jika tersedia
        $stale = Cache::get($staleCacheKey);
        if ($stale !== null) {
            // Perpanjang cache reguler selama 5 menit agar tidak spam API
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
        try {
            $response = Http::timeout(20)
                ->retry(2, 300)
                ->get($this->baseUrl . $endpoint, $params);

            if ($response->successful()) {
                return $this->normalize($response->json() ?? []);
            }

            return ['error' => 'API request failed', 'status' => $response->status()];
        } catch (\Throwable $e) {
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
