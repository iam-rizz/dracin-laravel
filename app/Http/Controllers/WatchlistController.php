<?php

namespace App\Http\Controllers;

use App\Models\Watchlist;
use App\Models\WatchHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WatchlistController extends Controller
{
    public function index()
    {
        $watchlist = Watchlist::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('user.watchlist', compact('watchlist'));
    }

    public function history()
    {
        $history = WatchHistory::where('user_id', Auth::id())
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('user.history', compact('history'));
    }

    public function toggle(Request $request)
    {
        $request->validate([
            'provider'         => 'required|string',
            'drama_id'         => 'required|string',
            'drama_title'      => 'required|string',
            'drama_thumbnail'  => 'nullable|string',
            'total_episodes'   => 'nullable|integer',
        ]);

        $userId = Auth::id();
        $existing = Watchlist::where([
            'user_id'  => $userId,
            'provider' => $request->provider,
            'drama_id' => $request->drama_id,
        ])->first();

        if ($existing) {
            $existing->delete();
            return response()->json(['status' => 'removed', 'message' => 'Dihapus dari watchlist']);
        }

        Watchlist::create([
            'user_id'         => $userId,
            'provider'        => $request->provider,
            'drama_id'        => $request->drama_id,
            'drama_title'     => $request->drama_title,
            'drama_thumbnail' => $request->drama_thumbnail,
            'total_episodes'  => $request->total_episodes,
        ]);

        return response()->json(['status' => 'added', 'message' => 'Ditambahkan ke watchlist']);
    }

    public function check(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['in_watchlist' => false]);
        }

        $exists = Watchlist::where([
            'user_id'  => Auth::id(),
            'provider' => $request->provider,
            'drama_id' => $request->drama_id,
        ])->exists();

        return response()->json(['in_watchlist' => $exists]);
    }

    public function saveHistory(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['status' => 'guest']);
        }

        $request->validate([
            'provider'        => 'required|string',
            'drama_id'        => 'required|string',
            'drama_title'     => 'required|string',
            'drama_thumbnail' => 'nullable|string',
            'episode_number'  => 'required|integer',
            'episode_title'   => 'nullable|string',
        ]);

        WatchHistory::updateOrCreate(
            [
                'user_id'  => Auth::id(),
                'provider' => $request->provider,
                'drama_id' => $request->drama_id,
            ],
            [
                'drama_title'     => $request->drama_title,
                'drama_thumbnail' => $request->drama_thumbnail,
                'episode_number'  => $request->episode_number,
                'episode_title'   => $request->episode_title,
            ]
        );

        return response()->json(['status' => 'saved']);
    }

    public function removeHistory(int $id)
    {
        WatchHistory::where('id', $id)->where('user_id', Auth::id())->delete();
        return back()->with('success', 'Riwayat dihapus.');
    }

    public function clearHistory()
    {
        WatchHistory::where('user_id', Auth::id())->delete();
        return back()->with('success', 'Semua riwayat dihapus.');
    }
}
