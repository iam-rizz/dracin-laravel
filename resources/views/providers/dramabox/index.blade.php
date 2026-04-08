@extends('layouts.app')

@section('title', 'DramaBox — Drama Series Asia')

@section('content')

<div class="provider-header">
    <div class="provider-logo dramabox">DB</div>
    <div>
        <h1 style="font-size:1.5rem;margin-bottom:2px;">DramaBox</h1>
        <p style="color:var(--text-muted);font-size:0.8rem;">Short drama series from DramaBox</p>
    </div>
</div>

<div class="tabs">
    @foreach([
        ['foryou',   'Untuk Kamu'],
        ['trending', 'Trending'],
        ['latest',   'Terbaru'],
        ['dubindo',  'Dub Indo'],
    ] as [$key, $label])
        <a href="{{ route('dramabox.index', ['tab' => $key]) }}"
           class="tab-btn {{ $tab === $key ? 'active' : '' }}">{{ $label }}</a>
    @endforeach
</div>

@php
    $items = data_get($data, 'data', data_get($data, 'result', data_get($data, 'list', [])));
    $items = is_array($items) ? $items : [];
@endphp

@if(count($items) > 0)
<div class="drama-grid">
    @foreach($items as $item)
        @php
            $id = data_get($item, 'bookId') ?? data_get($item, 'id');
            $title = data_get($item, 'bookName') ?? data_get($item, 'title') ?? data_get($item, 'name');
            $thumb = data_get($item, 'cover') ?? data_get($item, 'coverWap') ?? data_get($item, 'thumbnail') ?? data_get($item, 'coverUrl');
            $eps = data_get($item, 'totalEpisodes') ?? data_get($item, 'chapterCount') ?? data_get($item, 'episodeCount');
        @endphp
        @if($id)
        <a href="{{ route('dramabox.detail', ['id' => $id]) }}" class="drama-card" style="display:block;text-decoration:none;">
            <div class="drama-card-poster">
                @if($thumb)
                    <img src="{{ $thumb }}" alt="{{ $title }}" loading="lazy"
                         onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'200\' height=\'300\'%3E%3Crect fill=\'%231A1408\' width=\'200\' height=\'300\'/%3E%3C/svg%3E'">
                @endif
                <div class="overlay"></div>
                <div class="play-btn"><svg width="18" height="18" fill="var(--cream)" viewBox="0 0 24 24"><polygon points="5,3 19,12 5,21"/></svg></div>
                <span class="provider-badge badge-dramabox">DRAMABOX</span>
            </div>
            <div class="drama-card-info">
                <div class="drama-card-title">{{ $title ?? 'Drama' }}</div>
                @if($eps)<div class="drama-card-meta">{{ $eps }} Episode</div>@endif
            </div>
        </a>
        @endif
    @endforeach
</div>
@else
<div class="watchlist-empty">
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4"/></svg>
    <h3>Tidak Ada Data</h3>
    <p>Gagal memuat drama dari DramaBox. Coba lagi nanti.</p>
</div>
@endif

@endsection
