<?php

namespace App\Http\Controllers\Providers;

use App\Http\Controllers\Controller;
use App\Services\Providers\MeloloService;
use Illuminate\Http\Request;

class MeloloController extends Controller
{
    public function __construct(protected MeloloService $service) {}

    public function index(Request $request)
    {
        $tab = $request->get('tab', 'foryou');

        $data = match($tab) {
            'trending' => $this->service->trending(),
            'latest'   => $this->service->latest(),
            default    => $this->service->forYou(),
        };

        return view('providers.melolo.index', compact('data', 'tab'));
    }

    public function detail(Request $request)
    {
        $bookId = $request->get('id');
        if (!$bookId) return redirect()->route('melolo.index');

        $detail = $this->service->detail($bookId);
        return view('providers.melolo.detail', compact('detail', 'bookId'));
    }

    public function watch(Request $request)
    {
        $bookId = $request->get('id');
        $videoId = $request->get('vid');
        $episode = (int) $request->get('ep', 1);
        if (!$bookId) return redirect()->route('melolo.index');

        $detail = $this->service->detail($bookId);
        $streamData = $videoId ? $this->service->stream($videoId) : [];

        return view('providers.melolo.watch', compact('detail', 'streamData', 'bookId', 'videoId', 'episode'));
    }

    public function stream(Request $request)
    {
        $videoId = $request->get('vid');
        if (!$videoId) return response()->json(['error' => 'videoId required'], 400);

        $result = $this->service->stream($videoId);
        return response()->json($result);
    }
}
