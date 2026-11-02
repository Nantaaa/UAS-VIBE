@extends('layouts.app')

@section('title', 'Beranda')

@php($user = auth()->user())

@section('content')
<div class="container" style="padding-top:16px">

    {{-- 1. HERO SECTION & FLOATING SEARCH BOX (TRAVELOKA STYLE) --}}
    <section class="hero-traveloka" style="background-image: url('{{ asset('storage/destinasi/nusa_penida.jpg') }}');">
        <div class="hero-content">
            <span class="hero-badge-tvlk">✦ PILIHAN UTAMA WISATA NUSANTARA</span>
            <h1 class="hero-title-tvlk">Pilihan Utama untuk Jelajahi Keindahan Nusantara</h1>
            <p class="hero-sub-tvlk">
                Temukan paket Open Trip, Private Tour, dan Honeymoon impianmu dengan harga terjangkau, fasilitas terjamin, dan ramah solo traveler.
            </p>

            {{-- Floating Search Card --}}
            <div class="search-box-card">
                <form method="GET" action="{{ route('beranda') }}#katalog" id="heroSearchForm">
                    {{-- Tab Kategori Tipe Trip --}}
                    <div class="search-tabs">
                        @php($tipeAktif = request('tipe.0', ''))
                        <label class="search-tab-item {{ empty($tipeAktif) ? 'active' : '' }}">
                            <input type="radio" name="tipe[]" value="" style="display:none" onchange="this.form.submit()" {{ empty($tipeAktif) ? 'checked' : '' }}>
                            {!! view('partials.icon', ['name' => 'compass', 'size' => 14])->render() !!} Semua Paket
                        </label>
                        <label class="search-tab-item {{ $tipeAktif === 'Open Trip' ? 'active' : '' }}">
                            <input type="radio" name="tipe[]" value="Open Trip" style="display:none" onchange="this.form.submit()" {{ $tipeAktif === 'Open Trip' ? 'checked' : '' }}>
                            {!! view('partials.icon', ['name' => 'users', 'size' => 14])->render() !!} Open Trip (Bisa Solo)
                        </label>
                        <label class="search-tab-item {{ $tipeAktif === 'Private Trip' ? 'active' : '' }}">
                            <input type="radio" name="tipe[]" value="Private Trip" style="display:none" onchange="this.form.submit()" {{ $tipeAktif === 'Private Trip' ? 'checked' : '' }}>
                            {!! view('partials.icon', ['name' => 'shield', 'size' => 14])->render() !!} Private Trip
                        </label>
                        <label class="search-tab-item {{ $tipeAktif === 'Honeymoon' ? 'active' : '' }}">
                            <input type="radio" name="tipe[]" value="Honeymoon" style="display:none" onchange="this.form.submit()" {{ $tipeAktif === 'Honeymoon' ? 'checked' : '' }}>
                            {!! view('partials.icon', ['name' => 'heart', 'size' => 14])->render() !!} Honeymoon
                        </label>
                    </div>

                    {{-- Form Grid Input Terpadu --}}
                    <div class="search-inputs-grid">
                        {{-- Kolom Destinasi --}}
                        <div class="s-input-group">
                            <label class="s-input-label" for="searchQ">
                                {!! view('partials.icon', ['name' => 'map-pin', 'size' => 12])->render() !!} Destinasi Wisata
                            </label>
                            <input type="text" id="searchQ" name="q" value="{{ request('q') }}"
                                   class="s-input-control" placeholder="Mau liburan ke mana? (misal: Bromo, Komodo)">
                        </div>

                        {{-- Kolom Tanggal Berangkat --}}
                        <div class="s-input-group">
                            <label class="s-input-label" for="searchTgl">
                                {!! view('partials.icon', ['name' => 'calendar', 'size' => 12])->render() !!} Tanggal Berangkat
                            </label>
                            <input type="date" id="searchTgl" name="tanggal" value="{{ request('tanggal') }}"
                                   class="s-input-control">
                        </div>

                        {{-- Kolom Pilihan Durasi / Peserta --}}
                        <div class="s-input-group">
                            <label class="s-input-label" for="searchDurasi">
                                {!! view('partials.icon', ['name' => 'clock', 'size' => 12])->render() !!} Durasi Liburan
                            </label>
                            <select id="searchDurasi" name="durasi[]" class="s-input-control">
                                <option value="">Semua Durasi</option>
                                <option value="1" @selected(in_array('1', (array) request('durasi', [])))>1 Hari (Midnight Express)</option>
                                <option value="2" @selected(in_array('2', (array) request('durasi', [])))>2 Hari 1 Malam</option>
                                <option value="3" @selected(in_array('3', (array) request('durasi', [])))>3 Hari 2 Malam</option>
                                <option value="4" @selected(in_array('4', (array) request('durasi', [])))>4 Hari 3 Malam</option>
                                <option value="5" @selected(in_array('5', (array) request('durasi', [])))>5 Hari 4 Malam</option>
                            </select>
                        </div>

                        {{-- Tombol Submit --}}
                        <button type="submit" class="btn-search-exec">
                            {!! view('partials.icon', ['name' => 'search', 'size' => 16])->render() !!}
                            <span>Cari Paket</span>
                        </button>
                    </div>

                    {{-- Trust Badges Bar --}}
                    <div class="search-trust-bar">
                        <div class="trust-item">
                            <span class="icon">✓</span> 100% Jadwal Pasti Berangkat
                        </div>
                        <div class="trust-item">
                            <span class="icon">✓</span> Tour Guide &amp; Nahkoda Berlisensi
                        </div>
                        <div class="trust-item">
                            <span class="icon">✓</span> Pembayaran Resmi &amp; Terverifikasi
                        </div>
                        <div class="trust-item">
                            <span class="icon">✓</span> Ramah Solo Traveler (Bisa 1 Orang)
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    {{-- 2. SECTION DESTINASI POPULER (PHOTO TILES) --}}
    <section class="destinations-section">
        <div class="section-head-tvlk">
            <h2 class="section-title-tvlk">
                <span>🏝️ Destinasi Favorit Wisatawan</span>
            </h2>
            <a href="{{ route('beranda') }}#katalog" class="tiny" style="color:#0284c7;font-weight:700">Lihat Semua Destinasi →</a>
        </div>
        <div class="dest-scroll-grid">
            @foreach($destinasi->take(8) as $d)
                <a href="{{ route('beranda', ['destinasi' => [$d->id]]) }}#katalog" class="dest-card-tvlk">
                    <img src="{{ asset('storage/' . $d->foto) }}" alt="{{ $d->nama_destinasi }}" loading="lazy">
                    <div class="dest-card-info">
                        <h4 class="dest-card-name">{{ $d->nama_destinasi }}</h4>
                        <div class="dest-card-count">{{ $d->paket_count ?? 1 }} Paket Trip</div>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    {{-- 3. BANNER EKSPLORASI NUSANTARA --}}
    <section class="mega-promo-banner">
        <div class="mega-promo-content">
            <span class="badge" style="background:#fef08a;color:#854d0e;font-size:10px;font-weight:800;padding:3px 8px;border-radius:4px;margin-bottom:6px;display:inline-block">
                ⚡ EKSPLORASI NUSANTARA 2026
            </span>
            <h3>Jelajahi Sudut Nusantara dengan Layanan Terbaik</h3>
            <p>Nikmati pengalaman liburan tak terlupakan dengan fasilitas kapal &amp; hotel terverifikasi, serta jaminan keberangkatan aman bersama pemandu lokal berlisensi.</p>
        </div>
        <div>
            <a href="{{ route('beranda', ['urutkan' => 'termurah']) }}#katalog" class="btn" style="background:#ffffff;color:#0369a1;font-weight:800;padding:12px 20px;border-radius:10px;text-decoration:none;white-space:nowrap;box-shadow:0 4px 14px rgba(0,0,0,0.15)">
                Cari Paket Termurah →
            </a>
        </div>
    </section>

    {{-- 4. BODY: FILTER LANJUTAN + HASIL KATALOG PAKET --}}
    <div class="catalog-body" id="katalog">
        <aside class="filter-card">
            <div class="filter-head">
                <strong>{!! view('partials.icon', ['name' => 'filter', 'size' => 14])->render() !!} Filter Lanjutan</strong>
                <div class="row" style="gap:8px;align-items:center">
                    <a href="{{ route('beranda') }}" class="filter-reset">Reset</a>
                    <button type="button" class="filter-collapse" id="filterToggle" aria-label="Sembunyikan filter">
                        {!! view('partials.icon', ['name' => 'chevron-down', 'size' => 14])->render() !!}
                    </button>
                </div>
            </div>

            <form method="GET" action="{{ route('beranda') }}" id="formFilter">
                <div class="filter-block">
                    <div class="filter-title">Destinasi</div>
                    <input type="text" placeholder="Cari destinasi..." class="filter-search" oninput="filterDestinasi(this.value)">
                    <div class="checklist" id="listDestinasi">
                        @foreach($destinasi as $d)
                            <label class="check">
                                <input type="checkbox" name="destinasi[]" value="{{ $d->id }}"
                                       @checked(in_array($d->id, (array) request('destinasi', [])))>
                                <span>{{ $d->nama_destinasi }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="filter-block">
                    <div class="filter-title">Tipe Perjalanan</div>
                    <div class="checklist">
                        @foreach($tipeTrip as $tipe)
                            <label class="check">
                                <input type="checkbox" name="tipe[]" value="{{ $tipe }}"
                                       @checked(in_array($tipe, (array) request('tipe', [])))>
                                <span>{{ $tipe }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="filter-block">
                    <div class="filter-title">Rentang Harga (per orang)</div>
                    <input type="range" min="0" max="10000000" step="250000" id="rangeHarga"
                           value="{{ request('harga_max', 10000000) }}" oninput="syncHarga(this.value)">
                    <div class="row between tiny muted" style="margin-top:2px">
                        <span>Rp 0</span><span>Rp 10.000.000</span>
                    </div>
                    <div class="row" style="gap:8px;margin-top:8px">
                        <label class="rp-input"><span>Rp</span>
                            <input type="number" name="harga_min" id="hargaMin" value="{{ request('harga_min', 0) }}" placeholder="0">
                        </label>
                        <label class="rp-input"><span>Rp</span>
                            <input type="number" name="harga_max" id="hargaMax" value="{{ request('harga_max', 10000000) }}" placeholder="10.000.000">
                        </label>
                    </div>
                </div>

                <div class="filter-block">
                    <div class="filter-title">Tanggal Keberangkatan</div>
                    <input type="date" name="tanggal" value="{{ request('tanggal') }}">
                </div>

                <div class="filter-block">
                    <div class="filter-title">Durasi Trip</div>
                    <div class="row" style="gap:8px">
                        @foreach([1, 2, 3, 4, 5] as $durasi)
                            <label class="durasi-box">
                                <input type="checkbox" name="durasi[]" value="{{ $durasi }}"
                                       @checked(in_array($durasi, (array) request('durasi', [])))>
                                <span>{{ $durasi }}H</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <input type="hidden" name="urutkan" value="{{ request('urutkan') }}">
                <button class="btn btn-primary btn-block" type="submit">{!! view('partials.icon', ['name' => 'check', 'size' => 14])->render() !!} Terapkan Filter</button>
            </form>
        </aside>

        <div>
            <?php
                $aktif = [];
                foreach ((array) request('destinasi', []) as $idD) {
                    $d = $destinasi->firstWhere('id', $idD);
                    if ($d) $aktif[] = ['label' => $d->nama_destinasi, 'key' => 'destinasi', 'val' => $idD];
                }
                foreach ((array) request('tipe', []) as $t) $aktif[] = ['label' => $t, 'key' => 'tipe', 'val' => $t];
                foreach ((array) request('durasi', []) as $du) $aktif[] = ['label' => $du.' Hari', 'key' => 'durasi', 'val' => $du];
                if (request('q')) $aktif[] = ['label' => '“'.request('q').'”', 'key' => 'q', 'val' => null];
                if (request('tanggal')) $aktif[] = ['label' => \Carbon\Carbon::parse(request('tanggal'))->translatedFormat('d M Y'), 'key' => 'tanggal', 'val' => null];
            ?>

            @if(!empty($aktif))
                <div class="active-chips">
                    <span class="tiny muted" style="margin-right:4px">Filter aktif:</span>
                    @foreach($aktif as $a)
                        <span class="active-chip">
                            {{ $a['label'] }}
                            <a href="{{ route('beranda', request()->except($a['key'])) }}">×</a>
                        </span>
                    @endforeach
                    <a href="{{ route('beranda') }}" class="tiny" style="color:var(--danger);font-weight:600;margin-left:4px">Hapus Semua</a>
                </div>
            @endif

            <div class="results-head">
                <div>
                    <h2 style="font-size:18px;margin:0 0 2px;font-weight:800">Daftar Paket Wisata</h2>
                    <span class="tiny muted">Ditemukan <strong>{{ $paket->total() }}</strong> pilihan perjalanan siap pesan</span>
                </div>
                <div class="row" style="gap:10px;align-items:center">
                    <form method="GET" class="row" style="gap:6px">
                        @foreach(request()->except('urutkan') as $k => $v)
                            @if(is_array($v))
                                @foreach($v as $vv)<input type="hidden" name="{{ $k }}[]" value="{{ $vv }}">@endforeach
                            @else
                                <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                            @endif
                        @endforeach
                        <select name="urutkan" onchange="this.form.submit()" style="font-size:12px;padding:8px 10px">
                            <option value="">Urutkan: Rekomendasi Terbaik</option>
                            <option value="termurah" @selected(request('urutkan') === 'termurah')>Harga Termurah</option>
                            <option value="termahal" @selected(request('urutkan') === 'termahal')>Harga Tertinggi</option>
                            <option value="terbaru" @selected(request('urutkan') === 'terbaru')>Terbaru</option>
                        </select>
                    </form>
                    <div class="view-toggle" id="viewToggle">
                        <button type="button" class="view-btn active" data-view="grid" title="Tampilan Grid" aria-label="Tampilan Grid">{!! view('partials.icon', ['name' => 'grid', 'size' => 15])->render() !!}</button>
                        <button type="button" class="view-btn" data-view="list" title="Tampilan Daftar" aria-label="Tampilan Daftar">{!! view('partials.icon', ['name' => 'list', 'size' => 15])->render() !!}</button>
                    </div>
                </div>
            </div>

            @if($paket->isEmpty())
                <div class="card empty">
                    <div class="big">{!! view('partials.icon', ['name' => 'compass', 'size' => 15])->render() !!}</div>
                    <h3>Paket tidak ditemukan</h3>
                    <p class="mb-0">Coba longgarkan filter atau ubah kata kunci pencarian Anda.</p>
                </div>
            @else
                <div class="grid grid-3" id="katalogGrid">
                    @foreach($paket as $p)
                        <article class="katalog-card">
                            <a href="{{ route('paket.detail', $p) }}" class="katalog-thumb">
                                @if($p->foto)
                                    <img src="{{ '/storage/'.$p->foto }}" alt="{{ $p->nama_paket }}" loading="lazy">
                                @endif
                                @if($p->is_bestseller)
                                    <span class="badge-seller">Best Seller</span>
                                @elseif($p->diskonPersen())
                                    <span class="badge-seller" style="background:#0d9488">-{{ $p->diskonPersen() }}%</span>
                                @endif

                                <span class="thumb-shade"></span>
                                @if(($p->jadwalTersedia->first()->sisa_kuota ?? 0) > 0)
                                    <span class="pill-tersedia">● Tersedia</span>
                                @else
                                    <span class="pill-tersedia" style="background:#fee2e2;color:#b91c1c">● Penuh</span>
                                @endif
                            </a>
                            @auth
                                <form method="POST" action="{{ route('wishlist.toggle', $p) }}" class="heart-form">
                                    @csrf
                                    <button type="submit" class="heart {{ in_array($p->id, $wishlistIds ?? []) ? 'active' : '' }}"
                                            title="{{ in_array($p->id, $wishlistIds ?? []) ? 'Hapus dari wishlist' : 'Simpan ke wishlist' }}">
                                        {!! view('partials.icon', ['name' => 'heart', 'size' => 15])->render() !!}
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="heart" title="Masuk untuk menyimpan">
                                    {!! view('partials.icon', ['name' => 'heart', 'size' => 15])->render() !!}
                                </a>
                            @endauth
                            <div class="katalog-body">
                                <a href="{{ route('paket.detail', $p) }}" class="katalog-title">{{ $p->nama_paket }}</a>
                                <div class="katalog-loc">{!! view('partials.icon', ['name' => 'map-pin', 'size' => 15])->render() !!} {{ $p->destinasi->lokasi }}</div>
                                
                                <div class="row between" style="gap:6px;align-items:center">
                                    <div class="katalog-meta">{{ $p->highlight ?? ($p->durasi_hari.' Hari '.($p->durasi_hari-1).' Malam') }}</div>
                                    <span class="badge" style="font-size:10.5px;padding:2px 6px;border-radius:4px;background:{{ $p->minPeserta() === 1 ? '#e0f2fe' : '#fef3c7' }};color:{{ $p->minPeserta() === 1 ? '#0369a1' : '#92400e' }};font-weight:700">
                                        Min. {{ $p->minPeserta() }} Pax{{ $p->minPeserta() === 1 ? ' (Bisa Solo)' : '' }}
                                    </span>
                                </div>
                                <div class="row between mt-4" style="gap:6px">
                                    <div class="tiny muted">{{ $p->durasi_hari }} Hari {{ max(1, $p->durasi_hari - 1) }} Malam · {{ $p->tipe_trip }}</div>
                                    @if(($p->peserta_terkonfirmasi ?? 0) > 0)
                                        <div class="card-rating" style="color:var(--teal-dark)">{!! view('partials.icon', ['name' => 'users', 'size' => 11])->render() !!} {{ (int) $p->peserta_terkonfirmasi }} peserta</div>
                                    @else
                                        <div class="card-rating" style="color:var(--muted)">Jadwal baru</div>
                                    @endif
                                </div>
                                <div class="katalog-price">
                                    <div>
                                        <div class="now">Rp {{ number_format($p->tarif, 0, ',', '.') }}</div>
                                        @if($p->harga_asli)
                                            <div class="was">Rp {{ number_format($p->harga_asli, 0, ',', '.') }}</div>
                                        @endif
                                    </div>
                                    <a href="{{ route('paket.detail', $p) }}" class="btn btn-detail btn-sm">Detail</a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="results-foot">
                    <span class="tiny muted">
                        Menampilkan {{ $paket->firstItem() }} - {{ $paket->lastItem() }} dari {{ $paket->total() }} paket
                    </span>
                    <div class="pagination" style="margin:0">
                        @if($paket->onFirstPage()) <span class="disabled">‹</span> @else <a href="{{ $paket->previousPageUrl() }}">‹</a> @endif
                        @foreach($paket->getUrlRange(1, min(4, $paket->lastPage())) as $page => $url)
                            <a href="{{ $url }}" class="{{ $page == $paket->currentPage() ? 'active' : '' }}">{{ $page }}</a>
                        @endforeach
                        @if($paket->lastPage() > 4)<span>…</span><a href="{{ $paket->url($paket->lastPage()) }}">{{ $paket->lastPage() }}</a>@endif
                        @if($paket->hasMorePages()) <a href="{{ $paket->nextPageUrl() }}">›</a> @else <span class="disabled">›</span> @endif
                    </div>
                    <form method="GET" class="row" style="gap:6px">
                        @foreach(request()->except(['per_halaman', 'page']) as $k => $v)
                            @if(is_array($v))
                                @foreach($v as $vv)<input type="hidden" name="{{ $k }}[]" value="{{ $vv }}">@endforeach
                            @else
                                <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                            @endif
                        @endforeach
                        <select name="per_halaman" onchange="this.form.submit()" style="font-size:11.5px;padding:6px 8px">
                            @foreach([9, 12, 18, 24] as $n)
                                <option value="{{ $n }}" @selected(request('per_halaman', 12) == $n)>Tampilkan: {{ $n }} per halaman</option>
                            @endforeach
                        </select>
                    </form>
                </div>
            @endif
        </div>
    </div>

    {{-- 6. SECTION 4 KEUNGGULAN (VALUE PROPOSITION) --}}
    <section class="features-section">
        <div class="features-grid">
            <div class="feature-item">
                <div class="feature-icon-box">🛡️</div>
                <div class="feature-text">
                    <h4>Jaminan Pasti Berangkat</h4>
                    <p>Jadwal terencana rapi tanpa pembatalan sepihak dan kuota terpantau real-time.</p>
                </div>
            </div>
            <div class="feature-item">
                <div class="feature-icon-box">👨‍✈️</div>
                <div class="feature-text">
                    <h4>Pemandu &amp; Kru Berlisensi</h4>
                    <p>Didampingi tour guide ramah, profesional, dan berpengalaman di tiap medan wisata.</p>
                </div>
            </div>
            <div class="feature-item">
                <div class="feature-icon-box">💎</div>
                <div class="feature-text">
                    <h4>Fasilitas Lengkap &amp; Jujur</h4>
                    <p>Harga transparan tanpa biaya siluman, tiket objek dan akomodasi tercover jelas.</p>
                </div>
            </div>
            <div class="feature-item">
                <div class="feature-icon-box">📞</div>
                <div class="feature-text">
                    <h4>Layanan CS 24 Jam Siaga</h4>
                    <p>Bantuan dan konsultasi perjalanan siap melayani Anda sebelum hingga selesai trip.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- 7. CTA BANNER KONSULTASI --}}
    <section class="cta-banner">
        <div>
            <span class="hero-badge">BUTUH RUTE KHUSUS?</span>
            <h2>Mau Liburan Rombongan atau Request Rute Sendiri?</h2>
            <p>Tim Nanta Travel siap membantu merancang perjalanan impian Anda. Konsultasi gratis, jadwal fleksibel, dan penawaran harga terbaik untuk rombongan kantor, keluarga, atau komunitas.</p>
        </div>
        <div class="row" style="gap:10px">
            <a href="https://wa.me/6281234567890?text=Halo%20Nanta%20Travel,%20saya%20ingin%20konsultasi%20paket%20wisata" class="btn btn-primary" target="_blank">Hubungi via WhatsApp</a>
            <a href="https://wa.me/6281234567890?text=Halo%20Nanta%20Travel,%20saya%20ingin%20request%20jadwal%20rombongan" class="btn btn-ghost" target="_blank" style="background:transparent;color:#fff;border-color:rgba(255,255,255,.4)">Konsultasi Rute</a>
        </div>
    </section>
