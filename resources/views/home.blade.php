@extends('layouts.app')

@section('title', 'DramaCina — Nonton Drama Asia Gratis')
@section('description', 'Streaming drama dari DramaBox, ReelShort, ShortMax, NetShort, Melolo, FreeReels & DramaNova. Gratis, no login untuk nonton.')

@section('content')

{{-- Provider Pills --}}
<div class="provider-pills">
    <a href="{{ route('dramabox.index') }}" class="provider-pill" style="--pill-color:var(--c-dramabox);">
        <span class="pill-icon">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8m-4-4v4"/>
            </svg>
        </span>
        <span class="pill-label">DramaBox</span>
    </a>
    <a href="{{ route('reelshort.index') }}" class="provider-pill" style="--pill-color:var(--c-reelshort);">
        <span class="pill-icon">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/><polygon points="10 8 16 12 10 16 10 8" fill="currentColor" stroke="none"/>
            </svg>
        </span>
        <span class="pill-label">ReelShort</span>
    </a>
    <a href="{{ route('shortmax.index') }}" class="provider-pill" style="--pill-color:var(--c-shortmax);">
        <span class="pill-icon">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>
            </svg>
        </span>
        <span class="pill-label">ShortMax</span>
    </a>
    <a href="{{ route('netshort.index') }}" class="provider-pill" style="--pill-color:var(--c-netshort);">
        <span class="pill-icon">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 6h16M4 12h16M4 18h10"/>
            </svg>
        </span>
        <span class="pill-label">NetShort</span>
    </a>
    <a href="{{ route('melolo.index') }}" class="provider-pill" style="--pill-color:var(--c-melolo);">
        <span class="pill-icon">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/>
            </svg>
        </span>
        <span class="pill-label">Melolo</span>
    </a>
    <a href="{{ route('freereels.index') }}" class="provider-pill" style="--pill-color:var(--c-freereels);">
        <span class="pill-icon">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 2H3v16h5l3 3 3-3h7z"/>
                <path d="M9 11V9m6 2V7"/>
            </svg>
        </span>
        <span class="pill-label">FreeReels</span>
    </a>
    <a href="{{ route('dramanova.index') }}" class="provider-pill" style="--pill-color:var(--c-dramanova);">
        <span class="pill-icon">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
            </svg>
        </span>
        <span class="pill-label">DramaNova</span>
    </a>
</div>

