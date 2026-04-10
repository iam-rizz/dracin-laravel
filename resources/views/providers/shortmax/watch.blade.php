@extends('layouts.app')
@php
    // Detail API: {status, data: {shortPlayName, picUrl, totalEpisodes, ...}}
    $d = $detail;
    $data  = data_get($d, 'data', $d);
    $title = data_get($data, 'shortPlayName') ?? data_get($data, 'name') ?? data_get($data, 'title') ?? 'Drama';
    $cover = data_get($data, 'picUrl') ?? data_get($data, 'cover') ?? '';
    $totalEps = data_get($data, 'totalEpisodes') ?? data_get($data, 'updateEpisode') ?? 0;

    // Episode API: {status, episode: {episodeNum, videoUrl: {video_480, video_720, video_1080}, locked, cover, duration}}
    $ep = data_get($episodeData, 'episode', []);
    $videoUrls = data_get($ep, 'videoUrl', []);
    $hlsRaw = null;
    if (is_array($videoUrls)) {
        // Prefer 720p, fallback to 1080p, then 480p
        $hlsRaw = $videoUrls['video_720'] ?? $videoUrls['video_1080'] ?? $videoUrls['video_480'] ?? null;
    } elseif (is_string($videoUrls)) {
        $hlsRaw = $videoUrls;
    }
    // Fallback: check direct keys
    if (!$hlsRaw) {
        $hlsRaw = data_get($ep, 'url') ?? data_get($ep, 'streamUrl') ?? data_get($ep, 'm3u8') ?? null;
    }

    $isLocked = data_get($ep, 'locked', false);
    $epCover  = data_get($ep, 'cover') ?? $cover;

    // Use proxy for ShortMax HLS
    $proxyUrl = $hlsRaw ? route('shortmax.hls', ['url' => $hlsRaw]) : null;
@endphp
@section('title', $title . ' — Episode ' . $episode . ' — ShortMax')
@section('content')

@push('styles')
<link href="https://vjs.zencdn.net/8.10.0/video-js.css" rel="stylesheet" />
@endpush

<div class="watch-layout">
    <div>
        <div class="player-container">
            @if($isLocked)
                <div class="player-loading">
                    <svg width="48" height="48" fill="none" stroke="var(--text-muted)" stroke-width="1.5" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    <span class="spinner-text" style="margin-top:0.75rem;">Episode ini terkunci (berbayar).</span>
                </div>
            @elseif($proxyUrl)
                <video id="vjsPlayer" class="video-js vjs-default-skin" controls autoplay playsinline style="width:100%;height:100%;"
                    data-src="{{ $proxyUrl }}"
                    @if($epCover) poster="{{ $epCover }}" @endif></video>
            @else
                <div class="player-loading">
                    <svg width="48" height="48" fill="none" stroke="var(--text-muted)" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                    <span class="spinner-text" style="margin-top:0.75rem;">Video tidak tersedia untuk episode ini.</span>
                </div>
            @endif
        </div>
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:var(--gap-md);">
            <h2 style="font-size:1rem;color:var(--cream);">{{ $title }} — Episode {{ $episode }}</h2>
            <div style="display:flex;gap:var(--gap-sm);">
                @if($episode > 1)<a href="{{ route('shortmax.watch', ['id' => $shortPlayId, 'ep' => $episode-1]) }}" class="btn btn-ghost" style="padding:0.375rem 0.75rem;font-size:0.8rem;">← Prev</a>@endif
                @if($episode < (int)$totalEps)<a href="{{ route('shortmax.watch', ['id' => $shortPlayId, 'ep' => $episode+1]) }}" class="btn btn-ghost" style="padding:0.375rem 0.75rem;font-size:0.8rem;">Next →</a>@endif
            </div>
        </div>
        <a href="{{ route('shortmax.detail', ['id' => $shortPlayId]) }}" class="btn btn-outline" style="font-size:0.8rem;padding:0.375rem 0.75rem;">← Detail Drama</a>
    </div>
    <div>
        <h3 style="font-size:0.9rem;color:var(--text-secondary);margin-bottom:var(--gap-md);">Daftar Episode</h3>
        @if((int)$totalEps > 0)
        <div class="episode-grid">
            @for($i = 1; $i <= min((int)$totalEps, 200); $i++)
                <a href="{{ route('shortmax.watch', ['id' => $shortPlayId, 'ep' => $i]) }}"
                   class="ep-btn {{ $i === (int)$episode ? 'active' : '' }}">{{ $i }}</a>
            @endfor
            @if((int)$totalEps > 200)<span class="ep-btn" style="opacity:0.5;cursor:default;">+{{ (int)$totalEps - 200 }} lagi</span>@endif
        </div>
        @else <p style="color:var(--text-muted);font-size:0.8rem;">Tidak ada daftar episode.</p>@endif
    </div>
</div>

@endsection
@push('scripts')
<script src="https://vjs.zencdn.net/8.10.0/video.min.js"></script>
<script>
const vjsEl = document.getElementById('vjsPlayer');
if (vjsEl) {
    const src = vjsEl.dataset.src;
    const player = videojs(vjsEl, { controls: true, autoplay: true, preload: 'auto' });
    player.src({ src: src, type: 'application/x-mpegURL' });
}
saveHistory({provider:'shortmax',drama_id:@json($shortPlayId),drama_title:@json($title),drama_thumbnail:@json($cover ?? ''),episode_number:{{ $episode }}});
</script>
@endpush
