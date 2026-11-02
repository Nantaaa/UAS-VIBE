@extends('layouts.app')

@section('title', 'Beranda')

@php($user = auth()->user())

@section('content')
<div class="container" style="padding-top:18px">
    {{-- HERO --}}
    <section class="catalog-hero" style="background-image:
        linear-gradient(115deg, rgba(6,20,36,.94) 0%, rgba(8,32,52,.82) 45%, rgba(8,42,44,.70) 100%),
        url('/storage/paket/raja_ampat.jpg');
        background-size: cover; background-position: center;">
        <div class="catalog-hero-inner">
            <span class="hero-badge">✦ PILIHAN PAKET WISATA TERBAIK</span>
            <h1>Temukan Paket Wisata Impianmu</h1>
            <p>Jelajahi keindahan nusantara dengan paket wisata terbaik, harga terjangkau, dan pengalaman yang tak terlupakan.</p>

            <form method="GET" action="{{ route('beranda') }}" class="hero-search">
                <span class="hs-ico">{!! view('partials.icon', ['name' => 'search', 'size' => 15])->render() !!}</span>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari destinasi, paket, atau aktivitas...">
                <button type="submit" class="btn btn-primary hs-submit">
                    {!! view('partials.icon', ['name' => 'search', 'size' => 14])->render() !!} Cari Paket
                </button>
            </form>

            <div class="hero-chips">
                @foreach($destinasi->take(10) as $d)
                    <a class="hero-chip {{ in_array($d->id, (array) request('destinasi', [])) ? 'active' : '' }}"
                       href="{{ route('beranda', ['destinasi' => [$d->id]]) }}#katalog">{{ $d->nama_destinasi }}</a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- BODY: filter + hasil --}}
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
                        @foreach([2, 3, 4, 5] as $durasi)
                            <label class="durasi-box">
                                <input type="checkbox" name="durasi[]" value="{{ $durasi }}"
                                       @checked(in_array($durasi, (array) request('durasi', [])))>
                                <span>{{ $durasi }} Hari</span>
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

            @if(count($aktif))
                <div class="active-filters">
                    <span class="af-label">Filter aktif:</span>
                    @foreach($aktif as $f)
                        <a class="af-chip" href="{{ route('beranda', request()->except([$f['key']])) }}">
                            {{ $f['label'] }} <span aria-hidden="true">×</span>
                        </a>
                    @endforeach
                    <a class="af-clear" href="{{ route('beranda') }}">Hapus semua</a>
                </div>
            @endif

            <div class="results-head">
                <div>
                    <h2 style="margin:0;font-size:17px">{{ $paket->total() }} Paket Wisata Ditemukan</h2>
                    <div class="tiny muted">Jelajahi paket wisata terbaik dengan harga terbaik</div>
                </div>
                <div class="row" style="gap:8px">
                    <form method="GET" id="formUrut">
                        @foreach(request()->except(['urutkan', 'page']) as $k => $v)
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
                    <p class="mb-0">Coba longgarkan filter atau ubah kata kunci pencarian.</p>
                </div>
            @else
                <div class="grid grid-3" id="katalogGrid">
                    @foreach($paket as $p)
                        <article class="katalog-card">
                            <a href="{{ route('paket.detail', $p) }}" class="katalog-thumb">
                                @if($p->foto)
                                    <img src="{{ '/storage/'.$p->foto }}" alt="{{ $p->nama_paket }}">
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

    {{-- CTA --}}
    <section class="cta-banner">
        <div>
            <span class="hero-badge">BUTUH RUTE KHUSUS?</span>
            <h2>Mau Liburan Rombongan atau Rute Sendiri?</h2>
            <p>Tim Nanta Travel siap membantu merancang perjalanan impian Anda. Konsultasi gratis, jadwal fleksibel, dan harga terbaik untuk grup.</p>
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