{{-- ── Hero Slider — ambil top-1 dari tiap provider ───────── --}}
@php
    $slides = [];

    // DramaBox Trending (featured)
    $dbHeroList = data_get($featured, 'data', data_get($featured, 'result', []));
    $dbHero = is_array($dbHeroList) && count($dbHeroList) > 0 ? $dbHeroList[0] : null;
    if ($dbHero) {
        $slides[] = [
            'provider'  => 'DramaBox',
            'key'       => 'dramabox',
            'tag'       => 'Trending',
            'color'     => 'var(--c-dramabox)',
            'title'     => data_get($dbHero, 'bookName') ?? data_get($dbHero, 'title') ?? '',
            'cover'     => data_get($dbHero, 'cover') ?? data_get($dbHero, 'coverWap') ?? data_get($dbHero, 'thumbnail') ?? '',
            'desc'      => data_get($dbHero, 'introduction') ?? data_get($dbHero, 'description') ?? data_get($dbHero, 'desc') ?? '',
            'id'        => data_get($dbHero, 'bookId') ?? data_get($dbHero, 'id'),
            'route'     => 'dramabox.detail',
            'indexRoute'=> 'dramabox.index',
        ];
    }

    // ReelShort
    $rsRaw2  = data_get($reelShortNew, 'data');
    $rsHeroItem = null;
    if (is_array($rsRaw2)) {
        $rsList = $rsRaw2['lists'] ?? (array_is_list($rsRaw2) ? $rsRaw2 : []);
        $rsHeroItem = $rsList[0] ?? null;
    }
    if ($rsHeroItem) {
        $slides[] = [
            'provider'  => 'ReelShort',
            'key'       => 'reelshort',
            'tag'       => 'For You',
            'color'     => 'var(--c-reelshort)',
            'title'     => data_get($rsHeroItem, 'book_title') ?? data_get($rsHeroItem, 'bookName') ?? '',
            'cover'     => data_get($rsHeroItem, 'book_pic') ?? data_get($rsHeroItem, 'cover') ?? '',
            'desc'      => data_get($rsHeroItem, 'introduction') ?? data_get($rsHeroItem, 'desc') ?? '',
            'id'        => data_get($rsHeroItem, 'book_id') ?? data_get($rsHeroItem, 'bookId'),
            'route'     => 'reelshort.detail',
            'indexRoute'=> 'reelshort.index',
        ];
    }

    // ShortMax — API: {status,results:[{shortPlayId,name,cover,summary}]}
    $smItems = data_get($shortMaxNew, 'results',
               data_get($shortMaxNew, 'data.results',
               data_get($shortMaxNew, 'data', [])));
    if (is_array($smItems) && isset($smItems['results'])) $smItems = $smItems['results'];
    $smHeroItem = is_array($smItems) && array_is_list($smItems) && count($smItems) > 0 ? $smItems[0] : null;
    if ($smHeroItem) {
        $slides[] = [
            'provider'  => 'ShortMax',
            'key'       => 'shortmax',
            'tag'       => 'For You',
            'color'     => 'var(--c-shortmax)',
            'title'     => data_get($smHeroItem, 'name') ?? data_get($smHeroItem, 'shortPlayName') ?? data_get($smHeroItem, 'title') ?? '',
            'cover'     => data_get($smHeroItem, 'cover') ?? data_get($smHeroItem, 'picUrl') ?? data_get($smHeroItem, 'coverUrl') ?? '',
            'desc'      => data_get($smHeroItem, 'summary') ?? data_get($smHeroItem, 'description') ?? data_get($smHeroItem, 'introduction') ?? '',
            'id'        => data_get($smHeroItem, 'shortPlayId') ?? data_get($smHeroItem, 'id'),
            'route'     => 'shortmax.detail',
            'indexRoute'=> 'shortmax.index',
        ];
    }

    // Melolo — API: {algo,books:[{book_id,book_name,thumb_url,abstract}]}
    $meBooks = data_get($meloloTrending, 'books',
               data_get($meloloTrending, 'data.books',
               data_get($meloloTrending, 'data', [])));
    $meHeroItem = is_array($meBooks) && array_is_list($meBooks) && count($meBooks) > 0 ? $meBooks[0] : null;
    if ($meHeroItem) {
        $slides[] = [
            'provider'  => 'Melolo',
            'key'       => 'melolo',
            'tag'       => 'Trending',
            'color'     => 'var(--c-melolo)',
            'title'     => data_get($meHeroItem, 'book_name') ?? data_get($meHeroItem, 'title') ?? data_get($meHeroItem, 'book_title') ?? '',
            'cover'     => data_get($meHeroItem, 'thumb_url') ?? data_get($meHeroItem, 'cover') ?? data_get($meHeroItem, 'thumbnail') ?? '',
            'desc'      => data_get($meHeroItem, 'abstract') ?? data_get($meHeroItem, 'description') ?? data_get($meHeroItem, 'introduction') ?? '',
            'id'        => data_get($meHeroItem, 'book_id') ?? data_get($meHeroItem, 'id'),
            'route'     => 'melolo.detail',
            'indexRoute'=> 'melolo.index',
        ];
    }

    // FreeReels
    $frRaw2   = data_get($freeReelsHome, 'data', []);
    $frOuter2 = is_array($frRaw2) ? ($frRaw2['items'] ?? []) : [];
    $frHeroItem = null;
    foreach ($frOuter2 as $mod) {
        $sub = $mod['items'] ?? [];
        if (!empty($sub)) { $frHeroItem = $sub[0]; break; }
    }
    if (!$frHeroItem && is_array($frRaw2) && array_is_list($frRaw2)) $frHeroItem = $frRaw2[0] ?? null;
    if ($frHeroItem) {
        $slides[] = [
            'provider'  => 'FreeReels',
            'key'       => 'freereels',
            'tag'       => 'Homepage',
            'color'     => 'var(--c-freereels)',
            'title'     => data_get($frHeroItem, 'title') ?? data_get($frHeroItem, 'name') ?? '',
            'cover'     => data_get($frHeroItem, 'cover') ?? data_get($frHeroItem, 'thumbnail') ?? '',
            'desc'      => data_get($frHeroItem, 'introduction') ?? data_get($frHeroItem, 'desc') ?? '',
            'id'        => data_get($frHeroItem, 'key') ?? data_get($frHeroItem, 'id'),
            'route'     => 'freereels.detail',
            'indexRoute'=> 'freereels.index',
        ];
    }

    // NetShort — foryou: {contentType,contentInfos:[{shortPlayId,shortPlayName,shortPlayCover}]}
    // parse() keeps dict as-is → $netShortNew has contentInfos at root or under data
    $nsHeroItem = null;
    $nsRoot = is_array($netShortNew) ? $netShortNew : [];
    $nsItems = $nsRoot['contentInfos']
            ?? data_get($nsRoot, 'data.contentInfos')
            ?? data_get($nsRoot, 'data', []);
    if (is_array($nsItems) && array_is_list($nsItems) && count($nsItems) > 0) {
        $nsHeroItem = $nsItems[0];
    }
    if ($nsHeroItem) {
        $slides[] = [
            'provider'  => 'NetShort',
            'key'       => 'netshort',
            'tag'       => 'Terbaru',
            'color'     => 'var(--c-netshort)',
            'title'     => data_get($nsHeroItem, 'shortPlayName') ?? data_get($nsHeroItem, 'title') ?? '',
            'cover'     => data_get($nsHeroItem, 'shortPlayCover') ?? data_get($nsHeroItem, 'cover') ?? '',
            'desc'      => data_get($nsHeroItem, 'description') ?? data_get($nsHeroItem, 'intro') ?? '',
            'id'        => data_get($nsHeroItem, 'shortPlayId') ?? data_get($nsHeroItem, 'id'),
            'route'     => 'netshort.detail',
            'indexRoute'=> 'netshort.index',
        ];
    }

    // DramaNova — API: {total,rows:[{dramaId,title,posterImg,synopsis,categoryNames}]}
    // parse() keeps dict as-is → $novaHome has rows at root
    $novRows = data_get($novaHome, 'rows',
               data_get($novaHome, 'data.rows', []));
    $novHeroItem = is_array($novRows) && count($novRows) > 0 ? $novRows[0] : null;
    if ($novHeroItem) {
        $slides[] = [
            'provider'  => 'DramaNova',
            'key'       => 'dramanova',
            'tag'       => 'Pilihan',
            'color'     => 'var(--c-dramanova)',
            'title'     => data_get($novHeroItem, 'title') ?? data_get($novHeroItem, 'name') ?? '',
            'cover'     => data_get($novHeroItem, 'posterImg') ?? data_get($novHeroItem, 'bannerImg') ?? data_get($novHeroItem, 'posterImgUrl') ?? data_get($novHeroItem, 'cover') ?? '',
            'desc'      => data_get($novHeroItem, 'synopsis') ?? data_get($novHeroItem, 'description') ?? data_get($novHeroItem, 'intro') ?? '',
            'id'        => data_get($novHeroItem, 'dramaId') ?? data_get($novHeroItem, 'id'),
            'route'     => 'dramanova.detail',
            'indexRoute'=> 'dramanova.index',
        ];
    }

    // Remove slides missing title or cover
    $slides = array_values(array_filter($slides, fn($s) => !empty($s['title']) && !empty($s['cover'])));
