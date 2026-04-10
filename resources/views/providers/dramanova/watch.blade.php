@extends('layouts.app')
@php
    // Detail API: {code, msg, data: {dramaId, title, synopsis, totalEpisodes, posterImgUrl, bannerImgUrl,
    //   episodes: [{id, episodeNumber, fileId, thumbnailImg, status, videos}]}}
    $d = $detail;
    $data   = data_get($d, 'data', $d);
    $title  = data_get($data, 'title') ?? data_get($data, 'dramaTitle') ?? 'DramaNova Drama';
    $cover  = data_get($data, 'posterImgUrl') ?? data_get($data, 'posterImg') ?? data_get($data, 'bannerImgUrl') ?? '';
    $totalEp = data_get($data, 'totalEpisodes') ?? 0;

    // Episode list from detail
    $epList  = data_get($data, 'episodes', []);
    $epList  = is_array($epList) ? $epList : [];

    // Find current episode's fileId if not provided
    $currentEp = collect($epList)->firstWhere('episodeNumber', (int)$episode);
    $currentFileId = $fileId ?: data_get($currentEp, 'fileId');
    $epThumb = data_get($currentEp, 'thumbnailImg') ?? '';

    // Try to extract video URL from server-side videoData if available
    $streamUrl = data_get($videoData, 'Result.PlayInfoList.0.MainPlayUrl')
              ?? data_get($videoData, 'result.playInfoList.0.mainPlayUrl')
              ?? null;

    // Fallback: try 1080p
    if (!$streamUrl) {
        $playList = data_get($videoData, 'Result.PlayInfoList', data_get($videoData, 'result.playInfoList', []));
        if (is_array($playList)) {
            // Prefer 720p, then any available
            foreach ($playList as $pl) {
                $def = data_get($pl, 'Definition') ?? data_get($pl, 'definition') ?? '';
                if ($def === '720p') {
                    $streamUrl = data_get($pl, 'MainPlayUrl') ?? data_get($pl, 'mainPlayUrl');
                    break;
                }
            }
            // If no 720p found, use first available
            if (!$streamUrl && !empty($playList)) {
                $streamUrl = data_get($playList[0], 'MainPlayUrl') ?? data_get($playList[0], 'mainPlayUrl');
            }
        }
    }
@endphp
@section('title', $title . ' — Episode ' . $episode . ' — DramaNova')
@section('content')

@push('styles')
<link href="https://vjs.zencdn.net/8.10.0/video-js.css" rel="stylesheet" />
@endpush

<div class="watch-layout">
    <div>
        <div class="player-container" id="playerContainer">
            @if($streamUrl)
                <video id="vjsPlayer" class="video-js vjs-default-skin" controls autoplay playsinline style="width:100%;height:100%;">
                    <source src="{{ $streamUrl }}" type="video/mp4">
                </video>
            @elseif($currentFileId)
                <div class="player-loading" id="playerLoading">
                    <div class="spinner"></div>
                    <span class="spinner-text">Memuat video...</span>
                </div>
            @else
                <div class="player-loading">
                    <svg width="48" height="48" fill="none" stroke="var(--text-muted)" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                    <span class="spinner-text" style="margin-top:0.75rem;">Video tidak tersedia.</span>
                </div>
            @endif
        </div>
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:var(--gap-md);">
            <h2 style="font-size:1rem;color:var(--cream);">{{ $title }} — Episode {{ $episode }}</h2>
            <div style="display:flex;gap:var(--gap-sm);">
                @if($episode > 1)
                    @php $prevEp = collect($epList)->firstWhere('episodeNumber', $episode - 1); @endphp
                    <a href="{{ route('dramanova.watch', ['id' => $dramaId, 'fid' => data_get($prevEp, 'fileId', ''), 'ep' => $episode-1]) }}" class="btn btn-ghost" style="padding:0.375rem 0.75rem;font-size:0.8rem;">← Prev</a>
                @endif
                @if($episode < (int)$totalEp)
                    @php $nextEp = collect($epList)->firstWhere('episodeNumber', $episode + 1); @endphp
                    <a href="{{ route('dramanova.watch', ['id' => $dramaId, 'fid' => data_get($nextEp, 'fileId', ''), 'ep' => $episode+1]) }}" class="btn btn-ghost" style="padding:0.375rem 0.75rem;font-size:0.8rem;">Next →</a>
                @endif
            </div>
        </div>
        <a href="{{ route('dramanova.detail', ['id' => $dramaId]) }}" class="btn btn-outline" style="font-size:0.8rem;padding:0.375rem 0.75rem;">← Detail Drama</a>
    </div>
    <div>
        <h3 style="font-size:0.9rem;color:var(--text-secondary);margin-bottom:var(--gap-md);">Daftar Episode</h3>
        @if(count($epList) > 0)
        <div class="episode-grid">
            @foreach($epList as $ep)
                @php
                    $epNum = (int) data_get($ep, 'episodeNumber', 0);
                    $fid   = data_get($ep, 'fileId', '');
                @endphp
                @if($epNum > 0)
                <a href="{{ route('dramanova.watch', ['id' => $dramaId, 'fid' => $fid, 'ep' => $epNum]) }}"
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
// Initialize videojs if video already loaded server-side
const vjsEl = document.getElementById('vjsPlayer');
if (vjsEl) {
    videojs(vjsEl, { controls: true, autoplay: true, preload: 'auto' });
}

@if(!$streamUrl && $currentFileId)
// Load video dynamically via getvideo API
fetch('{{ route("dramanova.getvideo") }}?fid={{ $currentFileId }}')
    .then(r => r.json())
    .then(data => {
        // Extract from Result.PlayInfoList[].MainPlayUrl
        const playList = data?.Result?.PlayInfoList ?? data?.result?.playInfoList ?? [];
        let url = null;

        // Prefer 720p
        for (const item of playList) {
            if ((item.Definition ?? item.definition) === '720p') {
                url = item.MainPlayUrl ?? item.mainPlayUrl;
                break;
            }
        }
        // Fallback to first available
        if (!url && playList.length > 0) {
            url = playList[0].MainPlayUrl ?? playList[0].mainPlayUrl;
        }

        const container = document.getElementById('playerLoading');
        if (url && container) {
            const vid = document.createElement('video');
            vid.id = 'vjsPlayer';
            vid.className = 'video-js vjs-default-skin';
            vid.controls = true;
            vid.autoplay = true;
            vid.playsInline = true;
            vid.style.cssText = 'width:100%;height:100%;';

            const src = document.createElement('source');
            src.src = url;
            src.type = 'video/mp4';
            vid.appendChild(src);

            container.replaceWith(vid);
            videojs(vid, { controls: true, autoplay: true, preload: 'auto' });
        } else if (container) {
            container.innerHTML = '<svg width="48" height="48" fill="none" stroke="var(--text-muted)" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg><span class="spinner-text" style="margin-top:0.75rem;">Video tidak tersedia.</span>';
        }
    })
    .catch(() => {
        const container = document.getElementById('playerLoading');
        if (container) {
            container.innerHTML = '<svg width="48" height="48" fill="none" stroke="var(--text-muted)" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg><span class="spinner-text" style="margin-top:0.75rem;">Gagal memuat video.</span>';
        }
    });
@endif

saveHistory({provider:'dramanova',drama_id:@json($dramaId),drama_title:@json($title),drama_thumbnail:@json($cover ?? ''),episode_number:{{ $episode }}});
</script>
@endpush
