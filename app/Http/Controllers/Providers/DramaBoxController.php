<?php

namespace App\Http\Controllers\Providers;

use App\Http\Controllers\Controller;
use App\Services\Providers\DramaBoxService;
use Illuminate\Http\Request;

class DramaBoxController extends Controller
{
    public function __construct(protected DramaBoxService $service) {}

    public function index(Request $request)
    {
        $page = $request->get('page', 1);
        $tab = $request->get('tab', 'foryou');

        $data = match($tab) {
            'trending' => $this->service->trending(),
            'latest'   => $this->service->latest(),
            'dubindo'  => $this->service->dubIndo('terpopuler', $page),
            default    => $this->service->forYou($page),
        };

        return view('providers.dramabox.index', compact('data', 'page', 'tab'));
    }

    public function detail(Request $request)
    {
        $bookId = $request->get('id');
        if (!$bookId) return redirect()->route('dramabox.index');

        $detail = $this->service->detail($bookId);
        return view('providers.dramabox.detail', compact('detail', 'bookId'));
    }

    public function watch(Request $request)
    {
        $bookId = $request->get('id');
        $episode = $request->get('ep', 1);
        if (!$bookId) return redirect()->route('dramabox.index');

        $detail = $this->service->detail($bookId);
        $episodes = $this->service->allEpisodes($bookId);

        return view('providers.dramabox.watch', compact('detail', 'episodes', 'bookId', 'episode'));
    }

    public function decrypt(Request $request)
    {
        $url = $request->get('url');
        if (!$url) return response()->json(['error' => 'URL required'], 400);

        $result = $this->service->decrypt($url);
        return response()->json($result);
    }
}