@endphp


@if(count($slides) > 0)
<div class="hero-slider" id="heroSlider" style="margin-bottom:var(--gap-2xl);">
    {{-- Slides --}}
    <div class="hero-slides" id="heroSlides">
        @foreach($slides as $i => $slide)
        <div class="hero-slide {{ $i === 0 ? 'active' : '' }}"
             data-index="{{ $i }}"
             style="--slide-color:{{ $slide['color'] }};">
            {{-- Background image --}}
            @if($slide['cover'])
                <img src="{{ $slide['cover'] }}" alt="{{ $slide['title'] }}" loading="{{ $i === 0 ? 'eager' : 'lazy' }}" class="hero-slide-img">
            @else
                <div class="hero-slide-placeholder"></div>
            @endif

            {{-- Gradient overlay --}}
            <div class="hero-overlay">
                <div class="hero-content">
                    <span class="hero-badge" style="background:color-mix(in srgb,{{ $slide['color'] }} 15%,transparent);border-color:color-mix(in srgb,{{ $slide['color'] }} 40%,transparent);color:{{ $slide['color'] }};">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><polygon points="5,3 19,12 5,21"/></svg>
                        {{ $slide['provider'] }}
                        <span class="hero-badge-sep">·</span>
                        <span class="hero-badge-tag" style="background:color-mix(in srgb,{{ $slide['color'] }} 20%,transparent);border-color:color-mix(in srgb,{{ $slide['color'] }} 25%,transparent);color:{{ $slide['color'] }};">{{ $slide['tag'] }}</span>
                    </span>
                    <h2 class="hero-title">{{ $slide['title'] }}</h2>
                    @if($slide['desc'])
                        <p class="hero-desc">{{ Str::limit(strip_tags($slide['desc']), 150) }}</p>
                    @endif
                    <div class="hero-actions">
                        @if($slide['id'])
                        <a href="{{ route($slide['route'], ['id' => $slide['id']]) }}" class="btn btn-primary">
                            <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24"><polygon points="5,3 19,12 5,21"/></svg>
                            Tonton Sekarang
                        </a>
                        @endif
                        <a href="{{ route($slide['indexRoute']) }}" class="btn btn-ghost">Lihat {{ $slide['provider'] }}</a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Prev / Next buttons --}}
    @if(count($slides) > 1)
    <button class="hero-arrow hero-arrow-prev" id="heroPrev" aria-label="Sebelumnya">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
    </button>
    <button class="hero-arrow hero-arrow-next" id="heroNext" aria-label="Berikutnya">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><polyline points="9 6 15 12 9 18"/></svg>
    </button>

    {{-- Dot indicators --}}
    <div class="hero-dots" id="heroDots">
        @foreach($slides as $i => $slide)
        <button class="hero-dot {{ $i === 0 ? 'active' : '' }}"
                data-slide="{{ $i }}"
                style="--dot-color:{{ $slide['color'] }};"
                aria-label="Slide {{ $i + 1 }}"></button>
        @endforeach
    </div>
    @endif
