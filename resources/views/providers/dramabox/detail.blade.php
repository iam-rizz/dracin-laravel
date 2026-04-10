@extends('layouts.app')
@php
    $d = $detail;
    // DramaBox detail returns the object directly (not wrapped in 'data')
    $title  = data_get($d, 'data.bookName') ?? data_get($d, 'bookName') ?? 'Drama';
    $cover  = data_get($d, 'data.coverWap') ?? data_get($d, 'data.cover') ?? data_get($d, 'coverWap') ?? data_get($d, 'cover');
    $desc   = data_get($d, 'data.introduction') ?? data_get($d, 'introduction') ?? data_get($d, 'data.description') ?? '';
    $eps    = data_get($d, 'data.chapterCount') ?? data_get($d, 'chapterCount') ?? data_get($d, 'data.totalEpisodes') ?? '';
    $tags   = data_get($d, 'data.tags') ?? data_get($d, 'tags') ?? data_get($d, 'data.tagV3s') ?? [];
    if (!is_array($tags)) $tags = [];
    // Extract tag names if objects
    $tagNames = array_map(fn($t) => is_array($t) ? ($t['tagName'] ?? $t['name'] ?? '') : $t, $tags);
    $tagNames = array_filter($tagNames);
    $year   = data_get($d, 'data.shelfTime') ? substr(data_get($d, 'data.shelfTime'), 0, 4)
             : (data_get($d, 'data.year') ?? data_get($d, 'data.releaseYear') ?? '');
@endphp
@section('title', $title . ' — DramaBox — DramaCina')
@section('content')

{{-- Hero Banner --}}
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
            <span class="provider-badge badge-dramabox" style="position:static;display:inline-block;margin-bottom:0.75rem;">DRAMABOX</span>
            <h1 class="detail-title">{{ $title }}</h1>

            {{-- Meta Row --}}
            <div class="detail-meta">
                @if($eps)
                <span class="meta-tag">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8m-4-4v4"/></svg>
                    {{ $eps }} Episode
                </span>
                @endif
                @if($year)
                <span class="meta-tag">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                    {{ $year }}
                </span>
                @endif
                <span class="meta-tag">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><polygon points="23,7 16,12 23,17"/><rect x="1" y="5" width="15" height="14" rx="2"/></svg>
                    Drama Asia
                </span>
            </div>

            {{-- Tags / Genre --}}
            @if(count($tagNames) > 0)
            <div class="detail-tags">
                @foreach(array_slice($tagNames, 0, 6) as $tag)
                    <span class="detail-tag">{{ $tag }}</span>
                @endforeach
            </div>
            @endif

            {{-- Synopsis --}}
            @if($desc)
            <div class="detail-synopsis">
                <h3>Sinopsis</h3>
                <p id="synopsisText" class="synopsis-text clamped">{{ strip_tags($desc) }}</p>
                <button onclick="toggleSynopsis()" id="synopsisBtn" class="synopsis-toggle">Selengkapnya ▼</button>
            </div>
            @endif

            {{-- Actions --}}
            <div class="detail-actions">
                <a href="{{ route('dramabox.watch', ['id' => $bookId, 'ep' => 1]) }}" class="btn btn-primary">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><polygon points="5,3 19,12 5,21"/></svg>
                    Tonton Episode 1
                </a>
                <button id="wlBtn" class="btn-watchlist"
                    onclick="toggleWatchlist(this, {provider:'dramabox',drama_id:'{{ $bookId }}',drama_title:{{ json_encode($title) }},drama_thumbnail:{{ json_encode($cover) }},total_episodes:{{ $eps ?: 'null' }}})">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
                    <span class="wl-text">+ Watchlist</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Episode List --}}
@if($eps && (int)$eps > 0)
<div class="detail-episodes-section">
    <h3 class="episodes-heading">Daftar Episode <span class="ep-count-badge">{{ $eps }}</span></h3>
    <div class="episode-grid">
        @for($i = 1; $i <= min((int)$eps, 200); $i++)
            <a href="{{ route('dramabox.watch', ['id' => $bookId, 'ep' => $i]) }}" class="ep-btn">{{ $i }}</a>
        @endfor
        @if((int)$eps > 200)
            <span class="ep-btn" style="opacity:0.5;cursor:default;">+{{ (int)$eps - 200 }} lagi</span>
        @endif
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
@auth
fetch('{{ route("watchlist.check") }}?provider=dramabox&drama_id={{ $bookId }}').then(r=>r.json()).then(res=>{
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
