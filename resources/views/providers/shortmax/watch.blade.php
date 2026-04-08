@extends('layouts.app')
@php
    $d = $detail;
    $title = data_get($d, 'data.shortPlayName') ?? data_get($d, 'shortPlayName') ?? data_get($d, 'data.title') ?? 'Drama';
    $cover = data_get($d, 'data.cover') ?? data_get($d, 'cover');

    // Episodes from allepisode
    $episodeList = data_get($episodes, 'data', data_get($episodes, 'result', data_get($episodes, 'list', [])));
    $episodeList = is_array($episodeList) ? $episodeList : [];

    $currentEp = collect($episodeList)->firstWhere(fn($e) =>
        (int)(data_get($e, 'episode') ?? data_get($e, 'episodeNum') ?? data_get($e, 'ep') ?? 0) === (int)$episode
    );
    $hlsRaw = $currentEp ? (data_get($currentEp, 'url') ?? data_get($currentEp, 'streamUrl') ?? data_get($currentEp, 'videoUrl') ?? data_get($currentEp, 'm3u8')) : null;
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
            @if($proxyUrl)
                <video id="vjsPlayer" class="video-js vjs-default-skin" controls autoplay playsinline style="width:100%;height:100%;" data-src="{{ $proxyUrl }}"></video>
            @else
                <div class="player-loading">
                    <div class="spinner"></div>
                    <span class="spinner-text">Video tidak tersedia untuk episode ini.</span>
                </div>
            @endif
        </div>
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:var(--gap-md);">
            <h2 style="font-size:1rem;color:var(--cream);">{{ $title }} — Episode {{ $episode }}</h2>
            <div style="display:flex;gap:var(--gap-sm);">
                @if($episode > 1)<a href="{{ route('shortmax.watch', ['id' => $shortPlayId, 'ep' => $episode-1]) }}" class="btn btn-ghost" style="padding:0.375rem 0.75rem;font-size:0.8rem;">← Prev</a>@endif
                @if(count($episodeList) > $episode)<a href="{{ route('shortmax.watch', ['id' => $shortPlayId, 'ep' => $episode+1]) }}" class="btn btn-ghost" style="padding:0.375rem 0.75rem;font-size:0.8rem;">Next →</a>@endif
            </div>
        </div>
        <a href="{{ route('shortmax.detail', ['id' => $shortPlayId]) }}" class="btn btn-outline" style="font-size:0.8rem;padding:0.375rem 0.75rem;">← Detail Drama</a>
    </div>
    <div>
        <h3 style="font-size:0.9rem;color:var(--text-secondary);margin-bottom:var(--gap-md);">Daftar Episode</h3>
        @if(count($episodeList) > 0)
        <div class="episode-grid">
            @foreach($episodeList as $ep)
                @php $epNum = (int)(data_get($ep,'episode') ?? data_get($ep,'episodeNum') ?? data_get($ep,'ep') ?? 0); @endphp
                @if($epNum > 0)
                <a href="{{ route('shortmax.watch', ['id' => $shortPlayId, 'ep' => $epNum]) }}"
                   class="ep-btn {{ $epNum === (int)$episode ? 'active' : '' }}">{{ $epNum }}</a>
                @endif
            @endforeach
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