</div>

@push('scripts')
<script>
(function() {
    const slider   = document.getElementById('heroSlider');
    if (!slider) return;
    const slides   = slider.querySelectorAll('.hero-slide');
    const dots     = slider.querySelectorAll('.hero-dot');
    const prevBtn  = document.getElementById('heroPrev');
    const nextBtn  = document.getElementById('heroNext');
    let current    = 0;
    let timer      = null;
    const total    = slides.length;

    function goTo(idx) {
        slides[current].classList.remove('active');
        dots[current]?.classList.remove('active');
        current = (idx + total) % total;
        slides[current].classList.add('active');
        dots[current]?.classList.add('active');
    }

    function startAuto() {
        timer = setInterval(() => goTo(current + 1), 5500);
    }
    function resetAuto() {
        clearInterval(timer);
        startAuto();
    }

    prevBtn?.addEventListener('click', () => { goTo(current - 1); resetAuto(); });
    nextBtn?.addEventListener('click', () => { goTo(current + 1); resetAuto(); });
    dots.forEach(d => d.addEventListener('click', () => { goTo(+d.dataset.slide); resetAuto(); }));

    // Pause on hover
    slider.addEventListener('mouseenter', () => clearInterval(timer));
    slider.addEventListener('mouseleave', startAuto);

    // Swipe support
    let touchX = 0;
    slider.addEventListener('touchstart', e => { touchX = e.touches[0].clientX; }, { passive: true });
    slider.addEventListener('touchend',   e => {
        const dx = e.changedTouches[0].clientX - touchX;
        if (Math.abs(dx) > 40) { goTo(current + (dx < 0 ? 1 : -1)); resetAuto(); }
    }, { passive: true });

    startAuto();
})();
</script>
@endpush
@endif

{{-- DramaBox Section --}}
@php
    $dbItems = data_get($dramaBoxNew, 'data', data_get($dramaBoxNew, 'result', data_get($dramaBoxNew, 'list', [])));
    $dbItems = is_array($dbItems) ? array_slice($dbItems, 0, 10) : [];