</div>

<script>
function syncHarga(v) {
    document.getElementById('hargaMax').value = v;
}
function filterDestinasi(q) {
    q = q.toLowerCase();
    document.querySelectorAll('#listDestinasi .check').forEach(el => {
        el.style.display = el.innerText.toLowerCase().includes(q) ? '' : 'none';
    });
}

// Filter lanjutan bisa dilipat — di layar kecil default tertutup
(() => {
    const btn = document.getElementById('filterToggle');
    const card = document.querySelector('.filter-card');
    if (!btn || !card) return;
    if (window.matchMedia('(max-width: 900px)').matches) card.classList.add('collapsed');
    btn.addEventListener('click', () => card.classList.toggle('collapsed'));
})();

// Toggle tampilan Grid / List
(() => {
    const grid = document.getElementById('katalogGrid');
    const toggle = document.getElementById('viewToggle');
    if (!grid || !toggle) return;

    const btns = toggle.querySelectorAll('.view-btn');

    function setView(mode) {
        if (mode === 'list') {
            grid.classList.add('is-list');
            grid.classList.remove('grid-3');
        } else {
            grid.classList.remove('is-list');
            grid.classList.add('grid-3');
        }
        btns.forEach(b => {
            const match = b.dataset.view === mode;
            b.classList.toggle('active', match);
            b.setAttribute('aria-pressed', match ? 'true' : 'false');
        });
        try { localStorage.setItem('nanta_katalog_view', mode); } catch (e) {}
    }

    btns.forEach(b => {
        b.addEventListener('click', () => setView(b.dataset.view));
    });

    try {
        const saved = localStorage.getItem('nanta_katalog_view');
        if (saved === 'list') setView('list');
    } catch (e) {}
})();
</script>
@endsection
