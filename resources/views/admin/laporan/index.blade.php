@extends('layouts.admin')

@section('title', 'Laporan Bulanan')

@php
    $maks = max(1, collect($rekap['detail'])->max('pendapatan') ?? 1);
    $pengeluaran = (float) $rekap['pendapatan'] * 0.12;
    $laba = (float) $rekap['pendapatan'] - $pengeluaran;
@endphp

@section('content')
<div class="adm-head">
    <div>
        <div class="crumb">Admin Portal › Laporan › {{ $rekap['periode'] }}</div>
        <h1>Laporan Perjalanan &amp; Pendapatan Bulanan</h1>
        <p>Ringkasan performa perjalanan dan pendapatan periode {{ $rekap['periode'] }}.</p>
    </div>
    <div class="actions">
        <a href="{{ route('admin.laporan.pdf', ['bulan' => $bulan, 'tahun' => $tahun]) }}" class="btn-adm dark">@include('partials.icon', ['name' => 'download', 'size' => 13]) Unduh PDF</a>
        <a href="{{ route('admin.laporan.excel', ['bulan' => $bulan, 'tahun' => $tahun]) }}" class="btn-adm orange">@include('partials.icon', ['name' => 'download', 'size' => 13]) Ekspor Excel</a>
    </div>
</div>

<form method="GET" class="adm-card adm-filter" style="margin-bottom:16px">
    <select name="bulan">
        @foreach(range(1,12) as $b)
            <option value="{{ $b }}" @selected($bulan == $b)>{{ \Carbon\Carbon::create()->month($b)->translatedFormat('F') }}</option>
        @endforeach
    </select>
    <select name="tahun">
        @foreach($daftarTahun as $t)
            <option value="{{ $t }}" @selected($tahun == $t)>{{ $t }}</option>
        @endforeach
    </select>
    <button class="btn-adm dark sm" type="submit">Terapkan</button>
    <a href="{{ route('admin.laporan.index') }}" class="btn-adm ghost sm">Reset</a>
    <span class="spacer"></span>
    <span class="pill gray">Periode {{ $rekap['periode'] }}</span>
</form>

<div class="adm-kpi">
    <div class="kpi">
        <div class="lbl">Total Perjalanan</div>
        <div class="val">{{ $rekap['jumlah_perjalanan'] }}</div>
        <div class="sub">Jadwal keberangkatan</div>
    </div>
    <div class="kpi">
        <div class="lbl">Total Pendapatan</div>
        <div class="val" style="font-size:16px">Rp {{ number_format($rekap['pendapatan'], 0, ',', '.') }}</div>
        <div class="sub">{{ $rekap['pemesanan_terkonfirmasi'] }} pemesanan</div>
    </div>
    <div class="kpi">
        <div class="lbl">Total Peserta</div>
        <div class="val">{{ $rekap['jumlah_peserta'] }}</div>
        <div class="sub">Peserta terkonfirmasi</div>
    </div>
    <div class="kpi dark">
        <div class="lbl">Rata-rata / Perjalanan</div>
        <div class="val" style="font-size:16px">Rp {{ number_format($rekap['jumlah_perjalanan'] > 0 ? $rekap['pendapatan'] / $rekap['jumlah_perjalanan'] : 0, 0, ',', '.') }}</div>
        <div class="sub">Pendapatan per jadwal</div>
    </div>
</div>

