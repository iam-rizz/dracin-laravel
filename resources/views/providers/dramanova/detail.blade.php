@extends('layouts.app')
@php
    // DramaNova detail API:
    // { code, msg, data: {dramaId, title, synopsis, description, totalEpisodes, posterImgUrl, bannerImgUrl,
    //   episodes: [{episodeNumber, fileId, thumbnailImg, ...}]} }
    $d = $detail;
    $data   = data_get($d, 'data', $d);

    $title  = data_get($data, 'title') ?: data_get($data, 'dramaTitle') ?: 'DramaNova Drama';
    $cover  = data_get($data, 'posterImgUrl') ?: data_get($data, 'posterImg') ?: data_get($data, 'bannerImgUrl') ?: '';
    $desc   = data_get($data, 'description') ?: data_get($data, 'synopsis') ?: '';
    $eps    = data_get($data, 'totalEpisodes') ?: '';

    // Status
    $isCompleted = data_get($data, 'isCompleted', false);
    $statusLabel = $isCompleted ? 'Tamat' : 'Ongoing';

    // Categories
    $categories = data_get($data, 'categories', []);
    $categories = is_array($categories) ? $categories : [];
    $tagNames   = array_filter(array_map(fn($c) => is_array($c) ? ($c['name'] ?? '') : $c, $categories));

    // Episode list
    $epList = data_get($data, 'episodes', []);
    $epList = is_array($epList) ? $epList : [];
    $firstVid = data_get($epList[0] ?? [], 'fileId');
@endphp
@section('title', $title . ' — DramaNova — DramaCina')
@section('content')

<div class="detail-hero" style="--cover: url('{{ $cover }}')">
    <div class="detail-hero-inner">
        <div class="detail-poster-wrap">
            @if($cover)
                <img src="{{ $cover }}" alt="{{ $title }}" class="detail-poster-img">
            @else
                <div class="detail-poster-placeholder"><svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21,15 16,10 5,21"/></svg></div>
            @endif
        </div>
        <div class="detail-info">
            <span class="provider-badge badge-dramanova" style="position:static;display:inline-block;margin-bottom:0.75rem;">DRAMANOVA</span>
            <h1 class="detail-title">{{ $title }}</h1>
            <div class="detail-meta">
                @if($eps)
                <span class="meta-tag">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8m-4-4v4"/></svg>
                    {{ $eps }} Episode
                </span>
                @endif
                <span class="meta-tag">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    {{ $statusLabel }}
                </span>
                <span class="meta-tag">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><polygon points="23,7 16,12 23,17"/><rect x="1" y="5" width="15" height="14" rx="2"/></svg>
                    Drama Asia
                </span>
            </div>
            @if(count($tagNames) > 0)
            <div class="detail-tags">
                @foreach(array_slice($tagNames, 0, 8) as $tag)
                    <span class="detail-tag">{{ $tag }}</span>
                @endforeach
            </div>
            @endif
            @if($desc)
            <div class="detail-synopsis">
                <h3>Sinopsis</h3>
                <p id="synopsisText" class="synopsis-text clamped">{{ strip_tags($desc) }}</p>
                <button onclick="toggleSynopsis()" id="synopsisBtn" class="synopsis-toggle">Selengkapnya ▼</button>
            </div>
            @endif
            <div class="detail-actions">
                @if($firstVid)
                <a href="{{ route('dramanova.watch', ['id' => $dramaId, 'fid' => $firstVid, 'ep' => 1]) }}" class="btn btn-primary">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><polygon points="5,3 19,12 5,21"/></svg>
                    Tonton Episode 1
                </a>
                @endif
                <button id="wlBtn" class="btn-watchlist"
                    onclick="toggleWatchlist(this, {provider:'dramanova',drama_id:'{{ $dramaId }}',drama_title:{{ json_encode($title) }},drama_thumbnail:{{ json_encode($cover) }},total_episodes:{{ $eps ?: 'null' }}})">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
                    <span class="wl-text">+ Watchlist</span>
                </button>
            </div>
        </div>
    </div>
</div>

@if(count($epList) > 0)
<div class="detail-episodes-section">
    <h3 class="episodes-heading">Daftar Episode <span class="ep-count-badge">{{ count($epList) }}</span></h3>
    <div class="episode-grid">
        @foreach($epList as $ep)
            @php
                $fid = data_get($ep, 'fileId');
                $epNo = data_get($ep, 'episodeNumber') ?? ($loop->index + 1);
            @endphp
            @if($fid)
            <a href="{{ route('dramanova.watch', ['id' => $dramaId, 'fid' => $fid, 'ep' => $epNo]) }}" class="ep-btn">{{ $epNo }}</a>
            @endif
        @endforeach
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
@auth
fetch('{{ route("watchlist.check") }}?provider=dramanova&drama_id={{ $dramaId }}').then(r=>r.json()).then(res=>{
    if(res.in_watchlist){const b=document.getElementById('wlBtn');b?.classList.add('in-watchlist');const t=b?.querySelector('.wl-text');if(t)t.textContent='✓ Dalam Watchlist';}
});
@endauth

function toggleSynopsis() {
    const el = document.getElementById('synopsisText');
    const btn = document.getElementById('synopsisBtn');
    if (el.classList.toggle('clamped')) { btn.textContent = 'Selengkapnya ▼'; }
    else { btn.textContent = 'Sembunyikan ▲'; }
}
</script>
@endpush
