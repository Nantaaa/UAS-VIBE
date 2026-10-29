<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel Admin') — Nanta Travel</title>
    <link rel="stylesheet" href="{{ asset('css/fonts.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body class="adm">
@php
    $nVerif = \App\Models\Pemesanan::where('status_pemesanan', 'menunggu_verifikasi')->count();
    $nPerbaikan = \App\Models\Pemesanan::where('status_pemesanan', 'perlu_perbaikan')->count();
@endphp
<div class="adm-shell">
    <aside class="adm-side">
        <a href="{{ route('admin.dashboard') }}" class="adm-brand">
            <span class="mark">N</span>
            <span>
                <span class="nm">Nanta Travel</span>
                <span class="sub">Archipelago Concierge</span>
            </span>
        </a>

        <div class="adm-label">Navigasi Operasional</div>

        <a href="{{ route('admin.dashboard') }}" class="adm-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            @include('partials.icon', ['name' => 'grid', 'size' => 14]) Dashboard
        </a>
        <a href="{{ route('admin.destinasi.index') }}" class="adm-link {{ request()->routeIs('admin.destinasi.*') ? 'active' : '' }}">
            @include('partials.icon', ['name' => 'map-pin', 'size' => 14]) Destinasi
        </a>
        <a href="{{ route('admin.paket.index') }}" class="adm-link {{ request()->routeIs('admin.paket.*') ? 'active' : '' }}">
            @include('partials.icon', ['name' => 'compass', 'size' => 14]) Paket Wisata
        </a>
        <a href="{{ route('admin.jadwal.index') }}" class="adm-link {{ request()->routeIs('admin.jadwal.*') ? 'active' : '' }}">
            @include('partials.icon', ['name' => 'calendar', 'size' => 14]) Jadwal Keberangkatan
        </a>
        <a href="{{ route('admin.verifikasi.index') }}" class="adm-link {{ request()->routeIs('admin.verifikasi.*') ? 'active' : '' }}">
            @include('partials.icon', ['name' => 'check-circle', 'size' => 14]) Verifikasi Pemesanan
            @if($nVerif > 0)<span class="badge-n">{{ $nVerif }}</span>@endif
        </a>
        <a href="{{ route('admin.manifes.index') }}" class="adm-link {{ request()->routeIs('admin.manifes.*') ? 'active' : '' }}">
            @include('partials.icon', ['name' => 'users', 'size' => 14]) Manifes Peserta
        </a>
        <a href="{{ route('admin.voucher.index') }}" class="adm-link {{ request()->routeIs('admin.voucher.*') ? 'active' : '' }}">
            @include('partials.icon', ['name' => 'ticket', 'size' => 14]) Validasi Voucher
        </a>
        <a href="{{ route('admin.laporan.index') }}" class="adm-link {{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}">
            @include('partials.icon', ['name' => 'chart', 'size' => 14]) Laporan Bulanan
        </a>

        <div class="adm-side-foot">
            <a href="{{ route('beranda') }}" class="adm-link">@include('partials.icon', ['name' => 'arrow-right', 'size' => 14]) Lihat Situs</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="adm-link" style="width:100%;background:none;border:0;font-family:inherit;cursor:pointer">
                    @include('partials.icon', ['name' => 'logout', 'size' => 14]) Keluar Panel
                </button>
            </form>
        </div>
    </aside>

    <div class="adm-main">
        <div class="adm-top">
            <div class="date">
                @include('partials.icon', ['name' => 'calendar', 'size' => 15])
                <span>
                    {{ now()->translatedFormat('l, d F Y') }}
                    <small>Operasional Travel &amp; Reservasi</small>
                </span>
            </div>

            <form class="adm-search" method="GET" action="{{ route('admin.verifikasi.index') }}">
                @include('partials.icon', ['name' => 'search', 'size' => 13])
                <input type="text" name="q" placeholder="Cari booking, armada, peserta...">
            </form>

            <div class="adm-top-right">
                <span class="adm-online"><span class="dot-green"></span> Sistem Online</span>
                <span class="adm-bell">@include('partials.icon', ['name' => 'bell', 'size' => 16])</span>
                <div class="adm-user">
                    <span class="ava">{{ strtoupper(substr(auth()->user()->nama_lengkap, 0, 1)) }}</span>
                    <span>
                        <span class="nm">{{ auth()->user()->nama_lengkap }}</span>
                        <span class="rl">Head of Operations</span>
                    </span>
                </div>
            </div>
        </div>

        <div class="adm-body">
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
