@extends('layouts.app')
@php
    $d = $detail;
    // FreeReels: data.info.name, data.info.cover, data.info.episode_list
    $info     = data_get($d, 'data.info', data_get($d, 'info', []));
    $title    = data_get($info, 'name') ?? data_get($d, 'data.title') ?? data_get($d, 'title') ?? 'FreeReels Drama';
    $cover    = data_get($info, 'cover') ?? data_get($d, 'data.cover') ?? data_get($d, 'cover');
    $epItems  = data_get($info, 'episode_list', data_get($d, 'data.episodes', data_get($d, 'data.chapterList', [])));
    $epItems  = is_array($epItems) ? $epItems : [];
    $totalEps = count($epItems) ?: (data_get($info, 'episode_count') ?? 0);

    // Current episode (1-indexed)
    $epIndex   = (int)$episode - 1;
    $currentEp = $epItems[$epIndex] ?? null;

    // Video URL: prefer H264 m3u8 → H265 m3u8 → fallback url/videoUrl
    $streamUrl = null;
    $proxyUrl  = null;
    if ($currentEp) {
        $streamUrl = data_get($currentEp, 'external_audio_h264_m3u8')
            ?: data_get($currentEp, 'external_audio_h265_m3u8')
            ?: data_get($currentEp, 'm3u8_url')
            ?: data_get($currentEp, 'video_url')
            ?: data_get($currentEp, 'url')
            ?: data_get($currentEp, 'videoUrl')
            ?: null;
        // Filter empty strings
        if ($streamUrl === '') $streamUrl = null;
        
        if ($streamUrl) {
            $proxyUrl = route('freereels.hls', ['url' => $streamUrl, 't' => time()]);
        }
    }

    $isHls = $streamUrl && str_contains($streamUrl, '.m3u8');
@endphp
@section('title', $title . ' — Episode ' . $episode . ' — FreeReels')
@section('content')
<div class="watch-layout">
    <div>
        <div class="player-container" id="playerContainer">
            @if($proxyUrl)
                <video id="videoPlayer" controls autoplay playsinline
                       style="width:100%;height:100%;background:#000;"
                       @if(!$isHls) src="{{ $proxyUrl }}" @endif>
                </video>
            @else
                <div class="player-loading">
                    <div class="spinner"></div>
                    <span class="spinner-text">Video tidak tersedia untuk episode ini.</span>
                </div>
            @endif
        </div>

        <div style="display:flex;justify-content:space-between;align-items:center;margin:var(--gap-md) 0;">
            <h2 style="font-size:1rem;color:var(--cream);">{{ $title }} — Episode {{ $episode }}</h2>
            <div style="display:flex;gap:var(--gap-sm);">
                @if($episode > 1)
                    <a href="{{ route('freereels.watch', ['id' => $key, 'ep' => $episode-1]) }}"
                       class="btn btn-ghost" style="padding:0.375rem 0.75rem;font-size:0.8rem;">← Prev</a>
                @endif
                @if($episode < $totalEps)
                    <a href="{{ route('freereels.watch', ['id' => $key, 'ep' => $episode+1]) }}"
                       class="btn btn-ghost" style="padding:0.375rem 0.75rem;font-size:0.8rem;">Next →</a>
                @endif
            </div>
        </div>
        <a href="{{ route('freereels.detail', ['id' => $key]) }}"
           class="btn btn-outline" style="font-size:0.8rem;padding:0.375rem 0.75rem;">← Detail Drama</a>
    </div>

    {{-- Episode Grid --}}
    <div>
        <h3 style="font-size:0.9rem;color:var(--text-secondary);margin-bottom:var(--gap-md);">
            Daftar Episode{{ $totalEps ? ' ('.$totalEps.')' : '' }}
        </h3>
        @if(count($epItems) > 0)
        <div class="episode-grid">
            @foreach($epItems as $i => $ep)
                <a href="{{ route('freereels.watch', ['id' => $key, 'ep' => $i+1]) }}"
                   class="ep-btn {{ ($i+1) === (int)$episode ? 'active' : '' }}">{{ $i+1 }}</a>
            @endforeach
        </div>
        @else
        <p style="color:var(--text-muted);font-size:0.8rem;">Daftar episode tidak tersedia.</p>
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($isHls)
<script src="https://cdn.jsdelivr.net/npm/hls.js@1.5/dist/hls.min.js"></script>
<script>
const video = document.getElementById('videoPlayer');
const proxyUrl = @json($proxyUrl);
if (video && proxyUrl) {
    if (Hls.isSupported()) {
        const hls = new Hls({ enableWorker: true });
        hls.loadSource(proxyUrl);
        hls.attachMedia(video);
        hls.on(Hls.Events.MANIFEST_PARSED, () => video.play().catch(() => {}));
        hls.on(Hls.Events.ERROR, (e, data) => {
            if (data.fatal) {
                document.getElementById('playerContainer').innerHTML =
                    '<p style="color:var(--text-muted);padding:2rem;text-align:center;">Gagal memuat stream. <a href="'+window.location.href+'" style="color:var(--gold);">Refresh</a></p>';
            }
        });
    } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
        video.src = proxyUrl;
        video.addEventListener('loadedmetadata', () => video.play().catch(() => {}));
    }
}
</script>
@endif
<script>
saveHistory({
    provider: 'freereels',
    drama_id: @json($key),
    drama_title: @json($title),
    drama_thumbnail: @json($cover ?? ''),
    episode_number: {{ (int)$episode }}
});
</script>
@endpush