@endphp
@if(count($dbItems) > 0)
<section class="section">
    <div class="section-header">
        <h2 class="section-title">DramaBox</h2>
        <a href="{{ route('dramabox.index') }}" class="section-link">Lihat Semua →</a>
    </div>
    <div class="scroll-row">
        @foreach($dbItems as $item)
            @php
                $id = data_get($item, 'bookId') ?? data_get($item, 'id');
                $title = data_get($item, 'bookName') ?? data_get($item, 'title') ?? data_get($item, 'name');
                $thumb = data_get($item, 'cover') ?? data_get($item, 'coverWap') ?? data_get($item, 'thumbnail') ?? data_get($item, 'coverUrl');
            @endphp
            @if($id)
            <a href="{{ route('dramabox.detail', ['id' => $id]) }}" class="drama-card" style="display:block;text-decoration:none;">
                <div class="drama-card-poster">
                    @if($thumb)<img src="{{ $thumb }}" alt="{{ $title }}" loading="lazy" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'200\' height=\'300\'%3E%3Crect fill=\'%231A1408\' width=\'200\' height=\'300\'/%3E%3C/svg%3E'">@endif
                    <div class="overlay"></div>
                    <div class="play-btn"><svg width="18" height="18" fill="var(--cream)" viewBox="0 0 24 24"><polygon points="5,3 19,12 5,21"/></svg></div>
                    <span class="provider-badge badge-dramabox">DRAMABOX</span>
                </div>
                <div class="drama-card-info">
                    <div class="drama-card-title">{{ $title ?? 'Drama' }}</div>
                </div>
            </a>
            @endif
        @endforeach
    </div>
</section>
@endif

{{-- ReelShort Section --}}
@php
    // ReelShort API: {success, data: {lists: [{book_id, book_title, book_pic, ...}]}}
    $rsRaw  = data_get($reelShortNew, 'data');
    $rsItems = [];
    if (is_array($rsRaw)) {
        // nested: data.lists
        $rsItems = $rsRaw['lists'] ?? (array_is_list($rsRaw) ? $rsRaw : []);
    }
    $rsItems = array_slice($rsItems, 0, 10);
@endphp
@if(count($rsItems) > 0)
<section class="section">
    <div class="section-header">
        <h2 class="section-title">ReelShort</h2>
        <a href="{{ route('reelshort.index') }}" class="section-link">Lihat Semua →</a>
    </div>
    <div class="scroll-row">
        @foreach($rsItems as $item)
            @php
                $id = data_get($item, 'book_id') ?? data_get($item, 'bookId') ?? data_get($item, 'id');
                $title = data_get($item, 'book_title') ?? data_get($item, 'bookName') ?? data_get($item, 'title');
                $thumb = data_get($item, 'book_pic') ?? data_get($item, 'cover') ?? data_get($item, 'thumbnail') ?? data_get($item, 'image');
            @endphp
            @if($id)
            <a href="{{ route('reelshort.detail', ['id' => $id]) }}" class="drama-card" style="display:block;text-decoration:none;">
                <div class="drama-card-poster">
                    @if($thumb)<img src="{{ $thumb }}" alt="{{ $title }}" loading="lazy" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'200\' height=\'300\'%3E%3Crect fill=\'%231A1408\' width=\'200\' height=\'300\'/%3E%3C/svg%3E'">@endif
                    <div class="overlay"></div>
                    <div class="play-btn"><svg width="18" height="18" fill="var(--cream)" viewBox="0 0 24 24"><polygon points="5,3 19,12 5,21"/></svg></div>
                    <span class="provider-badge badge-reelshort">REELSHORT</span>
                </div>
                <div class="drama-card-info">
                    <div class="drama-card-title">{{ $title ?? 'Drama' }}</div>
                </div>
            </a>
            @endif
        @endforeach
    </div>
</section>
@endif

