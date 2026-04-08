@extends('layouts.app')

@php
    $d = $detail;
    $title = data_get($d, 'data.bookName') ?? data_get($d, 'bookName') ?? 'Drama';
    $cover = data_get($d, 'data.cover') ?? data_get($d, 'data.coverWap') ?? data_get($d, 'cover');

    // allepisode returns array → ApiService wraps to ['data' => [...]]
    $episodeList = data_get($episodes, 'data', []);
    $episodeList = is_array($episodeList) ? $episodeList : [];

    // Episode URL param is 1-based; chapterIndex is 0-based
    $epIndex = (int)$episode - 1;
    $currentEp = null;
    foreach ($episodeList as $ep) {
        $idx = (int)(data_get($ep, 'chapterIndex') ?? -999);
        if ($idx === $epIndex) { $currentEp = $ep; break; }
    }

    // Extract encrypted video URL from cdnList → pick isDefault=1 (quality 720p fallback)
    $encryptedUrl = null;
    if ($currentEp) {
        $cdnList = data_get($currentEp, 'cdnList', []);
        $defCdn  = collect($cdnList)->firstWhere('isDefault', 1) ?? ($cdnList[0] ?? null);
        if ($defCdn) {
            $vids = data_get($defCdn, 'videoPathList', []);
            // Prefer isDefault=1, else 720p, else first
            $vid = collect($vids)->firstWhere('isDefault', 1)
                ?? collect($vids)->firstWhere('quality', 720)
                ?? ($vids[0] ?? null);
            $encryptedUrl = data_get($vid, 'videoPath');
        }
    }

    $totalEps = count($episodeList);
@endphp

@section('title', $title . ' — Episode ' . $episode . ' — DramaBox')

@section('content')
<div class="watch-layout">
    {{-- Player Column --}}
    <div>
        <div class="player-container" id="playerContainer">
            <div class="player-loading" id="playerLoading">
                <div class="spinner"></div>
                <span class="spinner-text">Mendekripsi &amp; Memuat Video...</span>
            </div>
            <video id="videoPlayer" controls playsinline
                   style="display:none;width:100%;height:100%;background:#000;">
            </video>
        </div>

        <div style="display:flex;justify-content:space-between;align-items:center;margin:var(--gap-md) 0;">
            <h2 style="font-size:1rem;color:var(--cream);">{{ $title }} — Episode {{ $episode }}</h2>
            <div style="display:flex;gap:var(--gap-sm);">
                @if($episode > 1)
                    <a href="{{ route('dramabox.watch', ['id' => $bookId, 'ep' => $episode - 1]) }}"
                       class="btn btn-ghost" style="padding:0.375rem 0.75rem;font-size:0.8rem;">← Prev</a>
                @endif
                @if($episode < $totalEps)
                    <a href="{{ route('dramabox.watch', ['id' => $bookId, 'ep' => $episode + 1]) }}"
                       class="btn btn-ghost" style="padding:0.375rem 0.75rem;font-size:0.8rem;">Next →</a>
                @endif
            </div>
        </div>

        <a href="{{ route('dramabox.detail', ['id' => $bookId]) }}"
           class="btn btn-outline" style="font-size:0.8rem;padding:0.375rem 0.75rem;">← Detail Drama</a>
    </div>

    {{-- Episode Sidebar --}}
    <div>
        <h3 style="font-size:0.9rem;color:var(--text-secondary);margin-bottom:var(--gap-md);">
            Daftar Episode ({{ $totalEps }})
        </h3>
        @if($totalEps > 0)
        <div class="episode-grid">
            @foreach($episodeList as $ep)
                @php $epNum = (int)(data_get($ep, 'chapterIndex') ?? -1) + 1; @endphp
                @if($epNum > 0)
                <a href="{{ route('dramabox.watch', ['id' => $bookId, 'ep' => $epNum]) }}"
                   class="ep-btn {{ $epNum === (int)$episode ? 'active' : '' }}">{{ $epNum }}</a>
                @endif
            @endforeach
        </div>
        @else
        <p style="color:var(--text-muted);font-size:0.8rem;">Daftar episode tidak tersedia.</p>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
const bookId       = @json($bookId);
const episode      = @json((int)$episode);
const encryptedUrl = @json($encryptedUrl);
const dramTitle    = @json($title);
const dramCover    = @json($cover ?? '');

function showError(msg) {
    document.getElementById('playerLoading').innerHTML =
        `<p style="color:var(--text-muted);padding:2rem;text-align:center;">${msg}<br>
         <a href="${window.location.href}" style="color:var(--gold);">🔄 Coba Refresh</a></p>`;
}

async function loadVideo() {
    if (!encryptedUrl) {
        showError('URL video tidak ditemukan untuk episode ini.');
        return;
    }

    try {
        const res  = await fetch('{{ route("dramabox.decrypt") }}?url=' + encodeURIComponent(encryptedUrl));
        const data = await res.json();

        // Decrypt API may return url in different paths
        const videoUrl = data?.streamUrl
            ?? data?.data?.streamUrl
            ?? data?.data?.url
            ?? data?.url
            ?? data?.result?.url
            ?? data?.videoUrl
            ?? data?.data?.videoUrl
            ?? null;

        if (!videoUrl) {
            console.error('Decrypt response:', JSON.stringify(data));
            showError('Gagal mendekripsi video. Response: ' + JSON.stringify(data).substr(0, 200));
            return;
        }

        const vid = document.getElementById('videoPlayer');
        vid.src = videoUrl;
        vid.style.display = 'block';
        document.getElementById('playerLoading').style.display = 'none';
        vid.play().catch(() => {});

        saveHistory({
            provider: 'dramabox',
            drama_id: bookId,
            drama_title: dramTitle,
            drama_thumbnail: dramCover,
            episode_number: episode,
        });
    } catch(e) {
        showError('Gagal memuat video: ' + e.message);
    }
}

loadVideo();
</script>
@endpush
