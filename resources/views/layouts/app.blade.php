<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'JavaTrip Travel') — Sistem Informasi Travel & Agen Wisata</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<header class="topbar">
    <div class="container">
        <a href="{{ route('beranda') }}" class="brand">
            <span class="brand-mark">✈</span>
            <span>JavaTrip</span>
        </a>

        <nav class="nav-links">
            <a href="{{ route('beranda') }}" class="{{ request()->routeIs('beranda') ? 'active' : '' }}">Beranda</a>
            <a href="{{ route('beranda') }}" class="{{ request()->routeIs('paket.*') ? 'active' : '' }}">Paket Wisata</a>
            @auth
                <a href="{{ route('pemesanan.index') }}" class="{{ request()->routeIs('pemesanan.*') ? 'active' : '' }}">Pemesanan Saya</a>
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}">Panel Admin</a>
                @endif
            @endauth
        </nav>

        <div class="actions">
            @auth
                <a href="{{ route('profil.edit') }}" class="btn btn-ghost btn-sm hide-sm">
                    <span class="avatar" style="width:26px;height:26px;font-size:11px">{{ strtoupper(substr(auth()->user()->nama_lengkap, 0, 1)) }}</span>
                    {{ Str::limit(auth()->user()->nama_lengkap, 14) }}
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-primary btn-sm" type="submit">Keluar</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn btn-ghost btn-sm">Masuk</a>
                <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Daftar</a>
            @endauth
        </div>
    </div>
</header>

<main>
    @if(session('sukses'))
        <div class="container mt-16">
            <div class="alert alert-success">✓ {{ session('sukses') }}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="container mt-16">
            <div class="alert alert-error">
                <div>
                    <strong>Periksa kembali isian Anda:</strong>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    @yield('content')
</main>

<footer style="border-top:1px solid var(--line);background:#fff;margin-top:48px">
    <div class="container" style="padding:26px 20px;display:flex;justify-content:space-between;gap:16px;flex-wrap:wrap">
        <div>
            <strong>JavaTrip Travel</strong>
            <div class="muted tiny">Sistem Informasi Travel &amp; Agen Wisata — UAS Proyek Sistem Aplikasi</div>
        </div>
        <div class="muted tiny">Universitas Duta Bangsa Surakarta · 2026</div>
    </div>
</footer>

<nav class="bottom-nav">
    <a href="{{ route('beranda') }}" class="{{ request()->routeIs('beranda') ? 'active' : '' }}">
        <span class="ico">🏠</span> Beranda
    </a>
    <a href="{{ route('beranda') }}" class="{{ request()->routeIs('paket.*') ? 'active' : '' }}">
        <span class="ico">🧭</span> Paket
    </a>
    <a href="{{ auth()->check() ? route('pemesanan.index') : route('login') }}" class="{{ request()->routeIs('pemesanan.*') ? 'active' : '' }}">
        <span class="ico">🎫</span> Pesanan
    </a>
    <a href="{{ auth()->check() ? route('profil.edit') : route('login') }}" class="{{ request()->routeIs('profil.*') ? 'active' : '' }}">
        <span class="ico">👤</span> Profil
    </a>
</nav>
</body>
</html>
