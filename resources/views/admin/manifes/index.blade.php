@extends('layouts.admin')

@section('title', 'Manifes Peserta')

@section('content')
<div class="adm-head">
    <div>
        <div class="crumb">Admin Portal › Operasional › Manifes Peserta</div>
        <h1>Manifes Peserta Pelayaran</h1>
        <p>Daftar peserta per keberangkatan, disusun otomatis dari pemesanan berstatus terkonfirmasi.</p>
    </div>
    <div class="actions">
        <a href="{{ route('admin.manifes.index') }}" class="btn-adm ghost">@include('partials.icon', ['name' => 'refresh', 'size' => 13]) Sinkronisasi Data</a>
    </div>
</div>

<div class="adm-kpi">
    <div class="kpi">
        <div class="lbl">Total Keberangkatan</div>
        <div class="val">{{ $jadwal->total() }}</div>
        <div class="sub">Jadwal terdaftar</div>
    </div>
    <div class="kpi">
        <div class="lbl">Peserta Terkonfirmasi</div>
        <div class="val">{{ $jadwal->getCollection()->sum('peserta_terkonfirmasi') }}</div>
        <div class="sub">Pada halaman ini</div>
    </div>
    <div class="kpi">
        <div class="lbl">Siap Berlayar</div>
        <div class="val">{{ $jadwal->getCollection()->where('peserta_terkonfirmasi', '>', 0)->count() }}</div>
        <div class="sub">Jadwal dengan peserta</div>
    </div>
    <div class="kpi dark">
        <div class="lbl">Total Kuota</div>
        <div class="val">{{ $jadwal->getCollection()->sum('kuota_peserta') }}</div>
        <div class="sub">Kursi tersedia</div>
    </div>
</div>

<div class="adm-card">
    <div class="adm-card-head">
        <div><h3>Daftar Manifes Keberangkatan</h3><p>Pilih jadwal untuk melihat dan mengunduh manifes resmi.</p></div>
    </div>
    <div style="overflow-x:auto">
        <table class="adm-table">
            <thead>
                <tr><th>Armada &amp; Paket</th><th>Berangkat</th><th>Kuota</th><th>Peserta</th><th>Petugas</th><th style="width:160px">Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($jadwal as $j)
                    <tr>
                        <td>
                            <div class="nm">{{ Str::limit($j->paket->nama_paket, 32) }}</div>
                            <div class="sub">{{ $j->paket->destinasi->nama_destinasi }} · {{ $j->paket->tipe_trip }}</div>
                        </td>
                        <td>
                            <div class="nm">{{ $j->tanggal_berangkat->translatedFormat('d M Y') }}</div>
                            <div class="sub">{{ $j->paket->durasi_hari }} Hari {{ $j->paket->durasi_hari - 1 }} Malam</div>
                        </td>
                        <td>{{ $j->kuota_peserta }}</td>
                        <td><span class="pill {{ $j->peserta_terkonfirmasi > 0 ? 'ok' : 'gray' }}">{{ $j->peserta_terkonfirmasi }} peserta</span></td>
                        <td class="sub">{{ $j->penugasan->map(fn($p) => $p->petugas->nama_petugas)->join(', ') ?: '—' }}</td>
                        <td>
                            <div class="row" style="gap:6px">
                                <a href="{{ route('admin.manifes.show', $j) }}" class="btn-adm ghost sm">Lihat</a>
                                <a href="{{ route('admin.manifes.pdf', $j) }}" class="btn-adm orange sm">PDF</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty" style="padding:28px">Belum ada jadwal keberangkatan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="adm-foot">
        <span>Menampilkan {{ $jadwal->firstItem() ?? 0 }} - {{ $jadwal->lastItem() ?? 0 }} dari {{ $jadwal->total() }} keberangkatan</span>
        <div class="pagination" style="margin:0">
            @foreach($jadwal->getUrlRange(1, max(1, $jadwal->lastPage())) as $page => $url)
                <a href="{{ $url }}" class="{{ $page == $jadwal->currentPage() ? 'active' : '' }}">{{ $page }}</a>
            @endforeach
        </div>
    </div>
</div>
@endsection
