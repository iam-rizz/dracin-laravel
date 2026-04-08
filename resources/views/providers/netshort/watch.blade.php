@extends('layouts.app')
@php
    $epList = data_get($episodes, 'data', data_get($episodes, 'result', data_get($episodes, 'list', [])));
    $epList = is_array($epList) ? $epList : [];
    $currentEp = collect($epList)->firstWhere(fn($e) => (int)(data_get($e,'ep') ?? data_get($e,'episode') ?? data_get($e,'episodeNum') ?? 0) === (int)$episode);
    $streamUrl = $currentEp ? (data_get($currentEp,'url') ?? data_get($currentEp,'streamUrl') ?? data_get($currentEp,'videoUrl')) : null;
    $title = data_get($episodes, 'data.shortPlayName') ?? data_get($episodes, 'shortPlayName') ?? 'NetShort Drama';
    $cover = data_get($episodes, 'data.cover') ?? null;
@endphp
@section('title', $title . ' — Episode ' . $episode . ' — NetShort')
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
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:var(--gap-md);">
            <h2 style="font-size:1rem;color:var(--cream);">{{ $title }} — Episode {{ $episode }}</h2>
            <div style="display:flex;gap:var(--gap-sm);">
                @if($episode > 1)<a href="{{ route('netshort.watch', ['id' => $shortPlayId, 'ep' => $episode-1]) }}" class="btn btn-ghost" style="padding:0.375rem 0.75rem;font-size:0.8rem;">← Prev</a>@endif
                @if(count($epList) > $episode)<a href="{{ route('netshort.watch', ['id' => $shortPlayId, 'ep' => $episode+1]) }}" class="btn btn-ghost" style="padding:0.375rem 0.75rem;font-size:0.8rem;">Next →</a>@endif
            </div>
        </div>
        <a href="{{ route('netshort.detail', ['id' => $shortPlayId]) }}" class="btn btn-outline" style="font-size:0.8rem;padding:0.375rem 0.75rem;">← Detail Drama</a>
    </div>
    <div>
        <h3 style="font-size:0.9rem;color:var(--text-secondary);margin-bottom:var(--gap-md);">Daftar Episode</h3>
        <div class="episode-grid">
            @foreach($epList as $ep)
                @php $epNum = (int)(data_get($ep,'ep') ?? data_get($ep,'episode') ?? data_get($ep,'episodeNum') ?? 0); @endphp
                @if($epNum > 0)
                <a href="{{ route('netshort.watch', ['id' => $shortPlayId, 'ep' => $epNum]) }}"
                   class="ep-btn {{ $epNum === (int)$episode ? 'active' : '' }}">{{ $epNum }}</a>
                @endif
            @endforeach
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>saveHistory({provider:'netshort',drama_id:@json($shortPlayId),drama_title:@json($title),drama_thumbnail:@json($cover ?? ''),episode_number:{{ $episode }}});</script>
@endpush
