@extends('layouts.admin')

@section('title', 'Manifes Keberangkatan')
@section('heading', 'Manifes Keberangkatan')

@section('content')
<div class="row between wrap mb-16">
    <div>
        <h3 style="margin:0">{{ $jadwal->paket->nama_paket }}</h3>
        <div class="tiny muted">
            {{ $jadwal->paket->destinasi->nama_destinasi }} ·
            {{ $jadwal->tanggal_berangkat->translatedFormat('d F Y') }} ·
            kuota {{ $jadwal->kuota_peserta }}
        </div>
    </div>
    <div class="row" style="gap:8px">
        <a href="{{ route('admin.manifes.pdf', $jadwal) }}" class="btn btn-primary btn-sm">Unduh PDF</a>
        <a href="{{ route('admin.manifes.index') }}" class="btn btn-ghost btn-sm">Kembali</a>
    </div>
</div>

<div class="grid grid-3 mb-16">
    <div class="card stat">
        <div class="ico green">👥</div>
        <div><div class="val">{{ $totalPeserta }}</div><div class="lbl">Total peserta</div></div>
    </div>
    <div class="card stat">
        <div class="ico">▤</div>
        <div><div class="val">{{ $pemesanan->count() }}</div><div class="lbl">Pemesanan terkonfirmasi</div></div>
    </div>
    <div class="card stat">
        <div class="ico orange">🚌</div>
        <div>
            <div class="val" style="font-size:15px">
                {{ $jadwal->penugasan->map(fn($p) => $p->petugas->nama_petugas)->join(', ') ?: 'Belum ditugaskan' }}
            </div>
            <div class="lbl">Petugas bertugas</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-head"><h3>Daftar peserta</h3></div>
    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr><th>No</th><th>Nama peserta</th><th>Identitas</th><th>Pemesan</th><th>Kode</th></tr>
            </thead>
            <tbody>
                @php($no = 1)
                @forelse($pemesanan as $p)
                    @foreach($p->peserta as $ps)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td><strong>{{ $ps->nama_peserta }}</strong></td>
                            <td class="tiny">{{ $ps->jenis_identitas }} · {{ $ps->no_identitas }}</td>
                            <td>{{ $p->user->nama_lengkap }}</td>
                            <td class="tiny muted">{{ $p->kode_pemesanan }}</td>
                        </tr>
                    @endforeach
                @empty
                    <tr><td colspan="5" class="empty">Belum ada peserta terkonfirmasi pada keberangkatan ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
