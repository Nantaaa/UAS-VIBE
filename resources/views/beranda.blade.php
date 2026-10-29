@extends('layouts.app')

@section('title', 'Beranda')

@php($user = auth()->user())

@section('content')
<div class="container">
    <div class="crumb">
        <span><a href="{{ route('beranda') }}">Beranda</a> / <strong>Paket Wisata</strong></span>
        <span class="tiny muted">Tersimpan di halaman: Beranda</span>
    </div>

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
                <button type="button" class="btn hs-secondary" onclick="this.form.q.value='';this.form.submit()">Cari Sekarang</button>
                <button type="submit" class="btn btn-primary">Cari Paket</button>
            </form>

            <div class="hero-chips">
                @foreach($destinasi->take(6) as $d)
                    <a class="hero-chip {{ in_array($d->id, (array) request('destinasi', [])) ? 'active' : '' }}"
                       href="{{ route('beranda', ['destinasi' => [$d->id]]) }}">{{ $d->nama_destinasi }}</a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- BODY: filter + hasil --}}
    <div class="catalog-body">
        <aside class="filter-card">
            <div class="filter-head">
                <strong>⚙ Filter Pencarian</strong>
                <a href="{{ route('beranda') }}" class="tiny" style="color:#2563eb;font-weight:700">Reset</a>
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
                           value="{{ request('harga_max', 5000000) }}" oninput="syncHarga(this.value)">
                    <div class="row between tiny muted" style="margin-top:2px">
                        <span>Rp 0</span><span>Rp 10.000.000</span>
                    </div>
                    <div class="row" style="gap:8px;margin-top:8px">
                        <input type="number" name="harga_min" id="hargaMin" value="{{ request('harga_min', 0) }}" placeholder="Min">
                        <input type="number" name="harga_max" id="hargaMax" value="{{ request('harga_max', 5000000) }}" placeholder="Max">
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
                <button class="btn btn-teal btn-block" type="submit">✓ Terapkan Filter</button>
            </form>
        </aside>

        <div>
            <div class="results-head">
                <div>
                    <h2 style="margin:0;font-size:17px">Menampilkan {{ $paket->total() }} dari {{ $totalPaket }} Paket Wisata</h2>
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
                    <div class="view-toggle">
                        <button class="active" type="button">{!! view('partials.icon', ['name' => 'grid', 'size' => 15])->render() !!}</button>
                        <button type="button">{!! view('partials.icon', ['name' => 'list', 'size' => 15])->render() !!}</button>
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
                <div class="grid grid-3">
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
                                <div class="row between" style="gap:6px">
                                    <div class="katalog-meta">{{ $p->highlight ?? ($p->durasi_hari.' Hari '.($p->durasi_hari-1).' Malam') }}</div>
                                    <div class="card-rating">{!! view('partials.icon', ['name' => 'star', 'size' => 15])->render() !!} 4.9</div>
                                </div>
                                <div class="katalog-price">
                                    <div>
                                        <div class="now">Rp {{ number_format($p->tarif, 0, ',', '.') }}</div>
                                        @if($p->harga_asli)
                                            <div class="was">Rp {{ number_format($p->harga_asli, 0, ',', '.') }}</div>
                                        @endif
                                    </div>
                                    <a href="{{ route('paket.detail', $p) }}" class="btn btn-soft btn-sm">Detail</a>
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
                            @foreach([6, 9, 12] as $n)
                                <option value="{{ $n }}" @selected(request('per_halaman', 6) == $n)>Tampilkan: {{ $n }} per halaman</option>
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
            <span class="hero-badge">BUTUH ITINERARY PRIVATE?</span>
            <h2>Buat Itinerary Custom atau Private Group?</h2>
            <p>Tim Nanta Travel siap membantu merancang perjalanan impian Anda. Konsultasi gratis, itinerary personal, dan harga terbaik untuk grup.</p>
        </div>
        <div class="row" style="gap:10px">
            <a href="https://wa.me/6281234567890" class="btn btn-primary">Hubungi via WhatsApp</a>
            <a href="{{ route('beranda') }}#katalog" class="btn btn-ghost" style="background:transparent;color:#fff;border-color:rgba(255,255,255,.4)">Request Itinerary</a>
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
</script>
@endsection