<div class="adm-grid" style="margin-bottom:16px">
    <div class="adm-card">
        <div class="adm-card-head">
            <div><h3>Distribusi Pendapatan per Keberangkatan</h3><p>Periode {{ $rekap['periode'] }}</p></div>
            <div class="chart-legend"><span><i style="background:var(--adm-orange)"></i>Pendapatan</span></div>
        </div>
        <div class="adm-card-body">
            @forelse($rekap['detail'] as $d)
                <div style="margin-bottom:10px">
                    <div class="row between" style="font-size:10.5px;margin-bottom:3px">
                        <span class="sub">{{ $d['tanggal']->format('d M') }} · {{ Str::limit($d['paket'], 30) }}</span>
                        <strong>Rp {{ number_format($d['pendapatan'], 0, ',', '.') }}</strong>
                    </div>
                    <div class="bar"><i style="width:{{ max(2, (int) ($d['pendapatan'] / $maks * 100)) }}%;background:var(--adm-orange)"></i></div>
                </div>
            @empty
                <div class="empty" style="padding:24px">Data periode tidak ditemukan.</div>
            @endforelse
        </div>
    </div>

    <div class="adm-card">
        <div class="adm-card-head"><div><h3>Komposisi</h3><p>Pendapatan vs estimasi biaya</p></div></div>
        <div class="adm-card-body">
            @php($rasio = $rekap['pendapatan'] > 0 ? round($laba / $rekap['pendapatan'] * 100, 1) : 0)
            <div style="display:grid;place-items:center;margin:6px 0 14px">
                <div style="width:130px;height:130px;border-radius:50%;display:grid;place-items:center;background:conic-gradient(var(--adm-orange) 0 {{ $rasio }}%, #e5e7eb {{ $rasio }}% 100%)">
                    <div style="width:92px;height:92px;border-radius:50%;background:#fff;display:grid;place-items:center;text-align:center">
                        <div>
                            <div style="font-size:16px;font-weight:800">{{ $rasio }}%</div>
                            <div style="font-size:8.5px;color:var(--adm-muted)">Laba</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row between" style="font-size:11px;padding:4px 0"><span class="sub">Pendapatan</span><strong>Rp {{ number_format($rekap['pendapatan'], 0, ',', '.') }}</strong></div>
            <div class="row between" style="font-size:11px;padding:4px 0"><span class="sub">Estimasi biaya (12%)</span><strong>Rp {{ number_format($pengeluaran, 0, ',', '.') }}</strong></div>
            <div class="row between" style="border-top:1px solid var(--adm-line);margin-top:6px;padding-top:8px;font-size:11.5px"><span class="sub">Laba bersih</span><strong style="color:#15803d">Rp {{ number_format($laba, 0, ',', '.') }}</strong></div>
        </div>
    </div>
</div>

<div class="adm-card">
    <div class="adm-card-head">
        <div><h3>Rincian Perjalanan</h3><p>Periode {{ $rekap['periode'] }}</p></div>
        <span class="pill gray">{{ $rekap['detail']->count() }} baris</span>
    </div>
    <div style="overflow-x:auto">
        <table class="adm-table">
            <thead>
                <tr><th>Tanggal</th><th>Paket</th><th>Destinasi</th><th>Kuota</th><th>Peserta</th><th>Pendapatan</th><th>Status</th></tr>
            </thead>
            <tbody>
                @forelse($rekap['detail'] as $d)
                    <tr>
                        <td>{{ $d['tanggal']->format('d M Y') }}</td>
                        <td><div class="nm">{{ Str::limit($d['paket'], 28) }}</div></td>
                        <td class="sub">{{ $d['destinasi'] }}</td>
                        <td>{{ $d['kuota'] }}</td>
                        <td>{{ $d['peserta'] }}</td>
                        <td><strong>Rp {{ number_format($d['pendapatan'], 0, ',', '.') }}</strong></td>
                        <td>
                            @php($konfirm = $d['status_pemesanan']['terkonfirmasi'] ?? 0)
                            <span class="pill {{ $konfirm > 0 ? 'ok' : 'gray' }}">{{ $konfirm > 0 ? 'Terjual' : 'Belum terjual' }}</span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty" style="padding:28px">Data periode tidak ditemukan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="adm-foot">
        <span>Total {{ $rekap['detail']->count() }} perjalanan pada {{ $rekap['periode'] }}</span>
        <strong>Rp {{ number_format($rekap['pendapatan'], 0, ',', '.') }}</strong>
    </div>
</div>
@endsection
