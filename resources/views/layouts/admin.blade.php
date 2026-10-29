<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel Admin') — JavaTrip Travel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<div class="admin-shell">
    <aside class="sidebar">
        <a href="{{ route('admin.dashboard') }}" class="brand">
            <span class="brand-mark">✈</span>
            <span>JavaTrip<span style="opacity:.6;font-weight:600"> Admin</span></span>
        </a>

        <div class="side-label">Utama</div>
        <a href="{{ route('admin.dashboard') }}" class="side-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <span class="ico">▦</span> Dashboard
        </a>

        <div class="side-label">Data Master</div>
        <a href="{{ route('admin.destinasi.index') }}" class="side-link {{ request()->routeIs('admin.destinasi.*') ? 'active' : '' }}">
            <span class="ico">◈</span> Destinasi
        </a>
        <a href="{{ route('admin.paket.index') }}" class="side-link {{ request()->routeIs('admin.paket.*') ? 'active' : '' }}">
            <span class="ico">▤</span> Paket Wisata
        </a>
        <a href="{{ route('admin.jadwal.index') }}" class="side-link {{ request()->routeIs('admin.jadwal.*') ? 'active' : '' }}">
            <span class="ico">🗓</span> Jadwal &amp; Kuota
        </a>

        <div class="side-label">Operasional</div>
        <a href="{{ route('admin.verifikasi.index') }}" class="side-link {{ request()->routeIs('admin.verifikasi.*') ? 'active' : '' }}">
            <span class="ico">✓</span> Verifikasi
            @php($nVerif = \App\Models\Pemesanan::where('status_pemesanan', 'menunggu_verifikasi')->count())
            @if($nVerif)
                <span class="badge badge-warning" style="margin-left:auto">{{ $nVerif }}</span>
            @endif
        </a>
        <a href="{{ route('admin.manifes.index') }}" class="side-link {{ request()->routeIs('admin.manifes.*') ? 'active' : '' }}">
            <span class="ico">☰</span> Manifes Peserta
        </a>
        <a href="{{ route('admin.voucher.index') }}" class="side-link {{ request()->routeIs('admin.voucher.*') ? 'active' : '' }}">
            <span class="ico">◫</span> Validasi Voucher
        </a>
        <a href="{{ route('admin.laporan.index') }}" class="side-link {{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}">
            <span class="ico">▧</span> Laporan
        </a>

        <div class="side-foot">
            <a href="{{ route('beranda') }}" class="side-link"><span class="ico">↗</span> Lihat Situs</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="side-link" style="background:none;border:0;width:100%;cursor:pointer;font-family:inherit">
                    <span class="ico">⏻</span> Keluar
                </button>
            </form>
        </div>
    </aside>

    <div class="admin-main">
        <div class="admin-top">
            <h1>@yield('heading', 'Dashboard')</h1>
            <div class="row" style="margin-left:auto;gap:10px">
                <div class="avatar">{{ strtoupper(substr(auth()->user()->nama_lengkap, 0, 1)) }}</div>
                <div>
                    <div style="font-size:13px;font-weight:700;line-height:1.2">{{ auth()->user()->nama_lengkap }}</div>
                    <div class="tiny muted">Admin Travel</div>
                </div>
            </div>
        </div>

        <div class="admin-body">
            @if(session('sukses'))
                <div class="alert alert-success">✓ {{ session('sukses') }}</div>
            @endif

            @if($errors->any())
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
            @endif

            @yield('content')
        </div>
    </div>
</div>
</body>
</html>
