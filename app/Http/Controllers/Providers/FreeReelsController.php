<?php

namespace App\Http\Controllers\Providers;

use App\Http\Controllers\Controller;
use App\Services\Providers\FreeReelsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
class FreeReelsController extends Controller
{
    public function __construct(protected FreeReelsService $service) {}

    public function index(Request $request)
    {
        $tab = $request->get('tab', 'foryou');

        $data = match($tab) {
            'homepage' => $this->service->homepage(),
            default    => $this->service->forYou(),
        };

        return view('providers.freereels.index', compact('data', 'tab'));
    }

    public function detail(Request $request)
    {
        $key = $request->get('id');
        if (!$key) return redirect()->route('freereels.index');

        $detail = $this->service->detailAndAllEpisode($key);
        return view('providers.freereels.detail', compact('detail', 'key'));
    }

    public function watch(Request $request)
    {
        $key = $request->get('id');
        $episode = (int) $request->get('ep', 1);
        if (!$key) return redirect()->route('freereels.index');

        $detail = $this->service->detailAndAllEpisode($key);
        return view('providers.freereels.watch', compact('detail', 'key', 'episode'));
    }

    public function hlsProxy(Request $request)
    {
        $url = $request->get('url');
        if (!$url) return response('URL required', 400);

        try {
            $domain = parse_url($url, PHP_URL_HOST) ?? '';
            $response = Http::withHeaders([
                'Referer'    => 'https://' . $domain,
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Origin'     => 'https://' . $domain,
            ])->timeout(30)->get($url);

            $content = $response->body();
            $contentType = $response->header('Content-Type');

            if (str_contains($contentType ?? '', 'm3u8') || str_ends_with($url, '.m3u8')) {
                $baseUrl = rtrim(dirname($url), '/');
                
                // Helper to rewrite a single URL
                $rewriteUrl = function ($seg) use ($baseUrl) {
                    $seg = trim($seg);
                    if (empty($seg)) return $seg;
                    if (str_starts_with($seg, 'http')) {
                        return route('freereels.hls') . '?url=' . urlencode($seg);
                    }
                    if (str_starts_with($seg, '/')) {
                        $parsedUrl = parse_url($baseUrl);
                        $domain = ($parsedUrl['scheme'] ?? 'https') . '://' . ($parsedUrl['host'] ?? '');
                        return route('freereels.hls') . '?url=' . urlencode($domain . $seg);
                    }
                    return route('freereels.hls') . '?url=' . urlencode($baseUrl . '/' . $seg);
                };

                // 1) Replace URI="..." attributes in tags (e.g. #EXT-X-MEDIA:URI="...")
                $content = preg_replace_callback('/URI="([^"]+)"/', function ($m) use ($rewriteUrl) {
                    return 'URI="' . $rewriteUrl($m[1]) . '"';
                }, $content);

                // 2) Replace standalone lines not starting with '#'
                $content = preg_replace_callback('/^(?!#)([^\r\n]+)/m', function ($m) use ($rewriteUrl) {
                    return $rewriteUrl($m[1]);
                }, $content);

                $contentType = 'application/vnd.apple.mpegurl';
            }

            return response($content, 200, [
                'Content-Type'                => $contentType ?? 'application/octet-stream',
                'Access-Control-Allow-Origin' => '*',
                'Cache-Control'               => 'no-store, no-cache, must-revalidate, max-age=0',
            ]);
        } catch (\Exception $e) {
            return response('Proxy error: ' . $e->getMessage(), 500);
        }
    }
}
