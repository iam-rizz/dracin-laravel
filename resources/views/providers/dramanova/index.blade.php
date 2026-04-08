@extends('layouts.app')
@section('title', 'DramaNova — Drama Series')
@section('content')
<div class="provider-header">
    <div class="provider-logo dramanova">DN</div>
    <div><h1 style="font-size:1.5rem;margin-bottom:2px;">DramaNova</h1><p style="color:var(--text-muted);font-size:0.8rem;">Short drama from DramaNova</p></div>
</div>
@php
    // DramaNova: {total, rows: [{dramaId, title, posterImg, synopsis}]}
    $items = data_get($data, 'rows')
        ?? data_get($data, 'data.rows')
        ?? data_get($data, 'data')
        ?? [];
    $items = is_array($items) && array_is_list($items) ? $items : [];
@endphp
@if(count($items) > 0)
<div class="drama-grid">
    @foreach($items as $item)
        @php
            $id    = data_get($item, 'dramaId') ?? data_get($item, 'id');
            $title = data_get($item, 'title') ?? data_get($item, 'dramaTitle') ?? data_get($item, 'name');
            $thumb = data_get($item, 'posterImg') ?? data_get($item, 'cover') ?? data_get($item, 'thumbnail') ?? data_get($item, 'posterImgUrl');
        @endphp
        @if($id)
        <a href="{{ route('dramanova.detail', ['id' => $id]) }}" class="drama-card" style="display:block;text-decoration:none;">
            <div class="drama-card-poster">
                @if($thumb)<img src="{{ $thumb }}" alt="{{ $title }}" loading="lazy">@endif
                <div class="overlay"></div><div class="play-btn"><svg width="18" height="18" fill="var(--cream)" viewBox="0 0 24 24"><polygon points="5,3 19,12 5,21"/></svg></div>
                <span class="provider-badge badge-dramanova">DRAMANOVA</span>
            </div>
            <div class="drama-card-info"><div class="drama-card-title">{{ $title ?? 'Drama' }}</div></div>
        </a>
        @endif
    @endforeach
</div>
@else <div class="watchlist-empty"><p>Tidak ada data tersedia.</p></div>
@endif

{{-- Pagination --}}
<div style="display:flex;justify-content:center;gap:var(--gap-sm);margin-top:var(--gap-xl);">
    @if($page > 1)<a href="{{ route('dramanova.index', ['page' => $page-1]) }}" class="btn btn-ghost">← Sebelumnya</a>@endif
    <a href="{{ route('dramanova.index', ['page' => $page+1]) }}" class="btn btn-ghost">Selanjutnya →</a>
</div>
@endsection
