@extends('layouts.app')
@php
    $d = $detail;
    $title    = data_get($d, 'data.book_title') ?? data_get($d, 'data.bookName') ?? data_get($d, 'bookName') ?? 'Drama';
    $cover    = data_get($d, 'data.book_pic')   ?? data_get($d, 'data.cover')    ?? data_get($d, 'cover');
    $totalEps = data_get($d, 'data.total_episodes') ?? data_get($d, 'data.totalEpisodes') ?? data_get($d, 'data.episodeCount') ?? 30;

    // ReelShort episode API returns:
    // { success:true, isLocked:false, videoList:[{url:'...m3u8', encode:'H265/H264', quality:720}] }
    $videoList  = data_get($streamData, 'videoList', data_get($streamData, 'data.videoList', []));
    // Prefer H264 (broader browser support) over H265
    $streamUrl  = null;
    if (is_array($videoList) && count($videoList) > 0) {
        $h264 = collect($videoList)->firstWhere('encode', 'H264');
        $streamUrl = $h264 ? $h264['url'] : ($videoList[0]['url'] ?? null);
    }
    $isLocked = data_get($streamData, 'isLocked', false);
@endphp
@section('title', $title . ' — Episode ' . $episode . ' — ReelShort')
@section('content')
<div class="watch-layout">
    <div>
        <div class="player-container" id="playerContainer">
            @if($isLocked)
                <div class="player-loading">
                    <span class="spinner-text" style="color:var(--gold);">🔒 Episode terkunci (Premium)</span>
                </div>
            @elseif($streamUrl)
                {{-- HLS player for m3u8 streams --}}
                <video id="videoPlayer" controls autoplay playsinline
                       style="width:100%;height:100%;background:#000;">
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
                    <a href="{{ route('reelshort.watch', ['id' => $bookId, 'ep' => $episode - 1]) }}"
                       class="btn btn-ghost" style="padding:0.375rem 0.75rem;font-size:0.8rem;">← Prev</a>
                @endif
                @if($episode < $totalEps)
                    <a href="{{ route('reelshort.watch', ['id' => $bookId, 'ep' => $episode + 1]) }}"
                       class="btn btn-ghost" style="padding:0.375rem 0.75rem;font-size:0.8rem;">Next →</a>
                @endif
            </div>
        </div>
        <a href="{{ route('reelshort.detail', ['id' => $bookId]) }}"
           class="btn btn-outline" style="font-size:0.8rem;padding:0.375rem 0.75rem;">← Detail Drama</a>
    </div>

    {{-- Episode Grid --}}
    <div>
        <h3 style="font-size:0.9rem;color:var(--text-secondary);margin-bottom:var(--gap-md);">
            Daftar Episode ({{ $totalEps }})
        </h3>
        <div class="episode-grid">
            @for($i = 1; $i <= min((int)$totalEps, 200); $i++)
                <a href="{{ route('reelshort.watch', ['id' => $bookId, 'ep' => $i]) }}"
                   class="ep-btn {{ $i === (int)$episode ? 'active' : '' }}">{{ $i }}</a>
            @endfor
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- HLS.js for m3u8 playback in non-Safari browsers --}}
<script src="https://cdn.jsdelivr.net/npm/hls.js@1.5/dist/hls.min.js"></script>
<script>
const streamUrl = @json($streamUrl);
const bookId    = @json($bookId);
const episode   = {{ (int)$episode }};
const dramTitle = @json($title);
const dramCover = @json($cover ?? '');

if (streamUrl) {
    const video = document.getElementById('videoPlayer');

    if (video) {
        if (Hls.isSupported()) {
            // Use HLS.js for Chrome, Firefox, etc.
            const hls = new Hls({ enableWorker: true, lowLatencyMode: false });
            hls.loadSource(streamUrl);
            hls.attachMedia(video);
            hls.on(Hls.Events.MANIFEST_PARSED, () => {
                video.play().catch(() => {});
            });
            hls.on(Hls.Events.ERROR, (event, data) => {
                if (data.fatal) {
                    document.getElementById('playerContainer').innerHTML =
                        `<p style="color:var(--text-muted);padding:2rem;text-align:center;">
                            Gagal memuat stream HLS.<br>
                            <a href="${window.location.href}" style="color:var(--gold);">🔄 Refresh</a>
                         </p>`;
                }
            });
        } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
            // Safari native HLS
            video.src = streamUrl;
            video.addEventListener('loadedmetadata', () => video.play().catch(() => {}));
        } else {
            document.getElementById('playerContainer').innerHTML =
                `<p style="color:var(--text-muted);padding:2rem;text-align:center;">
                     Browser tidak mendukung HLS. Coba Chrome atau Firefox.<br>
                     <a href="${streamUrl}" style="color:var(--gold);" target="_blank">Buka URL Langsung</a>
                 </p>`;
        }
    }
}

// Save history
saveHistory({
    provider: 'reelshort',
    drama_id: bookId,
    drama_title: dramTitle,
    drama_thumbnail: dramCover,
    episode_number: episode,
});
</script>
@endpush
