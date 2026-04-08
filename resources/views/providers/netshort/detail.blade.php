@extends('layouts.app')
@php
    // NetShort allEpisodes returns the object directly (not wrapped in 'data'):
    // { shortPlayId, shortPlayName, shortPlayCover, shotIntroduce,
    //   totalEpisode, isFinish, shortPlayLabels:[],
    //   shortPlayEpisodeInfos:[{episodeId, episodeNo, playVoucher, ...}] }
    $d = $detail;

    // If API wrapped in 'data', unwrap; else use root
    $root  = data_get($d, 'data', $d);

    $title = data_get($root, 'shortPlayName') ?? data_get($root, 'title') ?? 'Drama';
    $cover = data_get($root, 'shortPlayCover') ?? data_get($root, 'cover') ?? data_get($root, 'thumbnail') ?? '';
    $desc  = data_get($root, 'shotIntroduce') ?? data_get($root, 'description') ?? data_get($root, 'introduction') ?? '';
    $totalEp = data_get($root, 'totalEpisode') ?? data_get($root, 'totalEpisodes') ?? '';
    $isFinish = data_get($root, 'isFinish');
    $statusLabel = match(true) {
        $isFinish === 1 || $isFinish === true || $isFinish === '1' => 'Tamat',
        $isFinish === 0 || $isFinish === false || $isFinish === '0' => 'Ongoing',
        default => ''
    };

    // Tags / labels
    $labels = data_get($root, 'shortPlayLabels') ?? [];
    if (is_string($labels)) {
        // sometimes encoded as JSON string e.g. '["Romance","Holiday"]'
        $decoded = json_decode($labels, true);
        $labels = is_array($decoded) ? $decoded : array_map('trim', explode(',', $labels));
    }
    if (!is_array($labels)) $labels = [];
    $tagNames = array_filter($labels);

    // Episodes list
    $epItems = data_get($root, 'shortPlayEpisodeInfos') ?? [];
    $epItems = is_array($epItems) ? $epItems : [];
@endphp
@section('title', $title . ' — NetShort — DramaCina')
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
            <span class="provider-badge badge-netshort" style="position:static;display:inline-block;margin-bottom:0.75rem;">NETSHORT</span>
            <h1 class="detail-title">{{ $title }}</h1>
            <div class="detail-meta">
                @if($totalEp)
                <span class="meta-tag">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8m-4-4v4"/></svg>
                    {{ $totalEp }} Episode
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
                <a href="{{ route('netshort.watch', ['id' => $shortPlayId, 'ep' => 1]) }}" class="btn btn-primary">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><polygon points="5,3 19,12 5,21"/></svg>
                    Tonton Episode 1
                </a>
                <button id="wlBtn" class="btn-watchlist"
                    onclick="toggleWatchlist(this, {provider:'netshort',drama_id:'{{ $shortPlayId }}',drama_title:{{ json_encode($title) }},drama_thumbnail:{{ json_encode($cover) }},total_episodes:{{ $totalEp ?: 'null' }}})">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
                    <span class="wl-text">+ Watchlist</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Episode List --}}
@if(count($epItems) > 0)
<div class="detail-episodes-section">
    <h3 class="episodes-heading">Daftar Episode <span class="ep-count-badge">{{ count($epItems) }}</span></h3>
    <div class="episode-grid">
        @foreach($epItems as $ep)
            @php $epNo = data_get($ep, 'episodeNo'); @endphp
            @if($epNo)
            <a href="{{ route('netshort.watch', ['id' => $shortPlayId, 'ep' => $epNo]) }}" class="ep-btn">{{ $epNo }}</a>
            @endif
        @endforeach
    </div>
</div>
@elseif($totalEp && (int)$totalEp > 0)
<div class="detail-episodes-section">
    <h3 class="episodes-heading">Daftar Episode <span class="ep-count-badge">{{ $totalEp }}</span></h3>
    <div class="episode-grid">
        @for($i = 1; $i <= min((int)$totalEp, 200); $i++)
            <a href="{{ route('netshort.watch', ['id' => $shortPlayId, 'ep' => $i]) }}" class="ep-btn">{{ $i }}</a>
        @endfor
        @if((int)$totalEp > 200)<span class="ep-btn" style="opacity:0.5;cursor:default;">+{{ (int)$totalEp - 200 }} lagi</span>@endif
    </div>
</div>
@endif

@endsection
@push('scripts')
<script>
@auth
fetch('{{ route("watchlist.check") }}?provider=netshort&drama_id={{ $shortPlayId }}').then(r=>r.json()).then(res=>{
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
