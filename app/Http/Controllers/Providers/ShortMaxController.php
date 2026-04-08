<?php

namespace App\Http\Controllers\Providers;

use App\Http\Controllers\Controller;
use App\Services\Providers\ShortMaxService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ShortMaxController extends Controller
{
    public function __construct(protected ShortMaxService $service) {}

    public function index(Request $request)
    {
        $page = $request->get('page', 1);
        $tab = $request->get('tab', 'foryou');

        $data = match($tab) {
            'latest' => $this->service->latest(),
            default  => $this->service->forYou($page),
        };

        return view('providers.shortmax.index', compact('data', 'page', 'tab'));
    }

    public function detail(Request $request)
    {
        $shortPlayId = $request->get('id');
        if (!$shortPlayId) return redirect()->route('shortmax.index');

        $detail = $this->service->detail($shortPlayId);
        return view('providers.shortmax.detail', compact('detail', 'shortPlayId'));
    }

    public function watch(Request $request)
    {
        $shortPlayId = $request->get('id');
        $episode = (int) $request->get('ep', 1);
        if (!$shortPlayId) return redirect()->route('shortmax.index');

        $detail = $this->service->detail($shortPlayId);
        $episodes = $this->service->allEpisodes($shortPlayId);

        return view('providers.shortmax.watch', compact('detail', 'episodes', 'shortPlayId', 'episode'));
    }

    // HLS Proxy for ShortMax
    public function hlsProxy(Request $request)
    {
        $url = $request->get('url');
        if (!$url) return response('URL required', 400);

        try {
            $response = Http::withHeaders([
                'Referer'    => 'https://www.shortmax.com/',
                'User-Agent' => 'Mozilla/5.0 (Linux; Android 10) AppleWebKit/537.36',
                'Origin'     => 'https://www.shortmax.com',
            ])->timeout(30)->get($url);

            $content = $response->body();
            $contentType = $response->header('Content-Type');

            // Rewrite m3u8 segment URLs if needed
            if (str_contains($contentType ?? '', 'm3u8') || str_ends_with($url, '.m3u8')) {
                $baseUrl = dirname($url);
                $content = preg_replace_callback('/^(?!#)(.+\.ts.*)$/m', function ($m) use ($baseUrl) {
                    $seg = trim($m[1]);
                    if (str_starts_with($seg, 'http')) return $seg;
                    return route('shortmax.hls') . '?url=' . urlencode($baseUrl . '/' . $seg);
                }, $content);
                $contentType = 'application/vnd.apple.mpegurl';
            }

            return response($content, 200, [
                'Content-Type'                => $contentType ?? 'application/octet-stream',
                'Access-Control-Allow-Origin' => '*',
                'Cache-Control'               => 'public, max-age=3600',
            ]);
        } catch (\Exception $e) {
            return response('Proxy error: ' . $e->getMessage(), 500);
        }
    }
}
