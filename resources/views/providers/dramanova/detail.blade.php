@extends('layouts.app')
@php
    $d = $detail;
    $title = data_get($d, 'data.dramaTitle') ?? data_get($d, 'data.title') ?? data_get($d, 'dramaTitle') ?? 'Detail Drama';
    $cover = data_get($d, 'data.cover') ?? data_get($d, 'data.thumbnail') ?? data_get($d, 'data.image') ?? data_get($d, 'cover');
    $desc  = data_get($d, 'data.description') ?? data_get($d, 'data.intro') ?? '';
    // fileIds list for episodes
    $fileIds = data_get($d, 'data.fileIds', data_get($d, 'data.episodes', data_get($d, 'data.list', [])));
    $fileIds = is_array($fileIds) ? $fileIds : [];
    $eps = count($fileIds);
    $firstFile = count($fileIds) > 0 ? $fileIds[0] : null;
    $firstFid = is_array($firstFile) ? (data_get($firstFile, 'fileId') ?? data_get($firstFile, 'id')) : $firstFile;
@endphp
@section('title', $title . ' — DramaNova')
@section('content')
<div class="detail-layout">
    <div><div class="detail-poster">@if($cover)<img src="{{ $cover }}" alt="{{ $title }}" loading="eager">@endif</div></div>
    <div>
        <span class="provider-badge badge-dramanova" style="position:static;display:inline-block;margin-bottom:0.75rem;">DRAMANOVA</span>
        <h1 class="detail-title">{{ $title }}</h1>
        <div class="detail-meta">@if($eps)<span class="meta-tag">📺 {{ $eps }} Episode</span>@endif</div>
        @if($desc)<p class="detail-desc">{{ strip_tags($desc) }}</p>@endif
        <div class="detail-actions">
            @if($firstFid)
            <a href="{{ route('dramanova.watch', ['id' => $dramaId, 'fid' => $firstFid, 'ep' => 1]) }}" class="btn btn-primary">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><polygon points="5,3 19,12 5,21"/></svg>
                Tonton Episode 1
            </a>
            @endif
            <button id="wlBtn" class="btn-watchlist"
                onclick="toggleWatchlist(this, {provider:'dramanova',drama_id:'{{ $dramaId }}',drama_title:{{ json_encode($title) }},drama_thumbnail:{{ json_encode($cover) }},total_episodes:{{ $eps ?: 'null' }}})">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
                <span class="wl-text">Tambah Watchlist</span>
            </button>
        </div>
        @if(count($fileIds) > 0)
        <div style="margin-top:var(--gap-xl);">
            <h3 style="font-size:0.9rem;color:var(--text-secondary);margin-bottom:var(--gap-md);">Daftar Episode</h3>
            <div class="episode-grid">
                @foreach($fileIds as $i => $fid)
                    @php $fidVal = is_array($fid) ? (data_get($fid,'fileId') ?? data_get($fid,'id')) : $fid; @endphp
                    @if($fidVal)
                    <a href="{{ route('dramanova.watch', ['id' => $dramaId, 'fid' => $fidVal, 'ep' => $i+1]) }}"
                       class="ep-btn">{{ $i+1 }}</a>
                    @endif
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