{{-- ShortMax Section --}}
@php
    // ShortMax API: {status, results:[{shortPlayId,name,cover,summary}]}
    $smItems = data_get($shortMaxNew, 'results', []);
    if (empty($smItems)) {
        $smInner = data_get($shortMaxNew, 'data', $shortMaxNew);
        $smItems = data_get($smInner, 'results', is_array($smInner) && array_is_list($smInner) ? $smInner : []);
    }
    $smItems = is_array($smItems) && array_is_list($smItems) ? array_slice($smItems, 0, 10) : [];
@endphp
@if(count($smItems) > 0)
<section class="section">
    <div class="section-header">
        <h2 class="section-title">ShortMax</h2>
        <a href="{{ route('shortmax.index') }}" class="section-link">Lihat Semua →</a>
    </div>
    <div class="scroll-row">
        @foreach($smItems as $item)
            @php
                $id = data_get($item, 'shortPlayId') ?? data_get($item, 'id');
                $title = data_get($item, 'name') ?? data_get($item, 'shortPlayName') ?? data_get($item, 'title');
                $thumb = data_get($item, 'cover') ?? data_get($item, 'picUrl') ?? data_get($item, 'coverUrl');
            @endphp
            @if($id)
            <a href="{{ route('shortmax.detail', ['id' => $id]) }}" class="drama-card" style="display:block;text-decoration:none;">
                <div class="drama-card-poster">
                    @if($thumb)<img src="{{ $thumb }}" alt="{{ $title }}" loading="lazy" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'200\' height=\'300\'%3E%3Crect fill=\'%231A1408\' width=\'200\' height=\'300\'/%3E%3C/svg%3E'">@endif
                    <div class="overlay"></div>
                    <div class="play-btn"><svg width="18" height="18" fill="var(--cream)" viewBox="0 0 24 24"><polygon points="5,3 19,12 5,21"/></svg></div>
                    <span class="provider-badge badge-shortmax">SHORTMAX</span>
                </div>
                <div class="drama-card-info">
                    <div class="drama-card-title">{{ $title ?? 'Drama' }}</div>
                </div>
            </a>
            @endif
        @endforeach
    </div>
</section>
@endif

{{-- Melolo Section --}}
@php
    // Melolo API: {algo, books:[{book_id,book_name,thumb_url,abstract}]}
    $meItems = data_get($meloloTrending, 'books', []);
    if (empty($meItems)) {
        $meInner = data_get($meloloTrending, 'data', $meloloTrending);
        $meItems = data_get($meInner, 'books', is_array($meInner) && array_is_list($meInner) ? $meInner : []);
    }
    $meItems = is_array($meItems) && array_is_list($meItems) ? array_slice($meItems, 0, 10) : [];
@endphp
@if(count($meItems) > 0)
<section class="section">
    <div class="section-header">
        <h2 class="section-title">Melolo — Trending</h2>
        <a href="{{ route('melolo.index') }}" class="section-link">Lihat Semua →</a>
    </div>
    <div class="scroll-row">
        @foreach($meItems as $item)
            @php
                $id = data_get($item, 'book_id') ?? data_get($item, 'id');
                $title = data_get($item, 'book_name') ?? data_get($item, 'title') ?? data_get($item, 'book_title');
                $thumb = data_get($item, 'thumb_url') ?? data_get($item, 'cover') ?? data_get($item, 'thumbnail');
            @endphp
            @if($id)
            <a href="{{ route('melolo.detail', ['id' => $id]) }}" class="drama-card" style="display:block;text-decoration:none;">
                <div class="drama-card-poster">
                    @if($thumb)<img src="{{ $thumb }}" alt="{{ $title }}" loading="lazy" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'200\' height=\'300\'%3E%3Crect fill=\'%231A1408\' width=\'200\' height=\'300\'/%3E%3C/svg%3E'">@endif
                    <div class="overlay"></div>
                    <div class="play-btn"><svg width="18" height="18" fill="var(--cream)" viewBox="0 0 24 24"><polygon points="5,3 19,12 5,21"/></svg></div>
                    <span class="provider-badge badge-melolo">MELOLO</span>
                </div>
                <div class="drama-card-info">
                    <div class="drama-card-title">{{ $title ?? 'Drama' }}</div>
                </div>
            </a>
            @endif
        @endforeach
    </div>
</section>
@endif

