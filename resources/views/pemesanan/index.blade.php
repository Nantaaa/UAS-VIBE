@extends('layouts.app')

@section('title', 'Pesanan Saya')

@section('content')
<div class="container" style="padding-top:26px">
    <div class="row between wrap" style="gap:14px;align-items:flex-start">
        <div>
            <h1 style="font-size:24px;margin:0 0 4px">Pesanan &amp; Reservasi Saya</h1>
            <p class="muted mb-0" style="font-size:12.5px">Kelola semua pemesanan dan reservasi perjalanan Anda di satu tempat.</p>
        </div>
        <div class="row" style="gap:8px">
            <a href="https://wa.me/6281234567890" class="btn btn-ghost btn-sm">Hubungi Kami</a>
            <a href="{{ route('beranda') }}#katalog" class="btn btn-primary btn-sm">Buat Pesanan Baru</a>
        </div>
    </div>

    {{-- Filter panel --}}
    <div class="card card-body mt-16">
        <div class="row between wrap" style="gap:12px">
            <div class="tabs" style="flex-wrap:wrap">
                @foreach([
                    'semua' => 'Semua',
                    'menunggu_pembayaran' => 'Menunggu Pembayaran',
                    'diproses' => 'Diproses',
                    'selesai' => 'Selesai',
                    'dibatalkan' => 'Dibatalkan',
                ] as $key => $label)
                    <a href="{{ route('pemesanan.index', ['tab' => $key]) }}"
                       class="{{ $tab === $key ? 'active' : '' }}">{{ $label }} ({{ $hitung[$key] }})</a>
                @endforeach
            </div>

            <form method="GET" class="row" style="gap:8px;flex-wrap:wrap">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari pesanan..." style="min-width:200px">
                <select name="urut" onchange="this.form.submit()" style="width:auto">
                    <option value="terbaru" @selected(request('urut') !== 'terlama')>Terbaru</option>
                    <option value="terlama" @selected(request('urut') === 'terlama')>Terlama</option>
                </select>
                <button class="btn btn-teal btn-sm" type="submit">Terapkan Filter</button>
            </form>
        </div>
    </div>

    @if($daftar->isEmpty())
        <div class="card empty mt-16">
            <div class="big">🎫</div>
            <h3>Belum ada pesanan pada kategori ini</h3>
            <p class="mb-0">Mulai jelajahi paket wisata dan buat pemesanan pertama Anda.</p>
            <a href="{{ route('beranda') }}#katalog" class="btn btn-primary mt-16">Lihat Paket Wisata</a>
        </div>
    @else
        <div class="stack mt-16">
            @foreach($daftar as $p)
                @php
                    $grup = match ($p->status_pemesanan) {
                        'menunggu_pembayaran' => ['orange', 'Menunggu Pembayaran'],
                        'menunggu_verifikasi' => ['blue', 'Diproses — Menunggu Verifikasi'],
                        'perlu_perbaikan' => ['red', 'Perlu Perbaikan Dokumen'],
                        'terkonfirmasi' => ['green', 'Selesai — Terkonfirmasi'],
                        default => ['red', $p->labelStatus()],
                    };
                @endphp

                <article class="order-card">
                    <div class="order-thumb">
                        @if($p->jadwal->paket->foto)
                            <img src="{{ Storage::disk('public')->url($p->jadwal->paket->foto) }}" alt="">
                        @endif
                    </div>
                    <div class="order-body">
                        <div class="row between wrap" style="gap:8px">
                            <span class="badge badge-{{ $p->badgeStatus() }}">{{ $p->labelStatus() }}</span>
                            <span class="order-code">#{{ $p->kode_pemesanan }}</span>
                        </div>

                        <div class="order-title">{{ $p->jadwal->paket->nama_paket }}</div>

                        <div class="order-meta">
                            <span>📍 {{ $p->jadwal->paket->destinasi->nama_destinasi }}</span>
                            <span>🗓 {{ $p->jadwal->tanggal_berangkat->translatedFormat('d M Y') }}</span>
                            <span>👥 {{ $p->jumlah_peserta }} peserta</span>
                            <span>⏳ {{ $p->jadwal->paket->durasi_hari }} hari</span>
                        </div>

                        @if($p->status_pemesanan === 'menunggu_pembayaran' && $p->sisaDetikPembayaran() > 0)
                            <div class="status-strip orange" data-countdown="{{ $p->sisaDetikPembayaran() }}">
                                ⏱ Selesaikan pembayaran dalam <span class="cd">{{ gmdate('H:i:s', $p->sisaDetikPembayaran()) }}</span>
                            </div>
                        @elseif($p->status_pemesanan === 'perlu_perbaikan')
                            <div class="status-strip red">✎ {{ Str::limit($p->catatan_status, 90) }}</div>
                        @endif

                        <div class="order-foot">
                            <div class="order-price">Rp {{ number_format($p->total_bayar, 0, ',', '.') }}</div>
                            <div class="row" style="gap:7px;flex-wrap:wrap">
                                <a href="{{ route('pemesanan.show', $p) }}" class="btn btn-ghost btn-sm">Detail Pesanan</a>

                                @if($p->status_pemesanan === 'menunggu_pembayaran' || $p->status_pemesanan === 'perlu_perbaikan')
                                    <a href="{{ route('pemesanan.pembayaran', $p) }}" class="btn btn-primary btn-sm">Bayar Sekarang</a>
                                @elseif($p->status_pemesanan === 'terkonfirmasi')
                                    <a href="{{ route('pemesanan.voucher', $p) }}" class="btn btn-teal btn-sm">E-Voucher</a>
                                @elseif(in_array($p->status_pemesanan, ['dibatalkan', 'kedaluwarsa']))
                                    <a href="{{ route('paket.detail', $p->jadwal->id_paket) }}" class="btn btn-ghost btn-sm">Pesan Lagi</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="pagination">
            @if($daftar->onFirstPage()) <span class="disabled">‹</span> @else <a href="{{ $daftar->previousPageUrl() }}">‹</a> @endif
            @foreach($daftar->getUrlRange(1, $daftar->lastPage()) as $page => $url)
                <a href="{{ $url }}" class="{{ $page == $daftar->currentPage() ? 'active' : '' }}">{{ $page }}</a>
            @endforeach
            @if($daftar->hasMorePages()) <a href="{{ $daftar->nextPageUrl() }}">›</a> @else <span class="disabled">›</span> @endif
        </div>
    @endif

    <div class="cta-banner mt-24">
        <div>
            <span class="hero-badge">BUTUH BANTUAN PERENCANAAN?</span>
            <h2>Butuh bantuan merencanakan perjalanan?</h2>
            <p>Tim kami siap membantu menyusun itinerary impian Anda — konsultasi gratis, tanpa komitmen.</p>
        </div>
        <a href="https://wa.me/6281234567890" class="btn btn-primary">Konsultasi Gratis</a>
    </div>
</div>

<script>
document.querySelectorAll('[data-countdown]').forEach(el => {
    let s = parseInt(el.dataset.countdown, 10);
    const span = el.querySelector('.cd');
    const tick = () => {
        if (s <= 0) { span.textContent = 'kedaluwarsa'; return; }
        const h = String(Math.floor(s / 3600)).padStart(2, '0');
        const m = String(Math.floor((s % 3600) / 60)).padStart(2, '0');
        const x = String(s % 60).padStart(2, '0');
        span.textContent = `${h}:${m}:${x}`;
        s--; setTimeout(tick, 1000);
    };
    tick();
});
</script>
@endsection
