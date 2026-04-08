@extends('layouts.app')
@section('title', 'ShortMax — Drama Series')
@section('content')
<div class="provider-header">
    <div class="provider-logo shortmax">SM</div>
    <div><h1 style="font-size:1.5rem;margin-bottom:2px;">ShortMax</h1><p style="color:var(--text-muted);font-size:0.8rem;">Short drama from ShortMax</p></div>
</div>
<div class="tabs">
    <a href="{{ route('shortmax.index', ['tab' => 'foryou']) }}" class="tab-btn {{ $tab === 'foryou' ? 'active' : '' }}">Untuk Kamu</a>
    <a href="{{ route('shortmax.index', ['tab' => 'latest']) }}" class="tab-btn {{ $tab === 'latest' ? 'active' : '' }}">Terbaru</a>
</div>
@php
    // ShortMax: {status, page, results: [{shortPlayId, name, cover, totalEpisodes}]}
    $items = data_get($data, 'results')
        ?? data_get($data, 'data.results')
        ?? data_get($data, 'data.cell.books')
        ?? data_get($data, 'data')
        ?? [];
    $items = is_array($items) && array_is_list($items) ? $items : [];
@endphp
@if(count($items) > 0)
<div class="drama-grid">
    @foreach($items as $item)
        @php
            $id    = data_get($item, 'shortPlayId') ?? data_get($item, 'id');
            $title = data_get($item, 'name') ?? data_get($item, 'shortPlayName') ?? data_get($item, 'title');
            $thumb = data_get($item, 'cover') ?? data_get($item, 'thumbnail') ?? data_get($item, 'coverUrl');
        @endphp
        @if($id)
        <a href="{{ route('shortmax.detail', ['id' => $id]) }}" class="drama-card" style="display:block;text-decoration:none;">
            <div class="drama-card-poster">
                @if($thumb)<img src="{{ $thumb }}" alt="{{ $title }}" loading="lazy" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'200\' height=\'300\'%3E%3Crect fill=\'%231A1408\' width=\'200\' height=\'300\'/%3E%3C/svg%3E'">@endif
                <div class="overlay"></div><div class="play-btn"><svg width="18" height="18" fill="var(--cream)" viewBox="0 0 24 24"><polygon points="5,3 19,12 5,21"/></svg></div>
                <span class="provider-badge badge-shortmax">SHORTMAX</span>
            </div>
            <div class="drama-card-info"><div class="drama-card-title">{{ $title ?? 'Drama' }}</div></div>
        </a>
        @endif
    @endforeach
</div>
@else <div class="watchlist-empty"><p>Tidak ada data tersedia.</p></div>
@endif
@endsection