{{-- FreeReels Section --}}
@php
    // FreeReels API: {code, data: {items: [{type, items: [{key, cover, title}]}]}}
    $frRaw   = data_get($freeReelsHome, 'data', []);
    $frOuter = is_array($frRaw) ? ($frRaw['items'] ?? []) : [];
    $frItems = [];
    foreach ($frOuter as $module) {
        $sub = $module['items'] ?? [];
        foreach ($sub as $s) { $frItems[] = $s; }
    }
    // Fallback: if data is already a flat list
    if (empty($frItems) && is_array($frRaw) && array_is_list($frRaw)) {
        $frItems = $frRaw;
    }
    $frItems = array_slice($frItems, 0, 10);
@endphp
@if(count($frItems) > 0)
<section class="section">
    <div class="section-header">
        <h2 class="section-title">FreeReels</h2>
        <a href="{{ route('freereels.index') }}" class="section-link">Lihat Semua →</a>
    </div>
    <div class="scroll-row">
        @foreach($frItems as $item)
            @php
                $id = data_get($item, 'key') ?? data_get($item, 'id') ?? data_get($item, 'bookId');
                $title = data_get($item, 'title') ?? data_get($item, 'name') ?? data_get($item, 'bookName');
                $thumb = data_get($item, 'cover') ?? data_get($item, 'thumbnail') ?? data_get($item, 'image') ?? data_get($item, 'poster');
            @endphp
            @if($id)
            <a href="{{ route('freereels.detail', ['id' => $id]) }}" class="drama-card" style="display:block;text-decoration:none;">
                <div class="drama-card-poster">
                    @if($thumb)<img src="{{ $thumb }}" alt="{{ $title }}" loading="lazy" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'200\' height=\'300\'%3E%3Crect fill=\'%231A1408\' width=\'200\' height=\'300\'/%3E%3C/svg%3E'">@endif
                    <div class="overlay"></div>
                    <div class="play-btn"><svg width="18" height="18" fill="var(--cream)" viewBox="0 0 24 24"><polygon points="5,3 19,12 5,21"/></svg></div>
                    <span class="provider-badge badge-freereels">FREEREELS</span>
                </div>
                <div class="drama-card-info">
                    <div class="drama-card-title">{{ $title ?? 'Drama' }}</div>
                </div>
            </a>
            @endif
        @endforeach
    </div>
</section>
@endif

{{-- DramaNova Section --}}
@php
    $dnItems = data_get($novaHome, 'data', data_get($novaHome, 'result', data_get($novaHome, 'list', [])));
    $dnItems = is_array($dnItems) ? array_slice($dnItems, 0, 10) : [];
@endphp
@if(count($dnItems) > 0)
<section class="section">
    <div class="section-header">
        <h2 class="section-title">DramaNova</h2>
        <a href="{{ route('dramanova.index') }}" class="section-link">Lihat Semua →</a>
    </div>
    <div class="scroll-row">
        @foreach($dnItems as $item)
            @php
                $id = data_get($item, 'dramaId') ?? data_get($item, 'id');
                $title = data_get($item, 'dramaTitle') ?? data_get($item, 'title') ?? data_get($item, 'name');
                $thumb = data_get($item, 'cover') ?? data_get($item, 'thumbnail') ?? data_get($item, 'image');
            @endphp
            @if($id)
            <a href="{{ route('dramanova.detail', ['id' => $id]) }}" class="drama-card" style="display:block;text-decoration:none;">
                <div class="drama-card-poster">
                    @if($thumb)<img src="{{ $thumb }}" alt="{{ $title }}" loading="lazy" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'200\' height=\'300\'%3E%3Crect fill=\'%231A1408\' width=\'200\' height=\'300\'/%3E%3C/svg%3E'">@endif
                    <div class="overlay"></div>
                    <div class="play-btn"><svg width="18" height="18" fill="var(--cream)" viewBox="0 0 24 24"><polygon points="5,3 19,12 5,21"/></svg></div>
                    <span class="provider-badge badge-dramanova">DRAMANOVA</span>
                </div>
                <div class="drama-card-info">
                    <div class="drama-card-title">{{ $title ?? 'Drama' }}</div>
                </div>
            </a>
            @endif
        @endforeach
    </div>
</section>
@endif

@endsection
