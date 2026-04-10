@extends('layouts.app')
@php
    // allepisode API returns:
    // {shortPlayName, shortPlayCover, shotIntroduce, totalEpisode, isFinish,
    //  shortPlayEpisodeInfos: [{episodeNo, episodeId, playVoucher, playClarity, episodeCover, isLock, sdkVid, ...}]}
    $d = $episodes;

    $title   = data_get($d, 'shortPlayName') ?? data_get($d, 'data.shortPlayName') ?? 'NetShort Drama';
    $cover   = data_get($d, 'shortPlayCover') ?? data_get($d, 'data.shortPlayCover') ?? '';
    $totalEp = data_get($d, 'totalEpisode') ?? data_get($d, 'data.totalEpisode') ?? 0;

    // Get episode list
    $epList = data_get($d, 'shortPlayEpisodeInfos') ?? data_get($d, 'data.shortPlayEpisodeInfos') ?? [];
    $epList = is_array($epList) ? $epList : [];

    // Find current episode
    $currentEp = collect($epList)->firstWhere('episodeNo', (int)$episode);

    // Video URL is in playVoucher
    $streamUrl = null;
    $isLocked  = false;
    $epCover   = $cover;
    if ($currentEp) {
        $streamUrl = data_get($currentEp, 'playVoucher') ?? null;
        $isLocked  = data_get($currentEp, 'isLock', false);
        $epCover   = data_get($currentEp, 'episodeCover') ?? $cover;
    }
@endphp
@section('title', $title . ' — Episode ' . $episode . ' — NetShort')
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
            @elseif($streamUrl)
                <video id="vjsPlayer" class="video-js vjs-default-skin" controls autoplay playsinline style="width:100%;height:100%;"
                    @if($epCover) poster="{{ $epCover }}" @endif>
                    <source src="{{ $streamUrl }}" type="video/mp4">
                </video>
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
                @if($episode > 1)<a href="{{ route('netshort.watch', ['id' => $shortPlayId, 'ep' => $episode-1]) }}" class="btn btn-ghost" style="padding:0.375rem 0.75rem;font-size:0.8rem;">← Prev</a>@endif
                @if($episode < (int)$totalEp)<a href="{{ route('netshort.watch', ['id' => $shortPlayId, 'ep' => $episode+1]) }}" class="btn btn-ghost" style="padding:0.375rem 0.75rem;font-size:0.8rem;">Next →</a>@endif
            </div>
        </div>
        <a href="{{ route('netshort.detail', ['id' => $shortPlayId]) }}" class="btn btn-outline" style="font-size:0.8rem;padding:0.375rem 0.75rem;">← Detail Drama</a>
    </div>
    <div>
        <h3 style="font-size:0.9rem;color:var(--text-secondary);margin-bottom:var(--gap-md);">Daftar Episode</h3>
        @if(count($epList) > 0)
        <div class="episode-grid">
            @foreach($epList as $ep)
                @php $epNum = (int) data_get($ep, 'episodeNo', 0); @endphp
                @if($epNum > 0)
                <a href="{{ route('netshort.watch', ['id' => $shortPlayId, 'ep' => $epNum]) }}"
                   class="ep-btn {{ $epNum === (int)$episode ? 'active' : '' }} {{ data_get($ep, 'isLock') ? 'ep-locked' : '' }}">{{ $epNum }}</a>
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
    const player = videojs(vjsEl, { controls: true, autoplay: true, preload: 'auto' });
}
saveHistory({provider:'netshort',drama_id:@json($shortPlayId),drama_title:@json($title),drama_thumbnail:@json($cover ?? ''),episode_number:{{ $episode }}});
</script>
@endpush
