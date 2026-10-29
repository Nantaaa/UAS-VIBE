@extends('layouts.app')

@section('title', 'E-Voucher')

@section('content')
<div class="container" style="padding:28px 20px 0;max-width:820px">
    <div class="row between wrap mb-16">
        <div>
            <h1 style="font-size:24px;margin-bottom:2px">E-Voucher perjalanan</h1>
            <div class="tiny muted">Tunjukkan voucher ini beserta identitas asli saat keberangkatan.</div>
        </div>
        <div class="row" style="gap:8px">
            <a href="{{ route('pemesanan.voucher.unduh', $pemesanan) }}" class="btn btn-primary btn-sm">Unduh PDF</a>
            <a href="{{ route('pemesanan.show', $pemesanan) }}" class="btn btn-ghost btn-sm">Kembali</a>
        </div>
    </div>

    <div class="card" style="overflow:hidden">
        <div style="background:linear-gradient(115deg,#0b2440,#14406b 60%,#1b6ff3);color:#fff;padding:26px 28px">
            <div class="row between wrap">
                <div>
                    <div class="tiny" style="opacity:.7;letter-spacing:.14em;text-transform:uppercase">E-Voucher Resmi</div>
                    <h2 style="margin:6px 0 0;font-size:22px">{{ $pemesanan->jadwal->paket->nama_paket }}</h2>
                    <div class="tiny" style="opacity:.8">{{ $pemesanan->jadwal->paket->destinasi->nama_destinasi }} · {{ $pemesanan->jadwal->paket->destinasi->lokasi }}</div>
                </div>
                <div class="right">
                    <div class="tiny" style="opacity:.7">Kode pemesanan</div>
                    <div style="font-family:ui-monospace,monospace;font-size:17px;font-weight:800;letter-spacing:.04em">{{ $pemesanan->kode_pemesanan }}</div>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="grid" style="grid-template-columns:1fr auto;gap:24px;align-items:start">
                <div>
                    <dl class="kv">
                        <dt>Nama pemesan</dt><dd>{{ $pemesanan->user->nama_lengkap }}</dd>
                        <dt>Jumlah peserta</dt><dd>{{ $pemesanan->jumlah_peserta }} orang</dd>
                        <dt>Tanggal berangkat</dt><dd>{{ $pemesanan->jadwal->tanggal_berangkat->translatedFormat('l, d F Y') }}</dd>
                        <dt>Tanggal kembali</dt><dd>{{ $pemesanan->jadwal->tanggal_kembali?->translatedFormat('l, d F Y') ?? '-' }}</dd>
                        <dt>Total dibayar</dt><dd>Rp {{ number_format($pemesanan->total_bayar, 0, ',', '.') }}</dd>
                        <dt>Status</dt><dd><span class="badge badge-success">Terkonfirmasi</span></dd>
                    </dl>

                    <hr style="border:0;border-top:1px dashed var(--line);margin:18px 0">

                    <div class="tiny muted mb-8">Daftar peserta</div>
                    <ol style="margin:0;padding-left:18px;font-size:13.5px">
                        @foreach($pemesanan->peserta as $ps)
                            <li>{{ $ps->nama_peserta }} — {{ $ps->jenis_identitas }} {{ $ps->no_identitas }}</li>
                        @endforeach
                    </ol>
                </div>

                <div class="center">
                    <div style="padding:12px;border:1px solid var(--line);border-radius:14px;background:#fff">
                        <img src="data:image/svg+xml;base64,{{ base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(180)->margin(1)->generate($pemesanan->tiket->kode_qr ?? $pemesanan->kode_pemesanan)) }}"
                             alt="QR Code Voucher" width="180" height="180">
                    </div>
                    <div class="tiny muted mt-8" style="max-width:180px;word-break:break-all">
                        {{ $pemesanan->tiket->kode_qr ?? 'QR belum terbit' }}
                    </div>
                </div>
            </div>

            <div class="alert alert-info mt-24 mb-0">
                ℹ Voucher ini sah bila kode pemesanan terdaftar dan berstatus <strong>Terkonfirmasi</strong> pada sistem.
                Admin dapat memindai QR Code di menu Validasi Voucher.
            </div>
        </div>
    </div>
</div>
@endsection
