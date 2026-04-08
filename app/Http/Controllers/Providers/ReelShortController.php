<?php

namespace App\Http\Controllers\Providers;

use App\Http\Controllers\Controller;
use App\Services\Providers\ReelShortService;
use Illuminate\Http\Request;

class ReelShortController extends Controller
{
    public function __construct(protected ReelShortService $service) {}

    public function index(Request $request)
    {
        $page = $request->get('page', 1);
        $tab = $request->get('tab', 'foryou');

        $data = match($tab) {
            'homepage' => $this->service->homepage(),
            default    => $this->service->forYou($page),
        };

        return view('providers.reelshort.index', compact('data', 'page', 'tab'));
    }

    public function detail(Request $request)
    {
        $bookId = $request->get('id');
        if (!$bookId) return redirect()->route('reelshort.index');

        $detail = $this->service->detail($bookId);
        return view('providers.reelshort.detail', compact('detail', 'bookId'));
    }

    public function watch(Request $request)
    {
        $bookId = $request->get('id');
        $episode = (int) $request->get('ep', 1);
        if (!$bookId) return redirect()->route('reelshort.index');

        $detail = $this->service->detail($bookId);
        $streamData = $this->service->episode($bookId, $episode);

        return view('providers.reelshort.watch', compact('detail', 'streamData', 'bookId', 'episode'));
    }
}
