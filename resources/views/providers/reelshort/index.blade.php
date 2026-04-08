@extends('layouts.app')
@section('title', 'ReelShort — Drama Series')
@section('content')
<div class="provider-header">
    <div class="provider-logo reelshort">RS</div>
    <div><h1 style="font-size:1.5rem;margin-bottom:2px;">ReelShort</h1><p style="color:var(--text-muted);font-size:0.8rem;">Short drama from ReelShort</p></div>
</div>
<div class="tabs">
    <a href="{{ route('reelshort.index', ['tab' => 'foryou']) }}" class="tab-btn {{ $tab === 'foryou' ? 'active' : '' }}">Untuk Kamu</a>
    <a href="{{ route('reelshort.index', ['tab' => 'homepage']) }}" class="tab-btn {{ $tab === 'homepage' ? 'active' : '' }}">Homepage</a>
</div>
@php
    // ReelShort: {success, data: {lists: [{book_id, book_title, book_pic}]}}
    $items = data_get($data, 'data.lists')
        ?? (is_array(data_get($data, 'data')) && array_is_list(data_get($data, 'data')) ? data_get($data, 'data') : null)
        ?? [];
    $items = is_array($items) ? $items : [];
@endphp
@if(count($items) > 0)
<div class="drama-grid">
    @foreach($items as $item)
        @php
            $id    = data_get($item, 'book_id') ?? data_get($item, 'bookId') ?? data_get($item, 'id');
            $title = data_get($item, 'book_title') ?? data_get($item, 'bookName') ?? data_get($item, 'title');
            $thumb = data_get($item, 'book_pic') ?? data_get($item, 'cover') ?? data_get($item, 'thumbnail');
        @endphp
        @if($id)
        <a href="{{ route('reelshort.detail', ['id' => $id]) }}" class="drama-card" style="display:block;text-decoration:none;">
            <div class="drama-card-poster">
                @if($thumb)<img src="{{ $thumb }}" alt="{{ $title }}" loading="lazy" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'200\' height=\'300\'%3E%3Crect fill=\'%231A1408\' width=\'200\' height=\'300\'/%3E%3C/svg%3E'">@endif
                <div class="overlay"></div><div class="play-btn"><svg width="18" height="18" fill="var(--cream)" viewBox="0 0 24 24"><polygon points="5,3 19,12 5,21"/></svg></div>
                <span class="provider-badge badge-reelshort">REELSHORT</span>
            </div>
            <div class="drama-card-info"><div class="drama-card-title">{{ $title ?? 'Drama' }}</div></div>
        </a>
        @endif
    @endforeach
</div>
@else
<div class="watchlist-empty"><p>Tidak ada data tersedia.</p></div>
@endif
@endsection
