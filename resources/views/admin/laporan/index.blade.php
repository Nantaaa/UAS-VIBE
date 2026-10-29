@extends('layouts.admin')

@section('title', 'Laporan Perjalanan')
@section('heading', 'Laporan Perjalanan Bulanan')

@section('content')
<form method="GET" class="card card-pad mb-16 row wrap" style="gap:12px;align-items:flex-end">
    <div class="field" style="min-width:180px">
        <label for="bulan">Bulan</label>
        <select id="bulan" name="bulan">
            @foreach(range(1, 12) as $b)
                <option value="{{ $b }}" @selected($bulan == $b)>
                    {{ \Carbon\Carbon::create()->month($b)->translatedFormat('F') }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="field" style="min-width:130px">
        <label for="tahun">Tahun</label>
        <select id="tahun" name="tahun">
            @foreach($daftarTahun as $t)
                <option value="{{ $t }}" @selected($tahun == $t)>{{ $t }}</option>
            @endforeach
        </select>
    </div>
    <button class="btn btn-navy" type="submit">Tampilkan</button>
    <div style="margin-left:auto" class="row" style="gap:8px">
        <a href="{{ route('admin.laporan.pdf', ['bulan' => $bulan, 'tahun' => $tahun]) }}" class="btn btn-ghost btn-sm">Unduh PDF</a>
        <a href="{{ route('admin.laporan.excel', ['bulan' => $bulan, 'tahun' => $tahun]) }}" class="btn btn-primary btn-sm">Unduh Excel</a>
    </div>
</form>

<div class="grid grid-4 mb-16">
    <div class="card stat">
        <div class="ico">🗓</div>
        <div><div class="val">{{ $rekap['jumlah_perjalanan'] }}</div><div class="lbl">Jumlah perjalanan</div></div>
    </div>
    <div class="card stat">
        <div class="ico green">👥</div>
        <div><div class="val">{{ $rekap['jumlah_peserta'] }}</div><div class="lbl">Peserta terkonfirmasi</div></div>
    </div>
    <div class="card stat">
        <div class="ico orange">Rp</div>
        <div>
            <div class="val" style="font-size:17px">Rp {{ number_format($rekap['pendapatan'], 0, ',', '.') }}</div>
            <div class="lbl">Pendapatan</div>
        </div>
    </div>
    <div class="card stat">
        <div class="ico">▤</div>
        <div><div class="val">{{ $rekap['pemesanan_terkonfirmasi'] }}/{{ $rekap['jumlah_pemesanan'] }}</div><div class="lbl">Pemesanan terkonfirmasi</div></div>
    </div>
</div>

<div class="card">
    <div class="card-head">
        <h3>Rekap {{ $rekap['periode'] }}</h3>
        <span class="badge badge-info">Otomatis dari basis data</span>
    </div>
    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr><th>Tanggal</th><th>Paket</th><th>Destinasi</th><th>Kuota</th><th>Peserta</th><th>Pendapatan</th><th>Status pemesanan</th></tr>
            </thead>
            <tbody>
                @forelse($rekap['detail'] as $d)
                    <tr>
                        <td>{{ $d['tanggal']->translatedFormat('d M Y') }}</td>
                        <td><strong>{{ $d['paket'] }}</strong></td>
                        <td>{{ $d['destinasi'] }}</td>
                        <td>{{ $d['kuota'] }}</td>
                        <td>{{ $d['peserta'] }}</td>
                        <td>Rp {{ number_format($d['pendapatan'], 0, ',', '.') }}</td>
                        <td class="tiny muted">
                            @foreach($d['status_pemesanan'] as $st => $n)
                                {{ \App\Models\Pemesanan::STATUS_LABEL[$st] ?? $st }}: {{ $n }}@if(!$loop->last), @endif
                            @endforeach
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty">Data periode tidak ditemukan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
