@extends('layouts.app')

@section('title', 'E-Ticket')

@php
    $kode = $pemesanan->tiket->kode_qr ?? $pemesanan->kode_pemesanan;
    // barcode dekoratif deterministik dari kode
    $bars = [];
    foreach (str_split($kode) as $ch) {
        $v = ord($ch);
        $bars[] = ($v % 3) + 1;
        $bars[] = (($v >> 2) % 2) + 1;
    }
    $x = 0;
    $rects = [];
    foreach ($bars as $i => $w) {
        if ($i % 2 === 0) {
            $rects[] = [$x, $w * 2];
        }
        $x += $w * 2;
    }
    $barcodeW = $x;
@endphp

@section('content')
<div class="container" style="padding-top:24px;max-width:1080px">
    <div class="row between wrap" style="gap:14px;align-items:flex-start">
        <div>
            <h1 style="font-size:23px;margin:0 0 4px">E-Tiket &amp; Dokumen Perjalanan Resmi</h1>
            <p class="muted mb-0" style="font-size:12.5px">Tunjukkan e-tiket ini kepada petugas saat keberangkatan.</p>
        </div>
        <div class="row" style="gap:8px">
            <button type="button" class="btn btn-ghost btn-sm" onclick="window.print()">🖨 Cetak Dokumen</button>
            <a href="{{ route('pemesanan.voucher.unduh', $pemesanan) }}" class="btn btn-primary btn-sm">⬇ Unduh PDF</a>
        </div>
    </div>

    <div class="ticket mt-16">
        <div class="ticket-main">
            <div class="row between wrap" style="gap:10px">
                <div class="row" style="gap:9px">
                    <span class="brand-mark" style="width:30px;height:30px;font-size:13px">N</span>
                    <div>
                        <div style="font-size:12.5px;font-weight:800">Nanta Travel</div>
                        <div class="tiny muted">Official Concierge Partner</div>
                    </div>
                </div>
                <div class="row" style="gap:6px">
                    <span class="badge badge-success">e-Ticket</span>
                    <span class="badge badge-warning">Belum Digunakan</span>
                </div>
            </div>

            <div class="row between mt-16" style="gap:10px;align-items:flex-start">
                <div>
                    <div class="tiny muted">Kode pemesanan</div>
                    <div class="row" style="gap:7px">
                        <strong style="font-family:ui-monospace,monospace;font-size:14px">{{ $pemesanan->kode_pemesanan }}</strong>
                        <button type="button" class="btn btn-ghost btn-sm" style="padding:2px 7px;font-size:10px" onclick="salin('{{ $pemesanan->kode_pemesanan }}')">Salin</button>
                    </div>
                </div>
            </div>

            <h2 style="font-size:20px;margin:14px 0 6px">{{ $pemesanan->jadwal->paket->nama_paket }}</h2>
            <div class="row" style="gap:8px;flex-wrap:wrap">
                <span class="pdp-badge">{{ $pemesanan->jumlah_peserta }} Peserta</span>
                <span class="pdp-badge">{{ $pemesanan->jadwal->paket->durasi_hari }} Hari {{ $pemesanan->jadwal->paket->durasi_hari - 1 }} Malam</span>
                <span class="pdp-badge orange">{{ $pemesanan->jadwal->paket->tipe_trip }}</span>
            </div>

            <div class="row mt-16" style="gap:10px;align-items:stretch">
                <div class="ticket-date">
                    <div class="tiny muted">Keberangkatan</div>
                    <div class="d">{{ $pemesanan->jadwal->tanggal_berangkat->translatedFormat('l, d F Y') }}</div>
                    <div class="tiny muted">07:00 WITA</div>
                </div>
                <div class="ticket-date">
                    <div class="tiny muted">Kepulangan</div>
                    <div class="d">{{ $pemesanan->jadwal->tanggal_kembali?->translatedFormat('l, d F Y') ?? '-' }}</div>
                    <div class="tiny muted">16:00 WITA</div>
                </div>
            </div>

            <div class="mt-16">
                <div class="tiny" style="font-weight:800;margin-bottom:8px">Peserta</div>
                @foreach($pemesanan->peserta as $ps)
                    <div class="ticket-person">
                        <span class="ava">{{ strtoupper(substr($ps->nama_peserta, 0, 1)) }}</span>
                        <div>
                            <div style="font-size:12.5px;font-weight:700">{{ $ps->nama_peserta }}</div>
                            <div class="tiny muted">{{ $ps->jenis_identitas }} · {{ $ps->no_identitas }}</div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="ticket-info-box mt-16">
                <div class="tiny" style="font-weight:800;margin-bottom:6px">Data pemesan</div>
                <div class="kv" style="font-size:11.5px">
                    <dt>Nama</dt><dd>{{ $pemesanan->user->nama_lengkap }}</dd>
                    <dt>Email</dt><dd>{{ $pemesanan->user->email }}</dd>
                    <dt>Telepon</dt><dd>{{ $pemesanan->user->no_hp }}</dd>
                    <dt>Total dibayar</dt><dd>Rp {{ number_format($pemesanan->total_bayar, 0, ',', '.') }}</dd>
                </div>
            </div>
        </div>

        <div class="ticket-qr">
            <div class="tiny" style="font-weight:800;letter-spacing:.1em;text-transform:uppercase;color:#1d4ed8">Scan QR</div>
            <div class="qr-frame">
                <img src="data:image/svg+xml;base64,{{ base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(200)->margin(1)->generate($kode)) }}"
                     alt="QR Code" width="200" height="200">
            </div>

            <svg viewBox="0 0 {{ $barcodeW }} 44" width="100%" height="44" style="margin-top:10px">
                @foreach($rects as [$rx, $rw])
                    <rect x="{{ $rx }}" y="0" width="{{ $rw }}" height="44" fill="#0f172a"></rect>
                @endforeach
            </svg>
            <div class="tiny muted" style="font-family:ui-monospace,monospace;letter-spacing:.18em;margin-top:4px">{{ $pemesanan->kode_pemesanan }}</div>

            <div class="tiny muted" style="margin-top:10px;line-height:1.6">
                Tunjukkan QR Code ini kepada petugas saat check-in keberangkatan.
            </div>
            <a href="{{ route('pemesanan.voucher.unduh', $pemesanan) }}" class="btn btn-teal btn-block btn-sm mt-16">Simpan E-Ticket</a>
        </div>
    </div>

    {{-- Info strip --}}
    <div class="ticket-notes">
        <div>
            <strong>1. Dokumen Wajib Dibawa</strong>
            <span>KTP/Paspor asli dan e-tiket ini wajib ditunjukkan saat keberangkatan.</span>
        </div>
        <div>
            <strong>2. Ketentuan Barang</strong>
            <span>Maksimal 1 koper 20 kg per peserta. Barang berharga dibawa sendiri.</span>
        </div>
        <div>
            <strong>3. Fasilitas Tambahan</strong>
            <span>Permintaan khusus (alergi makanan, kamar tambahan) hubungi concierge.</span>
        </div>
    </div>

    <div class="cta-banner mt-16">
        <div>
            <span class="hero-badge">BUTUH BANTUAN?</span>
            <h2>Ada pertanyaan tentang perjalanan ini?</h2>
            <p>Tim concierge kami siap membantu 24 jam untuk perubahan jadwal maupun kebutuhan khusus.</p>
        </div>
        <a href="https://wa.me/6281234567890" class="btn btn-primary">Hubungi Kami</a>
    </div>
</div>

<script>
function salin(t) {
    navigator.clipboard?.writeText(t);
    alert('Kode disalin: ' + t);
}
</script>
@endsection
