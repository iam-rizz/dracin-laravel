@extends('layouts.app')
@section('title', 'Watchlist Saya — DramaCina')
@section('content')

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:var(--gap-xl);">
        <h1 class="section-title">Watchlist Saya</h1>
        <a href="{{ route('history.index') }}" class="btn btn-ghost" style="font-size:0.875rem;">Riwayat Nonton</a>
    </div>

    @if($watchlist->isEmpty())
        <div class="watchlist-empty">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z" />
            </svg>
            <h3>Watchlist Kosong</h3>
            <p>Tambahkan drama favorit kamu ke watchlist.</p>
            <a href="{{ route('home') }}" class="btn btn-primary" style="margin-top:var(--gap-lg);">Jelajahi Drama</a>
        </div>
    @else
        <div class="watchlist-grid">
            @foreach($watchlist as $item)
                @php
                    $routes = [
                        'dramabox' => 'dramabox.detail',
                        'reelshort' => 'reelshort.detail',
                        'shortmax' => 'shortmax.detail',
                        'netshort' => 'netshort.detail',
                        'melolo' => 'melolo.detail',
                        'freereels' => 'freereels.detail',
                        'dramanova' => 'dramanova.detail',
                    ];
                    $detailRoute = $routes[$item->provider] ?? 'home';
                @endphp
                <div class="drama-card" style="position:relative;">
                    <a href="{{ route($detailRoute, ['id' => $item->drama_id]) }}" style="text-decoration:none;display:block;">
                        <div class="drama-card-poster">
                            @if($item->drama_thumbnail)
                                <img src="{{ $item->drama_thumbnail }}" alt="{{ $item->drama_title }}" loading="lazy">
                            @endif
                            <div class="overlay"></div>
                            <div class="play-btn"><svg width="18" height="18" fill="var(--cream)" viewBox="0 0 24 24">
                                    <polygon points="5,3 19,12 5,21" />
                                </svg></div>
                            <span class="provider-badge badge-{{ $item->provider }}">{{ strtoupper($item->provider) }}</span>
                        </div>
                        <div class="drama-card-info">
                            <div class="drama-card-title">{{ $item->drama_title }}</div>
                            @if($item->total_episodes)
                                <div class="drama-card-meta">{{ $item->total_episodes }} Episode</div>
                            @endif
                            <div class="drama-card-meta" style="margin-top:2px;">{{ $item->created_at->diffForHumans() }}</div>
                        </div>
                    </a>
                    <button onclick="removeFromWatchlist(this, '{{ $item->provider }}', '{{ $item->drama_id }}')"
                        style="position:absolute;top:0.5rem;right:0.5rem;background:rgba(0,0,0,0.7);border:none;border-radius:50%;width:28px;height:28px;cursor:pointer;color:white;display:flex;align-items:center;justify-content:center;"
                        title="Hapus dari watchlist">
                        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M18 6 6 18M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endforeach
        </div>
    @endif

@endsection

@push('scripts')
    <script>
        function removeFromWatchlist(btn, provider, dramaId) {
            fetch('{{ route("watchlist.toggle") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ provider, drama_id: dramaId, drama_title: '', drama_thumbnail: '' })
            }).then(() => {
                const card = btn.closest('.drama-card');
                card.style.transition = 'opacity 0.3s, transform 0.3s';
                card.style.opacity = '0';
                card.style.transform = 'scale(0.9)';
                setTimeout(() => card.remove(), 300);
                showToast('Dihapus dari watchlist');
            }).catch(() => showToast('Gagal', 'error'));
        }
    </script>
@endpush