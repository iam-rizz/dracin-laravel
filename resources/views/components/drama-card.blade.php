{{--
    Drama Card Component
    Props:
      $drama    => array (title, thumbnail/cover, etc.)
      $provider => string ('dramabox', 'reelshort', etc.)
      $id       => string  (the drama ID)
      $detailRoute => route name for detail page
      $idParam => query param name for the ID (default: 'id')
--}}
@php
    $idParam = $idParam ?? 'id';
    $title = $drama['title'] ?? $drama['name'] ?? $drama['bookName'] ?? $drama['shortPlayName'] ?? $drama['dramaTitle'] ?? 'Tanpa Judul';
    $thumb = $drama['cover'] ?? $drama['thumbnail'] ?? $drama['coverUrl'] ?? $drama['image'] ?? $drama['poster'] ?? $drama['img'] ?? null;
    $eps = $drama['episodes'] ?? $drama['episodeCount'] ?? $drama['totalEpisodes'] ?? null;
@endphp

<a href="{{ route($detailRoute, [$idParam => $id]) }}" class="drama-card" style="display:block;text-decoration:none;">
    <div class="drama-card-poster">
        @if($thumb)
            <img src="{{ $thumb }}" alt="{{ $title }}" loading="lazy"
                 onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'200\' height=\'300\' viewBox=\'0 0 200 300\'%3E%3Crect fill=\'%231A1408\' width=\'200\' height=\'300\'/%3E%3Ctext fill=\'%235C4530\' font-family=\'sans-serif\' font-size=\'12\' x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\'%3ENo Image%3C/text%3E%3C/svg%3E'">
        @else
            <div style="width:100%;height:100%;background:var(--bg-card2);display:flex;align-items:center;justify-content:center;">
                <svg width="32" height="32" fill="none" stroke="var(--text-dim)" viewBox="0 0 24 24">
                    <rect x="2" y="2" width="20" height="20" rx="2" ry="2"/><path d="M8 12l4-4 4 4m-4-4v8"/>
                </svg>
            </div>
        @endif

        <div class="overlay"></div>
        <div class="play-btn">
            <svg width="18" height="18" fill="var(--cream)" viewBox="0 0 24 24">
                <polygon points="5,3 19,12 5,21"/>
            </svg>
        </div>

        <span class="provider-badge badge-{{ $provider }}">{{ strtoupper($provider) }}</span>
    </div>

    <div class="drama-card-info">
        <div class="drama-card-title">{{ $title }}</div>
        @if($eps)
            <div class="drama-card-meta">{{ $eps }} Episode</div>
        @endif
    </div>
</a>
