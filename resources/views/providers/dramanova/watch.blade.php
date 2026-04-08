@extends('layouts.app')
@php
    $d = $detail;
    $title = data_get($d, 'data.dramaTitle') ?? data_get($d, 'data.title') ?? 'DramaNova Drama';
    $cover = data_get($d, 'data.cover') ?? data_get($d, 'cover');
    $streamUrl = data_get($videoData, 'data.url') ?? data_get($videoData, 'data.videoUrl') ?? data_get($videoData, 'url') ?? null;
    $fileIds = data_get($d, 'data.fileIds', data_get($d, 'data.episodes', data_get($d, 'data.list', [])));
    $fileIds = is_array($fileIds) ? $fileIds : [];
@endphp
@section('title', $title . ' — Episode ' . $episode . ' — DramaNova')
@section('content')
<div class="watch-layout">
    <div>
        <div class="player-container" id="playerContainer">
            @if($streamUrl)
                <video controls autoplay playsinline style="width:100%;height:100%;" src="{{ $streamUrl }}"></video>
            @elseif($fileId)
                <div class="player-loading" id="playerLoading">
                    <div class="spinner"></div>
                    <span class="spinner-text">Memuat video...</span>
                </div>
            @else
                <div class="player-loading"><p style="color:var(--text-muted);">Video tidak tersedia.</p></div>
            @endif
        </div>
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:var(--gap-md);">
            <h2 style="font-size:1rem;color:var(--cream);">{{ $title }} — Episode {{ $episode }}</h2>
        </div>
        <a href="{{ route('dramanova.detail', ['id' => $dramaId]) }}" class="btn btn-outline" style="font-size:0.8rem;padding:0.375rem 0.75rem;">← Detail Drama</a>
    </div>
    <div>
        <h3 style="font-size:0.9rem;color:var(--text-secondary);margin-bottom:var(--gap-md);">Daftar Episode</h3>
        <div class="episode-grid">
            @foreach($fileIds as $i => $fid)
                @php $fidVal = is_array($fid) ? (data_get($fid,'fileId') ?? data_get($fid,'id')) : $fid; @endphp
                @if($fidVal)
                <a href="{{ route('dramanova.watch', ['id' => $dramaId, 'fid' => $fidVal, 'ep' => $i+1]) }}"
                   class="ep-btn {{ ($i+1) === (int)$episode ? 'active' : '' }}">{{ $i+1 }}</a>
                @endif
            @endforeach
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
@if(!$streamUrl && $fileId)
// Load video dynamically
fetch('{{ route("dramanova.getvideo") }}?fid={{ $fileId }}')
    .then(r => r.json())
    .then(data => {
        const url = data?.data?.url ?? data?.url ?? null;
        if (url) {
            const vid = document.createElement('video');
            vid.controls = true; vid.autoplay = true; vid.playsInline = true;
            vid.style.cssText = 'width:100%;height:100%;';
            vid.src = url;
            document.getElementById('playerLoading').replaceWith(vid);
        }
    });
@endif
saveHistory({provider:'dramanova',drama_id:@json($dramaId),drama_title:@json($title),drama_thumbnail:@json($cover ?? ''),episode_number:{{ $episode }}});
</script>
@endpush
