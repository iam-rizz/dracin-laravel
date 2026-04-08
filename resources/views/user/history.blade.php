@extends('layouts.app')
@section('title', 'Riwayat Nonton — DramaCina')
@section('content')

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:var(--gap-xl);">
        <h1 class="section-title">Riwayat Nonton</h1>
        <div style="display:flex;gap:var(--gap-sm);">
            <a href="{{ route('watchlist.index') }}" class="btn btn-ghost" style="font-size:0.875rem;">Watchlist</a>
            @if($history->isNotEmpty())
                <form method="POST" action="{{ route('history.clear') }}" onsubmit="return confirm('Hapus semua riwayat?')">
                    @csrf
                    <button type="submit" class="btn btn-outline"
                        style="font-size:0.8rem;padding:0.375rem 0.75rem;color:var(--text-muted);">🗑️ Hapus Semua</button>
                </form>
            @endif
        </div>
    </div>

    @if($history->isEmpty())
        <div class="watchlist-empty">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="64"
                height="64">
                <circle cx="12" cy="12" r="10" />
                <polyline points="12 6 12 12 16 14" />
            </svg>
            <h3>Belum Ada Riwayat</h3>
            <p>Mulai nonton drama untuk melihat riwayat di sini.</p>
            <a href="{{ route('home') }}" class="btn btn-primary" style="margin-top:var(--gap-lg);">Jelajahi Drama</a>
        </div>
    @else
        <div style="display:flex;flex-direction:column;gap:var(--gap-md);">
            @foreach($history as $item)
                @php
                    $routes = [
                        'dramabox' => ['route' => 'dramabox.watch', 'id' => $item->drama_id],
                        'reelshort' => ['route' => 'reelshort.watch', 'id' => $item->drama_id],
                        'shortmax' => ['route' => 'shortmax.watch', 'id' => $item->drama_id],
                        'netshort' => ['route' => 'netshort.watch', 'id' => $item->drama_id],
                        'melolo' => ['route' => 'melolo.detail', 'id' => $item->drama_id],
                        'freereels' => ['route' => 'freereels.watch', 'id' => $item->drama_id],
                        'dramanova' => ['route' => 'dramanova.detail', 'id' => $item->drama_id],
                    ];
                    $r = $routes[$item->provider] ?? null;
                    $watchUrl = $r ? route($r['route'], ['id' => $r['id'], 'ep' => $item->episode_number]) : '#';
                @endphp
                <div class="history-card">
                    <div class="history-thumb">
                        @if($item->drama_thumbnail)
                            <img src="{{ $item->drama_thumbnail }}" alt="{{ $item->drama_title }}" loading="lazy">
                        @else
                            <div style="width:100%;height:100%;background:var(--bg-card2);"></div>
                        @endif
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:var(--gap-sm);">
                            <div style="min-width:0;">
                                <span class="provider-badge badge-{{ $item->provider }}"
                                    style="position:static;display:inline-block;margin-bottom:4px;">{{ strtoupper($item->provider) }}</span>
                                <h3
                                    style="font-size:0.95rem;color:var(--cream);overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">
                                    {{ $item->drama_title }}</h3>
                                <p style="font-size:0.8rem;color:var(--text-muted);margin-top:4px;">Episode
                                    {{ $item->episode_number }} • {{ $item->updated_at->diffForHumans() }}</p>
                            </div>
                            <form method="POST" action="{{ route('history.remove', $item->id) }}" style="flex-shrink:0;">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    style="background:none;border:none;color:var(--text-dim);cursor:pointer;padding:4px;"
                                    title="Hapus">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M18 6 6 18M6 6l12 12" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                        <div style="margin-top:auto;padding-top:var(--gap-sm);">
                            <a href="{{ $watchUrl }}" class="btn btn-primary" style="padding:0.375rem 0.875rem;font-size:0.8rem;">
                                <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24">
                                    <polygon points="5,3 19,12 5,21" />
                                </svg>
                                Lanjut Nonton
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

@endsection