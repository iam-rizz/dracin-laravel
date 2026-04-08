<?php

namespace App\Http\Controllers\Providers;

use App\Http\Controllers\Controller;
use App\Services\Providers\NetShortService;
use Illuminate\Http\Request;

class NetShortController extends Controller
{
    public function __construct(protected NetShortService $service) {}

    public function index(Request $request)
    {
        $page = $request->get('page', 1);
        $tab = $request->get('tab', 'foryou');

        $data = match($tab) {
            'theaters' => $this->service->theaters(),
            default    => $this->service->forYou($page),
        };

        return view('providers.netshort.index', compact('data', 'page', 'tab'));
    }

    public function detail(Request $request)
    {
        $shortPlayId = $request->get('id');
        if (!$shortPlayId) return redirect()->route('netshort.index');

        // NetShort doesn't have a separate detail; we use allepisode
        $detail = $this->service->allEpisodes($shortPlayId);
        return view('providers.netshort.detail', compact('detail', 'shortPlayId'));
    }

    public function watch(Request $request)
    {
        $shortPlayId = $request->get('id');
        $episode = (int) $request->get('ep', 1);
        if (!$shortPlayId) return redirect()->route('netshort.index');

        $episodes = $this->service->allEpisodes($shortPlayId);
        return view('providers.netshort.watch', compact('episodes', 'shortPlayId', 'episode'));
    }
}
