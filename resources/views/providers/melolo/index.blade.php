@extends('layouts.app')
@section('title', 'Melolo — Drama Series')
@section('content')
<div class="provider-header">
    <div class="provider-logo melolo">ML</div>
    <div><h1 style="font-size:1.5rem;margin-bottom:2px;">Melolo</h1><p style="color:var(--text-muted);font-size:0.8rem;">Short drama from Melolo</p></div>
</div>
<div class="tabs">
    <a href="{{ route('melolo.index', ['tab' => 'foryou']) }}" class="tab-btn {{ $tab === 'foryou' ? 'active' : '' }}">Untuk Kamu</a>
    <a href="{{ route('melolo.index', ['tab' => 'trending']) }}" class="tab-btn {{ $tab === 'trending' ? 'active' : '' }}">Trending</a>
    <a href="{{ route('melolo.index', ['tab' => 'latest']) }}" class="tab-btn {{ $tab === 'latest' ? 'active' : '' }}">Terbaru</a>
</div>
@php
    // Melolo API response shapes differ per tab:
    // foryou   → {api_version, data: {cell: {books:[...]}}}
    // latest   → {books:[...], algo, has_more, ...}  (cell returned directly)
    // trending → {books:[...], algo, has_more, ...}  (cell returned directly)
    $items = data_get($data, 'data.cell.books')   // foryou
        ?? data_get($data, 'books')                // latest / trending
        ?? data_get($data, 'data.books')
        ?? [];
    $items = is_array($items) && array_is_list($items) ? $items : [];
@endphp
@if(count($items) > 0)
<div class="drama-grid">
    @foreach($items as $item)
        @php
            $id    = data_get($item, 'book_id') ?? data_get($item, 'bookId') ?? data_get($item, 'id');
            $title = data_get($item, 'book_name') ?? data_get($item, 'bookName') ?? data_get($item, 'title');
            $thumb = data_get($item, 'thumb_url') ?? data_get($item, 'cover') ?? data_get($item, 'thumbnail') ?? data_get($item, 'first_chapter_cover');
            if ($thumb && str_contains(strtolower($thumb), '.heic')) {
                $thumb = 'https://wsrv.nl/?url=' . urlencode($thumb) . '&output=webp';
            }
        @endphp
        @if($id)
        <a href="{{ route('melolo.detail', ['id' => $id]) }}" class="drama-card" style="display:block;text-decoration:none;">
            <div class="drama-card-poster">
                @if($thumb)<img src="{{ $thumb }}" alt="{{ $title }}" loading="lazy">@endif
                <div class="overlay"></div><div class="play-btn"><svg width="18" height="18" fill="var(--cream)" viewBox="0 0 24 24"><polygon points="5,3 19,12 5,21"/></svg></div>
                <span class="provider-badge badge-melolo">MELOLO</span>
            </div>
            <div class="drama-card-info"><div class="drama-card-title">{{ $title ?? 'Drama' }}</div></div>
        </a>
        @endif
    @endforeach
</div>
@else <div class="watchlist-empty"><p>Tidak ada data tersedia.</p></div>
@endif
@endsection
