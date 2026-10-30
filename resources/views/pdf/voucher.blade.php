<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Voucher {{ $pemesanan->kode_pemesanan }}</title>
    <style>
        @page { margin: 28px 30px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #101828; }
        .head { background: #0f2c4a; color: #fff; padding: 18px 20px; border-radius: 10px; }
        .head h1 { margin: 0; font-size: 18px; }
        .head .sub { font-size: 10px; color: #c7d6e6; margin-top: 3px; }
        .kode { float: right; text-align: right; }
        .kode .lbl { font-size: 8px; color: #9fb4cb; letter-spacing: 1px; }
        .kode .val { font-size: 14px; font-weight: bold; letter-spacing: .5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        td { padding: 5px 0; vertical-align: top; }
        .lbl { color: #667085; width: 34%; }
        .box { border: 1px solid #e4e7ec; border-radius: 8px; padding: 12px 14px; margin-top: 14px; }
        .box h3 { margin: 0 0 6px; font-size: 11px; color: #1b6ff3; }
        .qr { float: right; text-align: center; }
        .qr img { width: 130px; height: 130px; }
        .qr .cap { font-size: 7px; color: #667085; word-break: break-all; width: 130px; }
        .note { margin-top: 16px; font-size: 9px; color: #667085; border-top: 1px dashed #e4e7ec; padding-top: 8px; }
        .badge { display: inline-block; background: #e8f8ee; color: #15803d; padding: 2px 8px; border-radius: 8px; font-size: 9px; font-weight: bold; }
        ol { margin: 4px 0 0 16px; padding: 0; }
        li { margin-bottom: 2px; }
    </style>
</head>
<body>
    <div class="head">
        <div class="kode">
            <div class="lbl">KODE PEMESANAN</div>
            <div class="val">{{ $pemesanan->kode_pemesanan }}</div>
        </div>
        <h1>E-VOUCHER PERJALANAN</h1>
        <div class="sub">Nanta Travel — Sistem Informasi Travel &amp; Agen Wisata</div>
    </div>

    <table>
        <tr>
            <td class="lbl">Nama pemesan</td>
            <td><strong>{{ $pemesanan->user->nama_lengkap }}</strong></td>
        </tr>
        <tr>
            <td class="lbl">Paket wisata</td>
            <td><strong>{{ $pemesanan->jadwal->paket->nama_paket }}</strong></td>
        </tr>
        <tr>
            <td class="lbl">Destinasi</td>
            <td>{{ $pemesanan->jadwal->paket->destinasi->nama_destinasi }} — {{ $pemesanan->jadwal->paket->destinasi->lokasi }}</td>
        </tr>
        <tr>
            <td class="lbl">Tanggal berangkat</td>
            <td>{{ $pemesanan->jadwal->tanggal_berangkat->translatedFormat('l, d F Y') }}</td>
        </tr>
        <tr>
            <td class="lbl">Titik kumpul</td>
            <td colspan="3">{{ $pemesanan->jadwal->kota_kumpul ?? '-' }}{{ $pemesanan->jadwal->titik_kumpul ? ' — '.$pemesanan->jadwal->titik_kumpul : '' }}{{ $pemesanan->jadwal->jam_kumpul ? ' ('.$pemesanan->jadwal->jam_kumpul->format('H:i').')' : '' }}</td>
        </tr>
        <tr>
            <td class="lbl">Tanggal kembali</td>
            <td>{{ $pemesanan->jadwal->tanggal_kembali?->translatedFormat('l, d F Y') ?? '-' }}</td>
        </tr>
        <tr>
            <td class="lbl">Jumlah peserta</td>
            <td>{{ $pemesanan->jumlah_peserta }} orang</td>
        </tr>
        @if($pemesanan->diskon > 0)
        <tr>
            <td class="lbl">Diskon promo</td>
            <td>-Rp {{ number_format($pemesanan->diskon, 0, ',', '.') }} ({{ $pemesanan->kode_promo }})</td>
        </tr>
        @endif
        <tr>
            <td class="lbl">Total dibayar</td>
            <td>Rp {{ number_format($pemesanan->total_bayar, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="lbl">Status</td>
            <td><span class="badge">TERKONFIRMASI</span></td>
        </tr>
    </table>

    <div class="box">
        <div class="qr">
            <img src="{{ $qr }}" alt="QR">
            <div class="cap">{{ $tiket->kode_qr }}</div>
        </div>
        <h3>DAFTAR PESERTA</h3>
        <ol>
            @foreach($pemesanan->peserta as $ps)
                <li>{{ $ps->nama_peserta }} — {{ $ps->jenis_identitas }} {{ $ps->no_identitas }}</li>
            @endforeach
        </ol>
    </div>

    <div class="note">
        Voucher ini sah apabila kode pemesanan terdaftar dan berstatus TERKONFIRMASI pada sistem.
        Tunjukkan voucher beserta identitas asli saat keberangkatan. Diterbitkan otomatis pada
        {{ $tiket->tanggal_terbit->translatedFormat('d F Y H:i') }} WIB.
    </div>
</body>
</html>
