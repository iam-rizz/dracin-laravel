<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'DramaCina') — Streaming Drama Asia</title>
    <meta name="description" content="@yield('description', 'Nonton drama Asia gratis dari DramaBox, ReelShort, ShortMax, NetShort, Melolo, FreeReels & DramaNova.')">

    <!-- PWA -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0A0804">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="/css/app.css?v={{ filemtime(public_path('css/app.css')) }}">

    @stack('styles')
</head>
<body>

<!-- ── Navbar ───────────────────────────────────────────── -->
<nav class="navbar" id="navbar">
    <div class="container">
        <a href="{{ route('home') }}" class="navbar-brand">Drama<span>Cina</span></a>

        <div class="navbar-search">
            <form action="{{ route('search') }}" method="GET">
                <svg class="search-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                </svg>
                <input type="text" name="q" placeholder="Cari drama..." value="{{ request('q') }}" autocomplete="off">
            </form>
        </div>

        <ul class="navbar-nav">
            <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a></li>
            <li><a href="{{ route('dramabox.index') }}" class="{{ request()->routeIs('dramabox.*') ? 'active' : '' }}">DramaBox</a></li>
            <li><a href="{{ route('reelshort.index') }}" class="{{ request()->routeIs('reelshort.*') ? 'active' : '' }}">ReelShort</a></li>
            <li><a href="{{ route('shortmax.index') }}" class="{{ request()->routeIs('shortmax.*') ? 'active' : '' }}">ShortMax</a></li>
            <li><a href="{{ route('netshort.index') }}" class="{{ request()->routeIs('netshort.*') ? 'active' : '' }}">NetShort</a></li>
            <li><a href="{{ route('melolo.index') }}" class="{{ request()->routeIs('melolo.*') ? 'active' : '' }}">Melolo</a></li>
            <li style="position:relative;">
                <button class="nav-more-btn" id="navMoreBtn" aria-haspopup="true" aria-expanded="false">
                    Lagi
                    <svg width="12" height="12" viewBox="0 0 12 12" fill="currentColor"><path d="M2 4l4 4 4-4"/></svg>
                </button>
                <div class="nav-dropdown" id="navDropdown">
                    <a href="{{ route('freereels.index') }}" class="{{ request()->routeIs('freereels.*') ? 'active' : '' }}">FreeReels</a>
                    <a href="{{ route('dramanova.index') }}" class="{{ request()->routeIs('dramanova.*') ? 'active' : '' }}">DramaNova</a>
                    <a href="{{ route('search') }}">Cari Semua</a>
                </div>
            </li>
            @auth
                <li><a href="{{ route('watchlist.index') }}" title="Watchlist">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
                </a></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-ghost" style="padding:0.3rem 0.75rem;font-size:0.78rem;">Keluar</button>
                    </form>
                </li>
            @else
                <li><a href="{{ route('login') }}" class="nav-auth-btn">Masuk</a></li>
            @endauth
        </ul>

        <button class="hamburger" id="hamburgerBtn" aria-label="Menu">
            <span></span><span></span><span></span>
        </button>
    </div>
</nav>

<!-- Mobile Menu -->
<div class="mobile-menu" id="mobileMenu">
    <div class="mobile-menu-search">
        <form action="{{ route('search') }}" method="GET">
            <svg class="search-icon" width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            <input type="text" name="q" placeholder="Cari drama..." value="{{ request('q') }}">
        </form>
    </div>

    <div class="mobile-menu-label">Navigasi</div>
    <a href="{{ route('home') }}">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        Home
    </a>

    <div class="mobile-menu-label">Provider</div>
    <a href="{{ route('dramabox.index') }}">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8m-4-4v4"/></svg>
        DramaBox
    </a>
    <a href="{{ route('reelshort.index') }}">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><polygon points="10 8 16 12 10 16 10 8"/></svg>
        ReelShort
    </a>
    <a href="{{ route('shortmax.index') }}">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
        ShortMax
    </a>
    <a href="{{ route('netshort.index') }}">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="2" y="7" width="20" height="15" rx="2"/><path d="M16 3l-4 4-4-4"/></svg>
        NetShort
    </a>
    <a href="{{ route('melolo.index') }}">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
        Melolo
    </a>
    <a href="{{ route('freereels.index') }}">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M15 10l5 3-5 3V10z"/><path d="M3 5a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5z"/></svg>
        FreeReels
    </a>
    <a href="{{ route('dramanova.index') }}">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
        DramaNova
    </a>

    <div class="mobile-menu-divider"></div>

    @auth
        <a href="{{ route('watchlist.index') }}">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
            Watchlist
        </a>
        <a href="{{ route('history.index') }}">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            Riwayat
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-outline" style="width:100%;margin-top:0.5rem;">Keluar</button>
        </form>
    @else
        <a href="{{ route('login') }}" class="btn btn-primary" style="margin-top:0.5rem;justify-content:center;">Masuk</a>
        <a href="{{ route('register') }}" class="btn btn-outline" style="justify-content:center;">Daftar</a>
    @endauth
</div>

