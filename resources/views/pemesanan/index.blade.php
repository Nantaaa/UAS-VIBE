@extends('layouts.app')

@section('title', 'Pemesanan Saya')

@section('content')
<div class="container" style="padding:30px 20px 0">
    <div class="section-head">
        <div>
            <h1 style="font-size:24px">Pemesanan saya</h1>
            <p class="muted mb-0">Pantau status verifikasi dan unduh e-voucher Anda di sini.</p>
        </div>
    </div>

    <div class="chips mb-16">
        <a class="chip {{ request('status') ? '' : 'active' }}" href="{{ route('pemesanan.index') }}">Semua</a>
        @foreach(['menunggu_pembayaran' => 'Menunggu Bayar', 'menunggu_verifikasi' => 'Menunggu Verifikasi', 'perlu_perbaikan' => 'Perlu Perbaikan', 'terkonfirmasi' => 'Terkonfirmasi', 'kedaluwarsa' => 'Kedaluwarsa'] as $key => $label)
            <a class="chip {{ request('status') === $key ? 'active' : '' }}"
               href="{{ route('pemesanan.index', ['status' => $key]) }}">{{ $label }}</a>
        @endforeach
    </div>

    @if($daftar->isEmpty())
        <div class="card empty">
            <div class="big">🎫</div>
            <h3>Belum ada pemesanan</h3>
            <p class="mb-0">Mulai jelajahi paket wisata dan buat pemesanan pertama Anda.</p>
            <a href="{{ route('beranda') }}" class="btn btn-primary mt-16">Lihat paket wisata</a>
        </div>
    @else
        <div class="stack">
            @foreach($daftar as $p)
                <div class="card card-pad">
                    <div class="row between wrap" style="gap:16px;align-items:flex-start">
                        <div style="min-width:220px">
                            <div class="row" style="gap:10px">
                                <span class="badge badge-{{ $p->badgeStatus() }}">{{ $p->labelStatus() }}</span>
                                <span class="tiny muted">{{ $p->kode_pemesanan }}</span>
                            </div>
                            <h3 class="mt-8" style="font-size:16px">{{ $p->jadwal->paket->nama_paket }}</h3>
                            <div class="tiny muted">
                                {{ $p->jadwal->paket->destinasi->nama_destinasi }} ·
                                {{ $p->jadwal->tanggal_berangkat->translatedFormat('d M Y') }} ·
                                {{ $p->jumlah_peserta }} peserta
                            </div>
                        </div>

                        <div class="right" style="min-width:150px">
                            <div class="tiny muted">Total bayar</div>
                            <div style="font-weight:800;font-size:17px;color:var(--primary-dark)">
                                Rp {{ number_format($p->total_bayar, 0, ',', '.') }}
                            </div>
                        </div>

                        <div class="row" style="gap:8px">
                            <a href="{{ route('pemesanan.show', $p) }}" class="btn btn-ghost btn-sm">Detail</a>
                            @if($p->status_pemesanan === 'terkonfirmasi')
                                <a href="{{ route('pemesanan.voucher', $p) }}" class="btn btn-primary btn-sm">E-Voucher</a>
                            @elseif(in_array($p->status_pemesanan, ['menunggu_pembayaran', 'perlu_perbaikan']))
                                <a href="{{ route('pemesanan.show', $p) }}" class="btn btn-orange btn-sm">Bayar / Perbaiki</a>
                            @endif
                        </div>
                    </div>

                    @if($p->status_pemesanan === 'menunggu_pembayaran' && $p->sisaDetikPembayaran() > 0)
                        <div class="alert alert-info mt-16 mb-0">
                            ⏱ Selesaikan pembayaran dalam <strong>{{ gmdate('H:i:s', $p->sisaDetikPembayaran()) }}</strong> lagi, atau pesanan otomatis kedaluwarsa.
                        </div>
                    @endif

                    @if($p->status_pemesanan === 'perlu_perbaikan')
                        <div class="alert alert-error mt-16 mb-0">
                            ✎ {{ $p->catatan_status }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="pagination">
            @if($daftar->onFirstPage())
                <span class="disabled">‹</span>
            @else
                <a href="{{ $daftar->previousPageUrl() }}">‹</a>
            @endif
            @foreach($daftar->getUrlRange(1, $daftar->lastPage()) as $page => $url)
                <a href="{{ $url }}" class="{{ $page == $daftar->currentPage() ? 'active' : '' }}">{{ $page }}</a>
            @endforeach
            @if($daftar->hasMorePages())
                <a href="{{ $daftar->nextPageUrl() }}">›</a>
            @else
                <span class="disabled">›</span>
            @endif
        </div>
    @endif
</div>
@endsection
