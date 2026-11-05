<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Nanta Travel') — Sistem Informasi Travel &amp; Agen Wisata</title>
    <link rel="stylesheet" href="{{ asset('css/fonts.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/katalog.css') }}">
    <link rel="stylesheet" href="{{ asset('css/pages.css') }}">
    <link rel="stylesheet" href="{{ asset('css/ticket-profil.css') }}">
    <link rel="stylesheet" href="{{ asset('css/polish.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fixes.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <link rel="stylesheet" href="{{ asset('css/beranda-traveloka.css') }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
</head>
<body>
@hasSection('minimal')
@else
<header class="site-header">
    <div class="container">
        <a href="{{ route('beranda') }}" class="brand-h" title="Nanta Travel">
            <img src="{{ asset('images/logo-nanta.svg') }}" alt="Nanta Travel" height="38" style="height:38px;width:auto;display:block">
        </a>

        <nav class="nav-pills">
            <a href="{{ route('beranda') }}" class="{{ request()->routeIs('beranda') ? 'active' : '' }}">Beranda</a>
            <a href="{{ auth()->check() ? route('pemesanan.index') : route('login') }}" class="{{ request()->routeIs('pemesanan.*') ? 'active' : '' }}">Pesanan Saya</a>
            <a href="{{ auth()->check() ? route('wishlist.index') : route('login') }}" class="{{ request()->routeIs('wishlist.*') ? 'active' : '' }}">Wishlist</a>
        </nav>

        <div class="header-right">
            @auth
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-ghost btn-sm hide-sm">Panel Admin</a>
                @endif
                <a href="{{ route('profil.edit') }}" class="user-chip">
                    <span class="avatar" style="width:32px;height:32px;font-size:12px">{{ strtoupper(substr(auth()->user()->nama_lengkap, 0, 1)) }}</span>
                    <span class="user-meta">
                        <span class="user-name">{{ Str::limit(auth()->user()->nama_lengkap, 16) }}</span>
                        <span class="user-role">Pemilik Akun</span>
                    </span>
                    <span class="chev">⌄</span>
                </a>
                <form method="POST" action="{{ route('logout') }}" style="margin:0">
                    @csrf
                    <button class="btn btn-ghost btn-sm" type="submit" style="color:#ef4444;border-color:rgba(239,68,68,.3)">Keluar</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn btn-ghost btn-sm">Masuk</a>
                <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Daftar</a>
            @endauth
        </div>
    </div>
</header>
@endif

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

@hasSection('minimal')
@else
<footer class="site-footer-modern" id="bantuan">
    <div class="container">
        {{-- Top Grid 4 Kolom --}}
        <div class="footer-top-grid">
            {{-- Kolom 1: Profil Brand & Legalitas --}}
            <div class="footer-col">
                <a href="{{ route('beranda') }}" style="display:inline-block;margin-bottom:12px" title="Nanta Travel">
                    <img src="{{ asset('images/logo-nanta-white.svg') }}" alt="Nanta Travel" height="38" style="height:38px;width:auto;display:block">
                </a>
                <p>
                    Biro perjalanan wisata resmi nusantara berizin Kemenparekraf. Melayani ribuan penjelajah sejak 2024 dengan komitmen perjalanan aman, seru, dan transparan.
                </p>
                <div class="footer-legal-badge">
                    ✓ TDUP: 503/088/DPMPTSP · Anggota ASITA
                </div>
                <div class="footer-socials">
                    <a href="https://instagram.com" target="_blank" class="social-pill" title="Instagram @nantatravel">IG</a>
                    <a href="https://tiktok.com" target="_blank" class="social-pill" title="TikTok @nantatravel">TT</a>
                    <a href="https://youtube.com" target="_blank" class="social-pill" title="YouTube Nanta Travel">YT</a>
                    <a href="https://wa.me/6281234567890" target="_blank" class="social-pill" title="WhatsApp Resmi">WA</a>
                </div>
            </div>

            {{-- Kolom 2: Destinasi Populer --}}
            <div class="footer-col">
                <h4>Destinasi Nusantara</h4>
                <ul class="footer-links-list">
                    <li><a href="{{ route('beranda', ['q' => 'Komodo']) }}#katalog">Labuan Bajo &amp; Komodo</a></li>
                    <li><a href="{{ route('beranda', ['q' => 'Bromo']) }}#katalog">Gunung Bromo &amp; Semeru</a></li>
                    <li><a href="{{ route('beranda', ['q' => 'Raja Ampat']) }}#katalog">Raja Ampat &amp; Misool</a></li>
                    <li><a href="{{ route('beranda', ['q' => 'Karimunjawa']) }}#katalog">Surga Laut Karimunjawa</a></li>
                    <li><a href="{{ route('beranda', ['q' => 'Ijen']) }}#katalog">Kawah Ijen &amp; Baluran</a></li>
                    <li><a href="{{ route('beranda', ['q' => 'Banda Neira']) }}#katalog">Banda Neira Jalur Rempah</a></li>
                </ul>
            </div>

            {{-- Kolom 3: Kategori Layanan --}}
            <div class="footer-col">
                <h4>Layanan Wisata</h4>
                <ul class="footer-links-list">
                    <li><a href="{{ route('beranda', ['tipe' => ['Open Trip']]) }}#katalog">Open Trip (Bisa Solo Traveler)</a></li>
                    <li><a href="{{ route('beranda', ['tipe' => ['Private Trip']]) }}#katalog">Private Tour Eksklusif</a></li>
                    <li><a href="{{ route('beranda', ['tipe' => ['Honeymoon']]) }}#katalog">Paket Bulan Madu (Honeymoon)</a></li>
                    <li><a href="https://wa.me/6281234567890?text=Halo%20Nanta%20Travel,%20saya%20ingin%20konsultasi%20gathering%20rombongan" target="_blank">Corporate Gathering &amp; Outing</a></li>
                    <li><a href="https://wa.me/6281234567890?text=Halo%20Nanta%20Travel,%20saya%20mau%20request%20rute%20sendiri" target="_blank">Request Rute Sendiri</a></li>
                </ul>
            </div>

            {{-- Kolom 4: Kontak & Kantor Pusat --}}
            <div class="footer-col">
                <h4>Kontak &amp; Layanan CS</h4>
                <div class="footer-contact-item">
                    <span class="ico">📍</span>
                    <span>Gedung Nanta Nusantara, Jl. Slamet Riyadi No. 188, Surakarta, Jawa Tengah</span>
                </div>
                <div class="footer-contact-item">
                    <span class="ico">📞</span>
                    <span><a href="https://wa.me/6281234567890" target="_blank">+62 812-3456-7890 (CS 24 Jam)</a></span>
                </div>
                <div class="footer-contact-item">
                    <span class="ico">✉️</span>
                    <span><a href="mailto:halo@nanta.id">halo@nanta.id</a></span>
                </div>
                <div class="footer-contact-item">
                    <span class="ico">⏰</span>
                    <span>Siaga 24 Jam Setiap Hari (Senin - Minggu)</span>
                </div>
            </div>
        </div>

        {{-- Baris Metode Pembayaran Terverifikasi --}}
        <div class="footer-payments-bar">
            <span style="font-size:12px;color:#94a3b8;font-weight:700">Metode Pembayaran Resmi (a.n. PT Nanta Ekspedisi Nusantara):</span>
            <div class="payment-badges-list">
                <span class="bank-pill">BCA VA</span>
                <span class="bank-pill">MANDIRI</span>
                <span class="bank-pill">BNI</span>
                <span class="bank-pill">BRI</span>
            </div>
        </div>

        {{-- Bottom Legal Bar --}}
        <div class="footer-bottom-bar">
            <span>© {{ date('Y') }} PT Nanta Ekspedisi Nusantara (Nanta Travel). Seluruh hak cipta dilindungi.</span>
            <div style="display:flex;gap:14px">
                <a href="{{ route('halaman.syarat') }}">Syarat &amp; Ketentuan</a>
                <span>·</span>
                <a href="{{ route('halaman.privasi') }}">Kebijakan Privasi</a>
                <span>·</span>
                <a href="{{ route('halaman.faq') }}">Pusat Bantuan &amp; FAQ</a>
            </div>
        </div>
    </div>
</footer>
@endif

<nav class="bottom-nav">
    <a href="{{ route('beranda') }}" class="{{ request()->routeIs('beranda') ? 'active' : '' }}"><span class="ico">{!! view('partials.icon', ['name' => 'home', 'size' => 15])->render() !!}</span> Beranda</a>
    <a href="{{ auth()->check() ? route('wishlist.index') : route('login') }}" class="{{ request()->routeIs('wishlist.*') ? 'active' : '' }}"><span class="ico">{!! view('partials.icon', ['name' => 'heart', 'size' => 15])->render() !!}</span> Wishlist</a>
    <a href="{{ auth()->check() ? route('pemesanan.index') : route('login') }}" class="{{ request()->routeIs('pemesanan.*') ? 'active' : '' }}"><span class="ico">{!! view('partials.icon', ['name' => 'ticket', 'size' => 15])->render() !!}</span> Pesanan</a>
    <a href="{{ auth()->check() ? route('profil.edit') : route('login') }}" class="{{ request()->routeIs('profil.*') ? 'active' : '' }}"><span class="ico">{!! view('partials.icon', ['name' => 'user', 'size' => 15])->render() !!}</span> Profil</a>
</nav>

<script>
// Tampilkan nama berkas terpilih (ganti teks native "No file chosen")
document.addEventListener('change', function (e) {
    const el = e.target;
    if (!el || el.type !== 'file') return;
    let out = el.parentElement.querySelector('.file-name');
    if (!out) {
        out = document.createElement('span');
        out.className = 'file-name';
        el.insertAdjacentElement('afterend', out);
    }
    if (el.files && el.files.length) {
        out.textContent = '{!! view('partials.icon', ['name' => 'paperclip', 'size' => 15])->render() !!} ' + el.files[0].name + ' (' + Math.round(el.files[0].size / 1024) + ' KB)';
        out.classList.remove('empty');
    } else {
        out.textContent = 'Belum ada berkas dipilih';
        out.classList.add('empty');
    }
});
</script>
</body>
</html>
