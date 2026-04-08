@extends('layouts.app')
@section('title', 'FreeReels — Drama Series')
@section('content')
<div class="provider-header">
    <div class="provider-logo freereels">FR</div>
    <div><h1 style="font-size:1.5rem;margin-bottom:2px;">FreeReels</h1><p style="color:var(--text-muted);font-size:0.8rem;">Short drama from FreeReels</p></div>
</div>
<div class="tabs">
    <a href="{{ route('freereels.index', ['tab' => 'foryou']) }}" class="tab-btn {{ $tab === 'foryou' ? 'active' : '' }}">Untuk Kamu</a>
    <a href="{{ route('freereels.index', ['tab' => 'homepage']) }}" class="tab-btn {{ $tab === 'homepage' ? 'active' : '' }}">Homepage</a>
</div>
@php
    // FreeReels API shapes differ per tab:
    // foryou   → {data: {items: [{key, cover, title, ...}]}}   (direct drama list)
    // homepage → {data: {items: [{type, items:[{key, cover, title}]}]}} (sections)
    $rawItems = data_get($data, 'data.items') ?? data_get($data, 'data') ?? [];
    $items = [];
    if (is_array($rawItems) && count($rawItems) > 0) {
        $first = $rawItems[0] ?? [];
        if (is_array($first) && isset($first['items']) && is_array($first['items'])) {
            // homepage: list of sections — flatten nested items
            foreach ($rawItems as $section) {
                $nested = data_get($section, 'items', []);
                if (is_array($nested)) {
                    $items = array_merge($items, $nested);
                }
            }
        } else {
            // foryou: flat list of dramas
            $items = $rawItems;
        }
    }
@endphp
@if(count($items) > 0)
<div class="drama-grid">
    @foreach($items as $item)
        @php
            $id    = data_get($item, 'key') ?? data_get($item, 'id') ?? data_get($item, 'bookId');
            $title = data_get($item, 'title') ?? data_get($item, 'name') ?? data_get($item, 'bookName');
            $thumb = data_get($item, 'cover') ?? data_get($item, 'thumbnail') ?? data_get($item, 'image');
        @endphp
        @if($id)
        <a href="{{ route('freereels.detail', ['id' => $id]) }}" class="drama-card" style="display:block;text-decoration:none;">
            <div class="drama-card-poster">
                @if($thumb)<img src="{{ $thumb }}" alt="{{ $title }}" loading="lazy">@endif
                <div class="overlay"></div><div class="play-btn"><svg width="18" height="18" fill="var(--cream)" viewBox="0 0 24 24"><polygon points="5,3 19,12 5,21"/></svg></div>
                <span class="provider-badge badge-freereels">FREEREELS</span>
            </div>
            <div class="drama-card-info"><div class="drama-card-title">{{ $title ?? 'Drama' }}</div></div>
        </a>
        @endif
    @endforeach
</div>
@else <div class="watchlist-empty"><p>Tidak ada data tersedia.</p></div>
@endif
@endsection
