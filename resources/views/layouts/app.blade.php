<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Nanta Travel') — Sistem Informasi Travel &amp; Agen Wisata</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/katalog.css') }}">
    <link rel="stylesheet" href="{{ asset('css/pages.css') }}">
    <link rel="stylesheet" href="{{ asset('css/ticket-profil.css') }}">
    <link rel="stylesheet" href="{{ asset('css/polish.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fixes.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body>
@hasSection('minimal')
@else
<header class="site-header">
    <div class="container">
        <a href="{{ route('beranda') }}" class="brand-h">
            <span class="brand-mark">N</span>
            <span class="brand-name">Nanta <strong>Travel</strong></span>
        </a>

        <nav class="nav-pills">
            <a href="{{ route('beranda') }}" class="{{ request()->routeIs('beranda') ? 'active' : '' }}">Beranda</a>
            <a href="{{ route('beranda') }}#katalog" class="{{ request()->routeIs('paket.*') ? 'active' : '' }}">Paket Wisata</a>
            <a href="{{ auth()->check() ? route('pemesanan.index') : route('login') }}" class="{{ request()->routeIs('pemesanan.*') ? 'active' : '' }}">Pesanan Saya</a>
            <a href="{{ route('beranda') }}#katalog">Wishlist</a>
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
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn-masuk" type="submit">Keluar</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn-masuk">Masuk</a>
                <a href="{{ route('register') }}" class="btn btn-ghost btn-sm hide-sm">Daftar</a>
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
<footer class="site-footer" id="bantuan">
    <div class="container">
        <div class="footer-grid">
            <div>
                <a href="{{ route('beranda') }}" class="brand-h" style="margin-bottom:10px">
                    <span class="brand-mark">N</span>
                    <span class="brand-name">Nanta <strong>Travel</strong></span>
                </a>
                <p>Menciptakan pengalaman perjalanan terbaik dengan destinasi pilihan di seluruh Indonesia.</p>
            </div>

            <div>
                <h4>Layanan</h4>
                <ul>
                    <li><a href="{{ route('beranda') }}#katalog">Paket Wisata</a></li>
                    <li><a href="{{ route('beranda') }}#katalog">Private Trip</a></li>
                    <li><a href="{{ route('beranda') }}#katalog">Open Trip</a></li>
                    <li><a href="{{ route('beranda') }}#katalog">Custom Itinerary</a></li>
                </ul>
            </div>

            <div>
                <h4>Bantuan</h4>
                <ul>
                    <li><a href="#">Syarat &amp; Ketentuan</a></li>
                    <li><a href="#">Kebijakan Privasi</a></li>
                    <li><a href="#">FAQ</a></li>
                    <li><a href="#">Pusat Bantuan</a></li>
                </ul>
            </div>

            <div>
                <h4>Kontak &amp; Alamat</h4>
                <ul>
                    <li>Jl. Jenderal Sudirman No. 88, Jakarta</li>
                    <li>+62 812-3456-7890</li>
                    <li><a href="mailto:hello@nantatravel.com">hello@nantatravel.com</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-legal">
            <span>© {{ date('Y') }} Nanta Travel. Seluruh hak cipta dilindungi.</span>
            <span>Syarat &amp; Ketentuan | Kebijakan Privasi</span>
        </div>
    </div>
</footer>
@endif

<nav class="bottom-nav">
    <a href="{{ route('beranda') }}" class="{{ request()->routeIs('beranda') ? 'active' : '' }}"><span class="ico">🏠</span> Beranda</a>
    <a href="{{ route('beranda') }}#katalog" class="{{ request()->routeIs('paket.*') ? 'active' : '' }}"><span class="ico">🧭</span> Paket</a>
    <a href="{{ auth()->check() ? route('pemesanan.index') : route('login') }}" class="{{ request()->routeIs('pemesanan.*') ? 'active' : '' }}"><span class="ico">🎫</span> Pesanan</a>
    <a href="{{ auth()->check() ? route('profil.edit') : route('login') }}" class="{{ request()->routeIs('profil.*') ? 'active' : '' }}"><span class="ico">👤</span> Profil</a>
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
        out.textContent = '📎 ' + el.files[0].name + ' (' + Math.round(el.files[0].size / 1024) + ' KB)';
        out.classList.remove('empty');
    } else {
        out.textContent = 'Belum ada berkas dipilih';
        out.classList.add('empty');
    }
});
</script>
</body>
</html>
