<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Perjalanan {{ $rekap['periode'] }}</title>
    <style>
        @page { margin: 26px 28px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10.5px; color: #101828; }
        h1 { font-size: 16px; margin: 0; }
        .sub { color: #667085; font-size: 10px; margin-top: 3px; }
        .cards { margin-top: 14px; width: 100%; border-collapse: separate; border-spacing: 8px 0; }
        .cards td { background: #f5f7fb; border-radius: 8px; padding: 10px 12px; width: 25%; }
        .cards .val { font-size: 14px; font-weight: bold; color: #0f2c4a; }
        .cards .lbl { font-size: 8.5px; color: #667085; }
        table.data { width: 100%; border-collapse: collapse; margin-top: 14px; }
        table.data th { background: #0f2c4a; color: #fff; text-align: left; padding: 6px 8px; font-size: 9px; }
        table.data td { padding: 5px 8px; border-bottom: 1px solid #e9edf3; }
        .foot { margin-top: 14px; font-size: 9px; color: #667085; border-top: 1px dashed #e4e7ec; padding-top: 8px; }
    </style>
</head>
<body>
    <h1>LAPORAN PERJALANAN BULANAN</h1>
    <div class="sub">Periode {{ $rekap['periode'] }} · JavaTrip Travel</div>

    <table class="cards">
        <tr>
            <td><div class="val">{{ $rekap['jumlah_perjalanan'] }}</div><div class="lbl">Jumlah perjalanan</div></td>
            <td><div class="val">{{ $rekap['jumlah_peserta'] }}</div><div class="lbl">Peserta terkonfirmasi</div></td>
            <td><div class="val">Rp {{ number_format($rekap['pendapatan'], 0, ',', '.') }}</div><div class="lbl">Pendapatan</div></td>
            <td><div class="val">{{ $rekap['pemesanan_terkonfirmasi'] }}/{{ $rekap['jumlah_pemesanan'] }}</div><div class="lbl">Pemesanan terkonfirmasi</div></td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th style="width:70px">Tanggal</th>
                <th>Paket</th>
                <th>Destinasi</th>
                <th style="width:48px">Kuota</th>
                <th style="width:55px">Peserta</th>
                <th style="width:110px">Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rekap['detail'] as $d)
                <tr>
                    <td>{{ $d['tanggal']->format('d/m/Y') }}</td>
                    <td>{{ $d['paket'] }}</td>
                    <td>{{ $d['destinasi'] }}</td>
                    <td>{{ $d['kuota'] }}</td>
                    <td>{{ $d['peserta'] }}</td>
                    <td>Rp {{ number_format($d['pendapatan'], 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="6" style="text-align:center;padding:16px;color:#667085">Data periode tidak ditemukan.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="foot">
        Dibuat otomatis oleh sistem pada {{ now()->translatedFormat('d F Y H:i') }} WIB.
        Angka dihitung dari pemesanan berstatus TERKONFIRMASI.
    </div>
</body>
</html>
