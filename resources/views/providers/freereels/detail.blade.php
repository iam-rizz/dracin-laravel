@extends('layouts.app')
@php
    $d = $detail;
    $info    = data_get($d, 'data.info', data_get($d, 'info', []));
    $title   = data_get($info, 'name') ?? data_get($d, 'data.title') ?? data_get($d, 'title') ?? 'Drama';
    $cover   = data_get($info, 'cover') ?? data_get($d, 'data.cover') ?? data_get($d, 'cover');
    $desc    = data_get($info, 'desc') ?? data_get($d, 'data.description') ?? '';
    $epItems = data_get($info, 'episode_list', data_get($d, 'data.episodes', []));
    $epItems = is_array($epItems) ? $epItems : [];
    $eps     = count($epItems) ?: (data_get($info, 'episode_count') ?? 0);
    $tags    = data_get($info, 'content_tags') ?? data_get($info, 'tags') ?? [];
    if (!is_array($tags)) $tags = [];
    $tagNames = array_filter(array_map(fn($t) => is_array($t) ? ($t['name'] ?? '') : $t, $tags));
    $isFree  = data_get($info, 'free') ?? false;
    $follows = data_get($info, 'follow_count') ?? 0;
    $views   = data_get($info, 'comment_count') ?? 0;
    $finishStatus = data_get($info, 'finish_status'); // 2 = completed
@endphp
@section('title', $title . ' — FreeReels — DramaCina')
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
            <span class="provider-badge badge-freereels" style="position:static;display:inline-block;margin-bottom:0.75rem;">FREEREELS</span>
            <h1 class="detail-title">{{ $title }}</h1>
            <div class="detail-meta">
                @if($eps)<span class="meta-tag">📺 {{ $eps }} Episode</span>@endif
                @if($finishStatus == 2)<span class="meta-tag">✅ Tamat</span>@elseif($finishStatus)<span class="meta-tag">🔄 Ongoing</span>@endif
                @if($isFree)<span class="meta-tag" style="color:#4ade80;">🆓 Gratis</span>@endif
                @if($follows > 0)<span class="meta-tag">❤️ {{ number_format($follows) }}</span>@endif
            </div>
            @if(count($tagNames) > 0)
            <div class="detail-tags">
                @foreach(array_slice($tagNames, 0, 6) as $tag)
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
                <a href="{{ route('freereels.watch', ['id' => $key, 'ep' => 1]) }}" class="btn btn-primary">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><polygon points="5,3 19,12 5,21"/></svg>
                    Tonton Episode 1
                </a>
                <button id="wlBtn" class="btn-watchlist"
                    onclick="toggleWatchlist(this, {provider:'freereels',drama_id:'{{ $key }}',drama_title:{{ json_encode($title) }},drama_thumbnail:{{ json_encode($cover) }},total_episodes:{{ $eps ?: 'null' }}})">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
                    <span class="wl-text">+ Watchlist</span>
                </button>
            </div>
        </div>
    </div>
</div>

@if(count($epItems) > 0)
<div class="detail-episodes-section">
    <h3 class="episodes-heading">Daftar Episode <span class="ep-count-badge">{{ $eps }}</span></h3>
    <div class="episode-grid">
        @foreach($epItems as $i => $ep)
            <a href="{{ route('freereels.watch', ['id' => $key, 'ep' => $i+1]) }}" class="ep-btn">{{ $i+1 }}</a>
        @endforeach
    </div>
</div>
@endif

@endsection
@push('scripts')
<script>
@auth
fetch('{{ route("watchlist.check") }}?provider=freereels&drama_id={{ $key }}').then(r=>r.json()).then(res=>{
    if(res.in_watchlist){const b=document.getElementById('wlBtn');b?.classList.add('in-watchlist');const t=b?.querySelector('.wl-text');if(t)t.textContent='✓ Dalam Watchlist';}
});
@endauth
function toggleSynopsis(){const el=document.getElementById('synopsisText');const btn=document.getElementById('synopsisBtn');if(el.classList.toggle('clamped')){btn.textContent='Selengkapnya ▼';}else{btn.textContent='Sembunyikan ▲';}}
</script>
@endpush
