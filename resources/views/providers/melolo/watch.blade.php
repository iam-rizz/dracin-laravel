@extends('layouts.app')
@php
    $d = $detail;
    $title = data_get($d, 'data.title') ?? data_get($d, 'title') ?? 'Melolo Drama';
    $cover = data_get($d, 'data.cover') ?? data_get($d, 'cover');
    $streamUrl = data_get($streamData, 'data.url') ?? data_get($streamData, 'data.videoUrl') ?? data_get($streamData, 'url') ?? null;

    // All episodes for sidebar
    $epItems = data_get($d, 'data.episodes', data_get($d, 'data.chapterList', data_get($d, 'data.list', [])));
    $epItems = is_array($epItems) ? $epItems : [];
@endphp
@section('title', $title . ' — Episode ' . $episode . ' — Melolo')
@section('content')
<div class="watch-layout">
    <div>
        <div class="player-container">
            @if($streamUrl)
                <video controls autoplay playsinline style="width:100%;height:100%;" src="{{ $streamUrl }}"></video>
            @else
                <div class="player-loading"><div class="spinner"></div><span class="spinner-text">Video tidak tersedia.</span></div>
            @endif
        </div>
        <h2 style="font-size:1rem;color:var(--cream);margin-bottom:var(--gap-md);">{{ $title }} — Episode {{ $episode }}</h2>
        <a href="{{ route('melolo.detail', ['id' => $bookId]) }}" class="btn btn-outline" style="font-size:0.8rem;padding:0.375rem 0.75rem;">← Detail Drama</a>
    </div>
    <div>
        <h3 style="font-size:0.9rem;color:var(--text-secondary);margin-bottom:var(--gap-md);">Daftar Episode</h3>
        <div class="episode-grid">
            @foreach($epItems as $i => $ep)
                @php $vid = data_get($ep,'vid') ?? data_get($ep,'videoId') ?? data_get($ep,'id'); $epNum = $i + 1; @endphp
                @if($vid)
                <a href="{{ route('melolo.watch', ['id' => $bookId, 'vid' => $vid, 'ep' => $epNum]) }}"
                   class="ep-btn {{ $epNum === (int)$episode ? 'active' : '' }}">{{ $epNum }}</a>
                @endif
            @endforeach
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>saveHistory({provider:'melolo',drama_id:@json($bookId),drama_title:@json($title),drama_thumbnail:@json($cover ?? ''),episode_number:{{ $episode }}});</script>
@endpush
