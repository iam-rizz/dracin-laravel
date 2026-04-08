@extends('layouts.app')
@php
    // Melolo detail API:
    // { data: { video_data: {
    //     series_title, series_cover, series_intro, series_status, episode_cnt,
    //     category_schema (JSON string of [{name,...}]),
    //     video_list: [{ vid, vid_index, cover, episode_cover, title, series_id }]
    // }}}
    $d = $detail;
    $vid = data_get($d, 'data.video_data', []);

    $title  = data_get($vid, 'series_title') ?? data_get($d, 'data.title') ?? data_get($d, 'title') ?? 'Drama';
    $cover  = data_get($vid, 'series_cover') ?? data_get($d, 'data.cover') ?? data_get($d, 'cover') ?? '';
    $desc   = data_get($vid, 'series_intro') ?? data_get($d, 'data.description') ?? data_get($d, 'data.abstract') ?? '';
    $eps    = data_get($vid, 'episode_cnt') ?? data_get($d, 'data.totalEpisodes') ?? '';
    $sStatus = data_get($vid, 'series_status');  // 1 = ongoing, 2 = complete (typical)
    $statusLabel = match(true) {
        in_array($sStatus, [1, '1'])  => 'Ongoing',
        in_array($sStatus, [2, '2'])  => 'Tamat',
        default => ''
    };

    // Categories from JSON string
    $catRaw = data_get($vid, 'category_schema', '[]');
    if (is_string($catRaw)) {
        $catArr = json_decode($catRaw, true) ?? [];
    } else {
        $catArr = is_array($catRaw) ? $catRaw : [];
    }
    $tagNames = array_filter(array_map(fn($c) => is_array($c) ? ($c['name'] ?? '') : $c, $catArr));

    // Episode list from video_list [{vid, vid_index}]
    $epItems = data_get($vid, 'video_list', []);
    $epItems = is_array($epItems) ? $epItems : [];
    $firstVid = data_get($epItems[0] ?? [], 'vid');
@endphp
@section('title', $title . ' — Melolo — DramaCina')
@section('content')

<div class="detail-hero" style="--cover: url('{{ $cover }}')">
    <div class="detail-hero-inner">
        <div class="detail-poster-wrap">
            @if($cover)
                <img src="{{ $cover }}" alt="{{ $title }}" class="detail-poster-img">
            @else
                <div class="detail-poster-placeholder"><svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><polyline points="21,15 16,10 5,21"/></svg></div>
            @endif
        </div>
        <div class="detail-info">
            <span class="provider-badge badge-melolo" style="position:static;display:inline-block;margin-bottom:0.75rem;">MELOLO</span>
            <h1 class="detail-title">{{ $title }}</h1>
            <div class="detail-meta">
                @if($eps)
                <span class="meta-tag">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8m-4-4v4"/></svg>
                    {{ $eps }} Episode
                </span>
                @endif
                @if($statusLabel)
                <span class="meta-tag">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    {{ $statusLabel }}
                </span>
                @endif
                <span class="meta-tag">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
                    Melolo
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
                <a href="{{ route('melolo.watch', ['id' => $bookId, 'vid' => $firstVid ?? 'auto', 'ep' => 1]) }}" class="btn btn-primary">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><polygon points="5,3 19,12 5,21"/></svg>
                    Tonton Episode 1
                </a>
                <button id="wlBtn" class="btn-watchlist"
                    onclick="toggleWatchlist(this, {provider:'melolo',drama_id:'{{ $bookId }}',drama_title:{{ json_encode($title) }},drama_thumbnail:{{ json_encode($cover) }},total_episodes:{{ $eps ?: 'null' }}})">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
                    <span class="wl-text">+ Watchlist</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Episode list from video_list --}}
@if(count($epItems) > 0)
<div class="detail-episodes-section">
    <h3 class="episodes-heading">Daftar Episode <span class="ep-count-badge">{{ count($epItems) }}</span></h3>
    <div class="episode-grid">
        @foreach($epItems as $ep)
            @php
                $vid2 = data_get($ep, 'vid');
                $epNo = data_get($ep, 'vid_index') ?? ($loop->index + 1);
            @endphp
            @if($vid2)
            <a href="{{ route('melolo.watch', ['id' => $bookId, 'vid' => $vid2, 'ep' => $epNo]) }}" class="ep-btn">{{ $epNo }}</a>
            @endif
        @endforeach
    </div>
</div>
@elseif($eps && (int)$eps > 0)
<div class="detail-episodes-section">
    <h3 class="episodes-heading">Daftar Episode <span class="ep-count-badge">{{ $eps }}</span></h3>
    <div class="episode-grid">
        @for($i = 1; $i <= min((int)$eps, 100); $i++)
            <a href="{{ route('melolo.watch', ['id' => $bookId, 'vid' => 'auto', 'ep' => $i]) }}" class="ep-btn">{{ $i }}</a>
        @endfor
        @if((int)$eps > 100)<span class="ep-btn" style="opacity:0.5;cursor:default;">+{{ (int)$eps - 100 }} lagi</span>@endif
    </div>
</div>
@endif

@endsection
@push('scripts')
<script>
@auth
fetch('{{ route("watchlist.check") }}?provider=melolo&drama_id={{ $bookId }}').then(r=>r.json()).then(res=>{
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
