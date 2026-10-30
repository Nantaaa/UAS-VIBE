@extends('layouts.admin')

@section('title', 'Manifes Keberangkatan')

@php
    $terisi = $jadwal->kuota_peserta - $jadwal->sisa_kuota;
    $persen = $jadwal->kuota_peserta > 0 ? (int) round($terisi / $jadwal->kuota_peserta * 100) : 0;
@endphp

@section('content')
<div class="adm-head">
    <div>
        <div class="crumb">Admin Portal › Manifes › {{ $jadwal->tanggal_berangkat->format('d M Y') }}</div>
        <h1>Manifes Peserta Pelayaran</h1>
        <p>Dokumen resmi daftar penumpang untuk pelaporan dan keselamatan armada.</p>
    </div>
    <div class="actions">
        <a href="{{ route('admin.manifes.index') }}" class="btn-adm ghost">Kembali</a>
        <a href="{{ route('admin.manifes.pdf', $jadwal) }}" class="btn-adm orange">@include('partials.icon', ['name' => 'download', 'size' => 13]) Unduh PDF</a>
    </div>
</div>

<div class="adm-card" style="margin-bottom:16px">
    <div class="adm-card-body" style="display:grid;grid-template-columns:1.2fr 1fr;gap:18px">
        <div>
            <div style="font-size:9px;letter-spacing:.1em;text-transform:uppercase;color:var(--adm-muted);font-weight:800;margin-bottom:6px">Armada &amp; Paket Wisata</div>
            <div class="nm" style="font-size:13px">{{ $jadwal->paket->nama_paket }}</div>
            <div class="sub">{{ $jadwal->tanggal_berangkat->translatedFormat('d M') }} – {{ $jadwal->tanggal_kembali?->translatedFormat('d M Y') }} · ID {{ $jadwal->id }}</div>
            <div class="row mt-8" style="gap:6px">
                <span class="pill info">{{ $jadwal->paket->tipe_trip }}</span>
                <span class="pill gray">{{ $jadwal->paket->durasi_hari }} Hari</span>
            </div>
        </div>
        <div>
            <div style="font-size:9px;letter-spacing:.1em;text-transform:uppercase;color:var(--adm-muted);font-weight:800;margin-bottom:6px">Jadwal Pelayaran</div>
            <div class="nm" style="font-size:12.5px">{{ $jadwal->paket->destinasi->nama_destinasi }}</div>
            <div class="sub">{{ $jadwal->paket->destinasi->lokasi }}</div>
            <div class="sub" style="margin-top:6px">
                <strong>Titik kumpul:</strong> {{ $jadwal->kota_kumpul ?? '—' }}{{ $jadwal->titik_kumpul ? ' — '.$jadwal->titik_kumpul : '' }}{{ $jadwal->jam_kumpul ? ' ('.$jadwal->jam_kumpul->format('H:i').')' : '' }}
            </div>
            <div class="bar mt-8"><i class="{{ $persen >= 90 ? 'err' : ($persen >= 70 ? 'warn' : '') }}" style="width:{{ $persen }}%"></i></div>
            <div class="sub" style="margin-top:5px">{{ $terisi }}/{{ $jadwal->kuota_peserta }} kursi terisi ({{ $persen }}%)</div>
        </div>
    </div>
</div>

<div class="adm-kpi">
    <div class="kpi">
        <div class="lbl">Total Penumpang</div>
        <div class="val">{{ $totalPeserta }} <span style="font-size:11px;color:var(--adm-muted)">Orang</span></div>
        <div class="sub">{{ $pemesanan->count() }} pemesanan terkonfirmasi</div>
    </div>
    <div class="kpi">
        <div class="lbl">Kapasitas Maksimal</div>
        <div class="val">{{ $jadwal->kuota_peserta }}</div>
        <div class="sub">Sesuai kuota jadwal</div>
    </div>
    <div class="kpi">
        <div class="lbl">Status Kesiapan</div>
        <div class="val" style="font-size:15px;color:#15803d">Siap Berlayar</div>
        <div class="sub">Manifest lengkap</div>
    </div>
    <div class="kpi dark">
        <div class="lbl">Nahkoda &amp; Crew</div>
        <div class="val" style="font-size:13px">{{ $jadwal->penugasan->map(fn($p) => $p->petugas->nama_petugas)->join(', ') ?: 'Belum ditugaskan' }}</div>
        <div class="sub">{{ $jadwal->penugasan->count() }} petugas</div>
    </div>
</div>

<div class="adm-card">
    <div class="adm-card-head">
        <div><h3>Daftar Manifes Resmi Penumpang</h3><p>{{ $totalPeserta }} peserta terkonfirmasi</p></div>
        <span class="pill ok">{{ $pemesanan->count() }} pemesanan</span>
    </div>
    <div style="overflow-x:auto">
        <table class="adm-table">
            <thead>
                <tr><th>No</th><th>Nama Lengkap</th><th>Kategori</th><th>NIK / Paspor</th><th>Domisili</th><th>Kode Booking</th><th>Kontak Pemesan</th></tr>
            </thead>
            <tbody>
                @php($no = 1)
                @forelse($pemesanan as $p)
                    @foreach($p->peserta as $ps)
                        <tr>
                            <td>{{ str_pad($no++, 2, '0', STR_PAD_LEFT) }}</td>
                            <td><div class="nm">{{ $ps->nama_peserta }}</div><div class="sub">{{ $ps->jenis_kelamin === 'P' ? 'Perempuan' : 'Laki-laki' }}</div></td>
                            <td><span class="pill {{ ($ps->kategori ?? 'dewasa') === 'anak' ? 'info' : 'gray' }}">{{ ucfirst($ps->kategori ?? 'dewasa') }}</span></td>
                            <td style="font-family:ui-monospace,monospace;font-size:10.5px">{{ $ps->no_identitas }}</td>
                            <td class="sub">{{ $ps->kota ?: '—' }}</td>
                            <td style="font-family:ui-monospace,monospace;font-size:10.5px">{{ $p->kode_pemesanan }}</td>
                            <td>
                                <div class="sub">{{ $p->user->nama_lengkap }}</div>
                                <div class="sub">{{ $p->user->no_hp }}</div>
                            </td>
                        </tr>
                    @endforeach
                @empty
                    <tr><td colspan="7" class="empty" style="padding:28px">Belum ada peserta terkonfirmasi pada keberangkatan ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="adm-card adm-dark" style="margin-top:16px;padding:18px 20px">
    <div class="row between wrap" style="gap:16px">
        <div>
            <h4 style="margin:0 0 5px;font-size:13.5px">Ketentuan Keselamatan &amp; Pelaporan</h4>
            <p style="margin:0;font-size:11px;max-width:660px">Manifest dibuat otomatis dari pemesanan terkonfirmasi. Perubahan peserta wajib disinkronkan sebelum keberangkatan.</p>
        </div>
        <a href="{{ route('admin.manifes.pdf', $jadwal) }}" class="btn-adm orange">Cetak Manifes</a>
    </div>
</div>
@endsection