<!-- ── Main Content ─────────────────────────────────────── -->
<main class="page-content">
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success mb-md">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error mb-md">{{ session('error') }}</div>
        @endif

        @yield('content')
    </div>
</main>

<!-- ── Footer ───────────────────────────────────────────── -->
<footer style="border-top:1px solid var(--border);padding:var(--gap-xl) 0;margin-top:var(--gap-2xl);">
    <div class="container" style="display:flex;flex-wrap:wrap;justify-content:space-between;align-items:center;gap:var(--gap-md);">
        <div>
            <span style="font-family:'Bargain',sans-serif;font-size:1.2rem;background:linear-gradient(135deg,var(--cream),var(--gold));-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">DramaCina</span>
            <p style="font-size:0.75rem;color:var(--text-dim);margin-top:4px;">Powered by Sansekai API</p>
        </div>
        <div style="display:flex;flex-wrap:wrap;gap:var(--gap-sm);">
            <a href="{{ route('dramabox.index') }}" style="font-size:0.75rem;color:var(--text-muted);">DramaBox</a>
            <a href="{{ route('reelshort.index') }}" style="font-size:0.75rem;color:var(--text-muted);">ReelShort</a>
            <a href="{{ route('shortmax.index') }}" style="font-size:0.75rem;color:var(--text-muted);">ShortMax</a>
            <a href="{{ route('netshort.index') }}" style="font-size:0.75rem;color:var(--text-muted);">NetShort</a>
            <a href="{{ route('melolo.index') }}" style="font-size:0.75rem;color:var(--text-muted);">Melolo</a>
            <a href="{{ route('freereels.index') }}" style="font-size:0.75rem;color:var(--text-muted);">FreeReels</a>
            <a href="{{ route('dramanova.index') }}" style="font-size:0.75rem;color:var(--text-muted);">DramaNova</a>
        </div>
        <p style="font-size:0.7rem;color:var(--text-dim);">© {{ date('Y') }} DramaCina</p>
    </div>
</footer>

<!-- Toast Notification -->
<div class="toast" id="toast"></div>

<!-- ── Scripts ───────────────────────────────────────────── -->
<script>
// ── Hamburger Menu ─────────────────────────────────────
const hamburger = document.getElementById('hamburgerBtn');
const mobileMenu = document.getElementById('mobileMenu');

hamburger?.addEventListener('click', () => {
    const open = mobileMenu.classList.toggle('open');
    hamburger.setAttribute('aria-expanded', open);
    const spans = hamburger.querySelectorAll('span');
    if (open) {
        spans[0].style.transform = 'rotate(45deg) translate(3.5px, 3.5px)';
        spans[1].style.opacity = '0';
        spans[2].style.transform = 'rotate(-45deg) translate(3.5px,-3.5px)';
    } else {
        spans.forEach(s => { s.style.transform = ''; s.style.opacity = ''; });
    }
});

// ── "Lagi" Dropdown ────────────────────────────────────
const navMoreBtn = document.getElementById('navMoreBtn');
const navDropdown = document.getElementById('navDropdown');
if (navMoreBtn && navDropdown) {
    navMoreBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        const open = navDropdown.classList.toggle('open');
        navMoreBtn.classList.toggle('open', open);
        navMoreBtn.setAttribute('aria-expanded', open);
    });
    document.addEventListener('click', () => {
        navDropdown.classList.remove('open');
        navMoreBtn.classList.remove('open');
    });
    navDropdown.addEventListener('click', e => e.stopPropagation());
}

// ── Toast Utility ─────────────────────────────────────
function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    toast.textContent = message;
    toast.className = `toast ${type} show`;
    setTimeout(() => { toast.classList.remove('show'); }, 3000);
}

// ── Watchlist Toggle ──────────────────────────────────
function toggleWatchlist(btn, data) {
    if (!{{ auth()->check() ? 'true' : 'false' }}) {
        window.location.href = '{{ route("login") }}';
        return;
    }

    fetch('{{ route("watchlist.toggle") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify(data),
    })
    .then(r => r.json())
    .then(res => {
        if (res.status === 'added') {
            btn.classList.add('in-watchlist');
            btn.querySelector('.wl-text').textContent = 'Dalam Watchlist';
        } else {
            btn.classList.remove('in-watchlist');
            btn.querySelector('.wl-text').textContent = 'Tambah Watchlist';
        }
        showToast(res.message);
    })
    .catch(() => showToast('Terjadi kesalahan', 'error'));
}

// ── Save History ───────────────────────────────────────
function saveHistory(data) {
    @auth
    fetch('{{ route("history.save") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify(data),
    }).catch(() => {});
    @endauth
}

// ── PWA Service Worker ────────────────────────────────
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        // Unregister SW lama yang mungkin cache HTML halaman
        navigator.serviceWorker.getRegistrations().then(regs => {
            regs.forEach(reg => {
                reg.active?.scriptURL.includes('sw.js') && reg.update();
            });
        });

        navigator.serviceWorker.register('/sw.js?v=2').then(reg => {
            // Force update jika ada versi baru
            reg.update();
        }).catch(() => {});
    });
}
</script>

@stack('scripts')

</body>
</html>
