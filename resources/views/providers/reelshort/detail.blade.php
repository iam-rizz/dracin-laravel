@extends('layouts.app')
@php
    $d = $detail;
    $title   = data_get($d, 'data.book_title') ?? data_get($d, 'data.title') ?? data_get($d, 'data.bookName') ?? data_get($d, 'book_title') ?? 'Drama';
    $cover   = data_get($d, 'data.book_pic') ?? data_get($d, 'data.cover') ?? data_get($d, 'data.thumbnail') ?? data_get($d, 'cover');
    $desc    = data_get($d, 'data.description') ?? data_get($d, 'data.introduction') ?? data_get($d, 'data.intro') ?? data_get($d, 'data.synopsis') ?? '';
    $eps     = data_get($d, 'data.total_episodes') ?? data_get($d, 'data.totalEpisodes') ?? data_get($d, 'data.episode_count') ?? data_get($d, 'data.episodeCount') ?? '';
    $tags    = data_get($d, 'data.tags') ?? data_get($d, 'data.categories') ?? data_get($d, 'data.book_tags') ?? [];
    if (is_string($tags)) $tags = array_map('trim', explode(',', $tags));
    if (!is_array($tags)) $tags = [];
    $tagNames = array_filter(array_map(fn($t) => is_array($t) ? ($t['name'] ?? $t['tagName'] ?? '') : $t, $tags));
    $lang    = data_get($d, 'data.language') ?? data_get($d, 'data.lang') ?? '';
    $status  = data_get($d, 'data.status') ?? data_get($d, 'data.book_status') ?? '';
    $statusLabel = match(true) {
        in_array($status, ['completed','tamat','2',2]) => 'Tamat',
        in_array($status, ['ongoing','1',1,'0',0])    => 'Ongoing',
        default => $status
    };
@endphp
@section('title', $title . ' — ReelShort — DramaCina')
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
            <span class="provider-badge badge-reelshort" style="position:static;display:inline-block;margin-bottom:0.75rem;">REELSHORT</span>
            <h1 class="detail-title">{{ $title }}</h1>
            <div class="detail-meta">
                @if($eps)
                <span class="meta-tag">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8m-4-4v4"/></svg>
                    {{ $eps }} Episode
                </span>
                @endif
                @if($lang)
                <span class="meta-tag">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                    {{ $lang }}
                </span>
                @endif
                @if($statusLabel)
                <span class="meta-tag">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    {{ $statusLabel }}
                </span>
                @endif
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
                <a href="{{ route('reelshort.watch', ['id' => $bookId, 'ep' => 1]) }}" class="btn btn-primary">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><polygon points="5,3 19,12 5,21"/></svg>
                    Tonton Episode 1
                </a>
                <button id="wlBtn" class="btn-watchlist"
                    onclick="toggleWatchlist(this, {provider:'reelshort',drama_id:'{{ $bookId }}',drama_title:{{ json_encode($title) }},drama_thumbnail:{{ json_encode($cover) }},total_episodes:{{ $eps ?: 'null' }}})">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
                    <span class="wl-text">+ Watchlist</span>
                </button>
            </div>
        </div>
    </div>
</div>

@if($eps && (int)$eps > 0)
<div class="detail-episodes-section">
    <h3 class="episodes-heading">Daftar Episode <span class="ep-count-badge">{{ $eps }}</span></h3>
    <div class="episode-grid">
        @for($i = 1; $i <= min((int)$eps, 200); $i++)
            <a href="{{ route('reelshort.watch', ['id' => $bookId, 'ep' => $i]) }}" class="ep-btn">{{ $i }}</a>
        @endfor
        @if((int)$eps > 200)<span class="ep-btn" style="opacity:0.5;cursor:default;">+{{ (int)$eps - 200 }} lagi</span>@endif
    </div>
</div>
@endif

@endsection
@push('scripts')
<script>
@auth
fetch('{{ route("watchlist.check") }}?provider=reelshort&drama_id={{ $bookId }}').then(r=>r.json()).then(res=>{
    if(res.in_watchlist){const b=document.getElementById('wlBtn');b?.classList.add('in-watchlist');const t=b?.querySelector('.wl-text');if(t)t.textContent='✓ Dalam Watchlist';}
});
@endauth
function toggleSynopsis(){
    const el=document.getElementById('synopsisText');
    const btn=document.getElementById('synopsisBtn');
    if(el.classList.toggle('clamped')){btn.textContent='Selengkapnya ▼';}
    else{btn.textContent='Sembunyikan ▲';}
}
</script>
@endpush
