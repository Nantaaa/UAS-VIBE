<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Manifes Keberangkatan</title>
    <style>
        @page { margin: 26px 28px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10.5px; color: #101828; }
        h1 { font-size: 16px; margin: 0; }
        .sub { color: #667085; font-size: 10px; margin-top: 3px; }
        .meta { margin-top: 12px; }
        .meta td { padding: 3px 0; }
        .meta .lbl { color: #667085; width: 130px; }
        table.data { width: 100%; border-collapse: collapse; margin-top: 14px; }
        table.data th { background: #0f2c4a; color: #fff; text-align: left; padding: 6px 8px; font-size: 9px; }
        table.data td { padding: 5px 8px; border-bottom: 1px solid #e9edf3; }
        .foot { margin-top: 14px; font-size: 9px; color: #667085; border-top: 1px dashed #e4e7ec; padding-top: 8px; }
    </style>
</head>
<body>
    <h1>MANIFES PESERTA KEBERANGKATAN</h1>
    <div class="sub">JavaTrip Travel — Sistem Informasi Travel &amp; Agen Wisata</div>

    <table class="meta">
        <tr><td class="lbl">Paket wisata</td><td><strong>{{ $jadwal->paket->nama_paket }}</strong></td></tr>
        <tr><td class="lbl">Destinasi</td><td>{{ $jadwal->paket->destinasi->nama_destinasi }} — {{ $jadwal->paket->destinasi->lokasi }}</td></tr>
        <tr><td class="lbl">Tanggal berangkat</td><td>{{ $jadwal->tanggal_berangkat->translatedFormat('l, d F Y') }}</td></tr>
        <tr><td class="lbl">Tanggal kembali</td><td>{{ $jadwal->tanggal_kembali?->translatedFormat('l, d F Y') ?? '-' }}</td></tr>
        <tr><td class="lbl">Kuota / peserta</td><td>{{ $jadwal->kuota_peserta }} / {{ $totalPeserta }}</td></tr>
        <tr>
            <td class="lbl">Petugas</td>
            <td>{{ $jadwal->penugasan->map(fn($p) => $p->petugas->nama_petugas.' ('.$p->petugas->peran.')')->join(', ') ?: '-' }}</td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th style="width:32px">No</th>
                <th>Nama peserta</th>
                <th style="width:70px">Identitas</th>
                <th style="width:130px">Nomor</th>
                <th>Pemesan</th>
                <th style="width:120px">Kode pemesanan</th>
            </tr>
        </thead>
        <tbody>
            @php($no = 1)
            @forelse($pemesanan as $p)
                @foreach($p->peserta as $ps)
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td>{{ $ps->nama_peserta }}</td>
                        <td>{{ $ps->jenis_identitas }}</td>
                        <td>{{ $ps->no_identitas }}</td>
                        <td>{{ $p->user->nama_lengkap }}</td>
                        <td>{{ $p->kode_pemesanan }}</td>
                    </tr>
                @endforeach
            @empty
                <tr><td colspan="6" style="text-align:center;padding:16px;color:#667085">Belum ada peserta terkonfirmasi.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="foot">
        Dicetak {{ now()->translatedFormat('d F Y H:i') }} WIB · Total {{ $totalPeserta }} peserta dari
        {{ $pemesanan->count() }} pemesanan terkonfirmasi.
    </div>
</body>
</html>
