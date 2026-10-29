@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
<section class="hero">
    <div class="container">
        <div class="hero-inner">
            <div class="hero-badge">★ Biro perjalanan terpercaya sejak 2015</div>
            <h1>Temukan perjalanan yang pantas untuk Anda ceritakan.</h1>
            <p>Pilih paket, cek sisa kuota secara real-time, unggah berkas, dan terima e-voucher ber-QR Code — semuanya dalam satu sistem.</p>

            <form method="GET" action="{{ route('beranda') }}" class="searchbar">
                <div class="field">
                    <label>Cari paket / destinasi</label>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Bali, Bromo, Komodo...">
                </div>
                <div class="field">
                    <label>Destinasi</label>
                    <select name="destinasi">
                        <option value="">Semua destinasi</option>
                        @foreach($destinasi as $d)
                            <option value="{{ $d->id }}" @selected(request('destinasi') == $d->id)>{{ $d->nama_destinasi }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label>Tanggal berangkat</label>
                    <input type="date" name="tanggal" value="{{ request('tanggal') }}">
                </div>
                <button class="btn btn-primary" type="submit" style="align-self:center;margin:4px">Cari</button>
            </form>
        </div>
    </div>
</section>

<div class="container" style="margin-top:-34px;position:relative;z-index:3">
    <div class="grid grid-4">
        @foreach([
            ['ico' => '🗺', 'val' => $paket->total().'+', 'lbl' => 'Paket wisata aktif'],
            ['ico' => '👥', 'val' => '20', 'lbl' => 'Kuota per keberangkatan'],
            ['ico' => '⏱', 'val' => '1 jam', 'lbl' => 'Batas konfirmasi bayar'],
            ['ico' => '🎫', 'val' => 'QR', 'lbl' => 'E-voucher tervalidasi'],
        ] as $s)
            <div class="card stat">
                <div class="ico">{{ $s['ico'] }}</div>
                <div>
                    <div class="val">{{ $s['val'] }}</div>
                    <div class="lbl">{{ $s['lbl'] }}</div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<div class="container" style="padding:44px 20px 0">
    <div class="section-head">
        <div>
            <h2>Paket wisata pilihan</h2>
            <p class="muted mb-0">Harga transparan, kuota ditampilkan langsung dari basis data.</p>
        </div>
        <div class="chips">
            <a class="chip {{ request('destinasi') ? '' : 'active' }}" href="{{ route('beranda') }}">Semua</a>
            @foreach($destinasi->take(4) as $d)
                <a class="chip {{ request('destinasi') == $d->id ? 'active' : '' }}"
                   href="{{ route('beranda', ['destinasi' => $d->id]) }}">{{ $d->nama_destinasi }}</a>
            @endforeach
        </div>
    </div>

    @if($paket->isEmpty())
        <div class="card empty">
            <div class="big">🧭</div>
            <h3>Data paket belum tersedia</h3>
            <p class="mb-0">Coba ubah kata kunci pencarian atau filter destinasi.</p>
        </div>
    @else
        <div class="grid grid-3">
            @foreach($paket as $p)
                @php($jadwal = $p->jadwalTersedia->first())
                <article class="card paket-card">
                    <a href="{{ route('paket.detail', $p) }}" class="paket-thumb">
                        @if($p->foto)
                            <img src="{{ Storage::disk('public')->url($p->foto) }}" alt="{{ $p->nama_paket }}">
                        @endif
                        <span class="tag">{{ $p->destinasi->nama_destinasi }}</span>
                        <span class="durasi">{{ $p->durasi_hari }} Hari</span>
                    </a>
                    <div class="paket-body">
                        <a href="{{ route('paket.detail', $p) }}" class="paket-title">{{ $p->nama_paket }}</a>
                        <div class="paket-loc">📍 {{ $p->destinasi->lokasi }}</div>
                        <div class="tiny muted">
                            @if($jadwal)
                                Berangkat {{ $jadwal->tanggal_berangkat->translatedFormat('d M Y') }}
                                · sisa {{ $jadwal->sisa_kuota }} kursi
                            @else
                                Belum ada jadwal tersedia
                            @endif
                        </div>
                        <div class="paket-price">
                            <div>
                                <div class="harga">Rp {{ number_format($p->tarif, 0, ',', '.') }}</div>
                                <div class="tiny muted">per peserta</div>
                            </div>
                            <a href="{{ route('paket.detail', $p) }}" class="btn btn-soft btn-sm">Detail</a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="pagination">
            @if($paket->onFirstPage())
                <span class="disabled">‹</span>
            @else
                <a href="{{ $paket->previousPageUrl() }}">‹</a>
            @endif
            @foreach($paket->getUrlRange(max(1, $paket->currentPage() - 2), min($paket->lastPage(), $paket->currentPage() + 2)) as $page => $url)
                <a href="{{ $url }}" class="{{ $page == $paket->currentPage() ? 'active' : '' }}">{{ $page }}</a>
            @endforeach
            @if($paket->hasMorePages())
                <a href="{{ $paket->nextPageUrl() }}">›</a>
            @else
                <span class="disabled">›</span>
            @endif
        </div>
    @endif
</div>

<div class="container" style="padding:52px 20px 0">
    <div class="grid grid-3">
        @foreach([
            ['n' => '01', 't' => 'Pesan & unggah berkas', 'd' => 'Pilih jadwal, isi data peserta, unggah KTP/Paspor. Kuota langsung ditahan untuk Anda.'],
            ['n' => '02', 't' => 'Bayar dalam 1 jam', 'd' => 'Transfer sesuai tagihan lalu unggah bukti. Lewat dari 1 jam, pesanan kedaluwarsa otomatis.'],
            ['n' => '03', 't' => 'Verifikasi & e-voucher', 'd' => 'Admin memverifikasi dokumen, lalu voucher PDF ber-QR Code terbit otomatis.'],
        ] as $step)
            <div class="card card-pad">
                <div style="font-size:13px;font-weight:800;color:var(--primary)">{{ $step['n'] }}</div>
                <h3 class="mt-8">{{ $step['t'] }}</h3>
                <p class="muted mb-0" style="font-size:13.5px">{{ $step['d'] }}</p>
            </div>
        @endforeach
    </div>
</div>
@endsection
