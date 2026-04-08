<?php

namespace App\Http\Controllers\Providers;

use App\Http\Controllers\Controller;
use App\Services\Providers\DramaNovaService;
use Illuminate\Http\Request;

class DramaNovaController extends Controller
{
    public function __construct(protected DramaNovaService $service) {}

    public function index(Request $request)
    {
        $page = $request->get('page', 1);
        $data = $this->service->home($page);
        return view('providers.dramanova.index', compact('data', 'page'));
    }

    public function detail(Request $request)
    {
        $dramaId = $request->get('id');
        if (!$dramaId) return redirect()->route('dramanova.index');

        $detail = $this->service->detail($dramaId);
        return view('providers.dramanova.detail', compact('detail', 'dramaId'));
    }

    public function watch(Request $request)
    {
        $dramaId = $request->get('id');
        $fileId = $request->get('fid');
        $episode = (int) $request->get('ep', 1);
        if (!$dramaId) return redirect()->route('dramanova.index');

        $detail = $this->service->detail($dramaId);
        $videoData = $fileId ? $this->service->getVideo($fileId) : [];

        return view('providers.dramanova.watch', compact('detail', 'videoData', 'dramaId', 'fileId', 'episode'));
    }

    public function getVideo(Request $request)
    {
        $fileId = $request->get('fid');
        if (!$fileId) return response()->json(['error' => 'fileId required'], 400);

        $result = $this->service->getVideo($fileId);
        return response()->json($result);
    }
}
