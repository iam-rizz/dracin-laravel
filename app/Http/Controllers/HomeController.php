<?php

namespace App\Http\Controllers;

use App\Services\ProxyManager;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    protected string $baseUrl  = 'https://api.sansekai.my.id/api';
    protected int    $cacheTtl = 1800;  // 30 menit cache reguler
    protected string $cacheKey = 'home_data_v4';

    public function __construct(protected ProxyManager $proxy) {}

    public function index()
    {
        // ── Selalu tampilkan data lama dulu jika ada ──────────────────
        $staleKey = 'home_stale_v4';
        $locked   = 'home_fetching_v4';

        $existing = Cache::get($this->cacheKey);

        if ($existing) {
            // Cache masih valid → langsung render
            return view('home', $existing);
        }

        // Cache expired/kosong → cek stale & fetch baru
        $stale = Cache::get($staleKey);

        // Hanya fetch jika tidak sedang ada request lain yang fetching
        if (!Cache::has($locked)) {
            Cache::put($locked, true, 30); // lock 30 detik

            try {
                $fresh = $this->fetchAll();

                if (!empty(array_filter($fresh))) {
                    Cache::put($this->cacheKey, $fresh, $this->cacheTtl);
                    Cache::forever($staleKey, $fresh);
                }

                Cache::forget($locked);

                return view('home', $fresh);
            } catch (\Throwable $e) {
                Cache::forget($locked);
            }
        }

        // Fallback ke stale data jika fetch gagal/locked
        if ($stale) {
            // Perpanjang reguler selama 2 menit supaya halaman berikutnya cepat
            Cache::put($this->cacheKey, $stale, 120);
            return view('home', $stale);
        }

        // Absolute fallback — kosong tapi tidak error
        return view('home', [
            'featured'       => [],
            'dramaBoxNew'    => [],
            'reelShortNew'   => [],
            'shortMaxNew'    => [],
            'netShortNew'    => [],
            'meloloTrending' => [],
            'freeReelsHome'  => [],
            'novaHome'       => [],
        ]);
    }

    /**
     * Fetch semua provider secara PARALEL via Http::pool().
     * Pool berjalan concurrent → total waktu ≈ provider paling lambat (bukan jumlah).
     * Each request in the pool gets a different proxy (round-robin).
     */
    private function fetchAll(): array
    {
        $useProxy = $this->proxy->isEnabled();

        // Get a different proxy for each request in the pool
        $proxyFor = function () use ($useProxy): array {
            return $useProxy ? $this->proxy->getOptions() : [];
        };

        $proxyLabel = $useProxy ? $this->proxy->current() : 'direct';
        Log::info("[HomeController] fetchAll START — proxy: {$proxyLabel}, total proxies: {$this->proxy->count()}");

        $responses = Http::pool(fn ($pool) => [
            $pool->as('featured')    ->timeout(10)->withOptions($proxyFor())->get("{$this->baseUrl}/dramabox/trending"),
            $pool->as('dramaBoxNew') ->timeout(10)->withOptions($proxyFor())->get("{$this->baseUrl}/dramabox/foryou"),
            $pool->as('reelShort')   ->timeout(10)->withOptions($proxyFor())->get("{$this->baseUrl}/reelshort/foryou",  ['page' => 1]),
            $pool->as('shortMax')    ->timeout(10)->withOptions($proxyFor())->get("{$this->baseUrl}/shortmax/foryou"),
            $pool->as('netShort')    ->timeout(10)->withOptions($proxyFor())->get("{$this->baseUrl}/netshort/foryou",   ['page' => 1]),
            $pool->as('melolo')      ->timeout(10)->withOptions($proxyFor())->get("{$this->baseUrl}/melolo/trending"),
            $pool->as('freeReels')   ->timeout(10)->withOptions($proxyFor())->get("{$this->baseUrl}/freereels/homepage"),
            $pool->as('dramaNova')   ->timeout(10)->withOptions($proxyFor())->get("{$this->baseUrl}/dramanova/home", ['page' => 1]),
        ]);

        // Log results per provider
        $providers = ['featured', 'dramaBoxNew', 'reelShort', 'shortMax', 'netShort', 'melolo', 'freeReels', 'dramaNova'];
        foreach ($providers as $key) {
            $r = $responses[$key] ?? null;
            if ($r instanceof \Throwable) {
                Log::error("[HomeController] {$key}: EXCEPTION — {$r->getMessage()}");
            } elseif ($r && method_exists($r, 'successful')) {
                if ($r->successful()) {
                    Log::info("[HomeController] {$key}: OK ({$r->status()}, " . strlen($r->body()) . " bytes)");
                } else {
                    Log::warning("[HomeController] {$key}: FAIL ({$r->status()})");
                }
            } else {
                Log::warning("[HomeController] {$key}: NO RESPONSE");
            }
        }

        return [
            'featured'       => $this->parse($responses['featured']),
            'dramaBoxNew'    => $this->parse($responses['dramaBoxNew']),
            'reelShortNew'   => $this->parse($responses['reelShort']),
            'shortMaxNew'    => $this->parse($responses['shortMax']),
            'netShortNew'    => $this->parse($responses['netShort']),
            'meloloTrending' => $this->parse($responses['melolo']),
            'freeReelsHome'  => $this->parse($responses['freeReels']),
            'novaHome'       => $this->parse($responses['dramaNova']),
        ];
    }

    /**
     * Parse satu response dari Http::pool().
     * Normalize: list[] → ['data' => []].
     */
    private function parse(mixed $r): array
    {
        try {
            if ($r instanceof \Throwable || !method_exists($r, 'successful')) return [];
            if (!$r->successful()) return [];

            $json = $r->json();
            if (!is_array($json)) return [];

            // DramaBox: top-level array → wrap
            return array_is_list($json) ? ['data' => $json] : $json;
        } catch (\Throwable) {
            return [];
        }
    }
}
