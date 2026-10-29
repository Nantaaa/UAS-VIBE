@extends('layouts.admin')

@section('title', 'Verifikasi Pemesanan')

@php
    $jumlahMenunggu = \App\Models\Pemesanan::where('status_pemesanan','menunggu_verifikasi')->count();
    $jumlahPerbaikan = \App\Models\Pemesanan::where('status_pemesanan','perlu_perbaikan')->count();
    $pendapatanBulan = \App\Models\Pemesanan::where('status_pemesanan','terkonfirmasi')
        ->whereMonth('tanggal_pemesanan', now()->month)->whereYear('tanggal_pemesanan', now()->year)->sum('total_bayar');
@endphp

@section('content')
<div class="adm-head">
    <div>
        <div class="crumb">Admin Portal › Operasional › Verifikasi Pemesanan</div>
        <h1>Verifikasi Pembayaran &amp; Reservasi</h1>
        <p>Periksa bukti pembayaran dan dokumen identitas peserta sebelum menerbitkan e-ticket.</p>
    </div>
    <div class="actions">
        <a href="{{ route('admin.laporan.excel', ['bulan' => now()->month, 'tahun' => now()->year]) }}" class="btn-adm ghost">@include('partials.icon', ['name' => 'download', 'size' => 13]) Ekspor Laporan</a>
    </div>
</div>

<div class="adm-kpi">
    <div class="kpi">
        <div class="lbl">Antrean Verifikasi</div>
        <div class="val">{{ $jumlahMenunggu }}</div>
        <div class="sub">Perlu tindakan</div>
    </div>
    <div class="kpi">
        <div class="lbl">Perlu Perbaikan</div>
        <div class="val">{{ $jumlahPerbaikan }}</div>
        <div class="sub">Dokumen dikembalikan</div>
    </div>
    <div class="kpi">
        <div class="lbl">Total Antrean</div>
        <div class="val">{{ $jumlahMenunggu + $jumlahPerbaikan }}</div>
        <div class="sub">Menunggu diproses</div>
    </div>
    <div class="kpi dark">
        <div class="lbl">Pendapatan Bulan Ini</div>
        <div class="val">Rp {{ number_format($pendapatanBulan, 0, ',', '.') }}</div>
        <div class="sub">Pemesanan terkonfirmasi</div>
    </div>
</div>

<div class="adm-card">
    <div class="adm-card-head">
        <div class="tabs" style="background:#f1f3f7">
            <a href="{{ route('admin.verifikasi.index') }}" class="{{ $status === 'menunggu_verifikasi' ? 'active' : '' }}">Menunggu Verifikasi ({{ $jumlahMenunggu }})</a>
            <a href="{{ route('admin.verifikasi.index', ['status' => 'perlu_perbaikan']) }}" class="{{ $status === 'perlu_perbaikan' ? 'active' : '' }}">Perlu Perbaikan ({{ $jumlahPerbaikan }})</a>
        </div>
        <form method="GET" class="row" style="gap:6px">
            <input type="hidden" name="status" value="{{ $status }}">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari kode / nama..." style="font-size:11px;padding:7px 10px;border:1px solid var(--adm-line);border-radius:7px">
            <button class="btn-adm ghost sm" type="submit">Cari</button>
        </form>
    </div>

    <div style="overflow-x:auto">
        <table class="adm-table">
            <thead>
                <tr><th>Kode Booking</th><th>Peserta</th><th>Paket Wisata</th><th>Tgl Berangkat</th><th>Total</th><th>Diunggah</th><th>Status</th><th></th></tr>
            </thead>
            <tbody>
                @forelse($daftar as $p)
                    <tr>
                        <td><strong style="font-family:ui-monospace,monospace;font-size:11px">{{ $p->kode_pemesanan }}</strong></td>
                        <td>
                            <div class="nm">{{ $p->user->nama_lengkap }}</div>
                            <div class="sub">{{ $p->jumlah_peserta }} pax · {{ $p->user->no_hp }}</div>
                        </td>
                        <td>
                            <div class="nm">{{ Str::limit($p->jadwal->paket->nama_paket, 26) }}</div>
                            <div class="sub">{{ $p->jadwal->paket->destinasi->nama_destinasi }}</div>
                        </td>
                        <td>{{ $p->jadwal->tanggal_berangkat->translatedFormat('d M Y') }}</td>
                        <td><strong>Rp {{ number_format($p->total_bayar, 0, ',', '.') }}</strong></td>
                        <td class="sub">{{ $p->pembayaranTerakhir?->tanggal_upload?->translatedFormat('d M H:i') ?? '—' }}</td>
                        <td><span class="pill {{ $p->status_pemesanan === 'perlu_perbaikan' ? 'err' : 'warn' }}">{{ $p->labelStatus() }}</span></td>
                        <td><a href="{{ route('admin.verifikasi.show', $p) }}" class="btn-adm dark sm">Periksa</a></td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="empty" style="padding:28px">Tidak ada antrean pada tab ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="adm-foot">
        <span>Menampilkan {{ $daftar->firstItem() ?? 0 }} - {{ $daftar->lastItem() ?? 0 }} dari {{ $daftar->total() }} antrean</span>
        <div class="pagination" style="margin:0">
            @foreach($daftar->getUrlRange(1, max(1, $daftar->lastPage())) as $page => $url)
                <a href="{{ $url }}" class="{{ $page == $daftar->currentPage() ? 'active' : '' }}">{{ $page }}</a>
            @endforeach
        </div>
    </div>
</div>
@endsection
