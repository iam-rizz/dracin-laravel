@extends('layouts.app')
@section('title', 'NetShort — Drama Series')
@section('content')
<div class="provider-header">
    <div class="provider-logo netshort">NS</div>
    <div><h1 style="font-size:1.5rem;margin-bottom:2px;">NetShort</h1><p style="color:var(--text-muted);font-size:0.8rem;">Short drama from NetShort</p></div>
</div>
<div class="tabs">
    <a href="{{ route('netshort.index', ['tab' => 'foryou']) }}" class="tab-btn {{ $tab === 'foryou' ? 'active' : '' }}">Untuk Kamu</a>
    <a href="{{ route('netshort.index', ['tab' => 'theaters']) }}" class="tab-btn {{ $tab === 'theaters' ? 'active' : '' }}">Theater</a>
</div>
@php
    // NetShort API shapes differ per tab:
    // foryou   → {contentType, contentInfos:[...]}             (single section dict)
    // theaters → [{contentName, contentInfos:[...]}, ...]      (list of sections)
    //            ApiService normalizes lists → wraps into ['data' => [...]]
    $rawData = $data;
    $items = [];

    // Check if theaters: ApiService wraps list into ['data' => [...sections]]
    $sections = data_get($rawData, 'data');
    if (is_array($sections) && array_is_list($sections) && isset($sections[0]['contentInfos'])) {
        // theaters: flatten all sections' contentInfos
        foreach ($sections as $section) {
            $infos = data_get($section, 'contentInfos', []);
            if (is_array($infos)) {
                $items = array_merge($items, $infos);
            }
        }
    } elseif (is_array($rawData) && array_is_list($rawData) && isset($rawData[0]['contentInfos'])) {
        // theaters without wrapping
        foreach ($rawData as $section) {
            $infos = data_get($section, 'contentInfos', []);
            if (is_array($infos)) {
                $items = array_merge($items, $infos);
            }
        }
    } else {
        // foryou: single section with contentInfos
        $items = data_get($rawData, 'contentInfos')
            ?? data_get($rawData, 'data.contentInfos')
            ?? [];
        $items = is_array($items) ? $items : [];
    }
@endphp
@if(count($items) > 0)
<div class="drama-grid">
    @foreach($items as $item)
        @php
            $id    = data_get($item, 'shortPlayId') ?? data_get($item, 'id');
            $title = data_get($item, 'shortPlayName') ?? data_get($item, 'title') ?? data_get($item, 'name');
            $thumb = data_get($item, 'shortPlayCover') ?? data_get($item, 'cover') ?? data_get($item, 'thumbnail');
        @endphp
        @if($id)
        <a href="{{ route('netshort.detail', ['id' => $id]) }}" class="drama-card" style="display:block;text-decoration:none;">
            <div class="drama-card-poster">
                @if($thumb)<img src="{{ $thumb }}" alt="{{ $title }}" loading="lazy">@endif
                <div class="overlay"></div><div class="play-btn"><svg width="18" height="18" fill="var(--cream)" viewBox="0 0 24 24"><polygon points="5,3 19,12 5,21"/></svg></div>
                <span class="provider-badge badge-netshort">NETSHORT</span>
            </div>
            <div class="drama-card-info"><div class="drama-card-title">{{ $title ?? 'Drama' }}</div></div>
        </a>
        @endif
    @endforeach
</div>
@else <div class="watchlist-empty"><p>Tidak ada data tersedia.</p></div>
@endif
@endsection
