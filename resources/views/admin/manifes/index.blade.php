@extends('layouts.admin')

@section('title', 'Manifes Peserta')
@section('heading', 'Manifes Peserta')

@section('content')
<p class="muted">Manifes disusun otomatis dari pemesanan berstatus <strong>Terkonfirmasi</strong> pada setiap keberangkatan.</p>

<div class="card">
    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr><th>Paket</th><th>Berangkat</th><th>Kuota</th><th>Peserta terkonfirmasi</th><th>Petugas</th><th style="width:170px">Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($jadwal as $j)
                    <tr>
                        <td>
                            <strong>{{ $j->paket->nama_paket }}</strong>
                            <div class="tiny muted">{{ $j->paket->destinasi->nama_destinasi }}</div>
                        </td>
                        <td>{{ $j->tanggal_berangkat->translatedFormat('d M Y') }}</td>
                        <td>{{ $j->kuota_peserta }}</td>
                        <td><span class="badge badge-info">{{ $j->peserta_terkonfirmasi }} peserta</span></td>
                        <td class="tiny muted">
                            {{ $j->penugasan->map(fn($p) => $p->petugas->nama_petugas.' ('.$p->petugas->peran.')')->join(', ') ?: '-' }}
                        </td>
                        <td>
                            <div class="row" style="gap:6px">
                                <a href="{{ route('admin.manifes.show', $j) }}" class="btn btn-ghost btn-sm">Lihat</a>
                                <a href="{{ route('admin.manifes.pdf', $j) }}" class="btn btn-primary btn-sm">PDF</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty">Belum ada jadwal keberangkatan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="pagination">
    @foreach($jadwal->getUrlRange(1, $jadwal->lastPage()) as $page => $url)
        <a href="{{ $url }}" class="{{ $page == $jadwal->currentPage() ? 'active' : '' }}">{{ $page }}</a>
    @endforeach
</div>
@endsection
