@extends('layouts.admin')

@section('title', 'Jadwal & Kuota')
@section('heading', 'Jadwal Keberangkatan & Kuota')

@section('content')
<div class="row between wrap mb-16" style="gap:12px">
    <form method="GET" class="row" style="gap:8px">
        <select name="paket" style="min-width:220px">
            <option value="">Semua paket</option>
            @foreach($daftarPaket as $p)
                <option value="{{ $p->id }}" @selected(request('paket') == $p->id)>{{ $p->nama_paket }}</option>
            @endforeach
        </select>
        <button class="btn btn-ghost btn-sm" type="submit">Filter</button>
    </form>
    <a href="{{ route('admin.jadwal.create') }}" class="btn btn-primary btn-sm">+ Tambah jadwal</a>
</div>

<div class="card">
    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr>
                    <th>Paket</th><th>Berangkat</th><th>Kembali</th><th>Kuota</th>
                    <th>Terpakai</th><th>Sisa</th><th>Petugas</th><th style="width:150px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jadwal as $j)
                    <tr>
                        <td>
                            <strong>{{ Str::limit($j->paket->nama_paket, 30) }}</strong>
                            <div class="tiny muted">{{ $j->paket->destinasi->nama_destinasi }}</div>
                        </td>
                        <td>{{ $j->tanggal_berangkat->translatedFormat('d M Y') }}</td>
                        <td>{{ $j->tanggal_kembali?->translatedFormat('d M Y') ?? '-' }}</td>
                        <td>{{ $j->kuota_peserta }}</td>
                        <td>{{ $j->kuota_peserta - $j->sisa_kuota }}</td>
                        <td>
                            <span class="badge badge-{{ $j->sisa_kuota > 0 ? ($j->sisa_kuota <= 3 ? 'warning' : 'success') : 'danger' }}">
                                {{ $j->sisa_kuota }} kursi
                            </span>
                        </td>
                        <td class="tiny muted">
                            {{ $j->penugasan->map(fn($p) => $p->petugas->nama_petugas)->join(', ') ?: '-' }}
                        </td>
                        <td>
                            <div class="row" style="gap:6px">
                                <a href="{{ route('admin.jadwal.edit', $j) }}" class="btn btn-ghost btn-sm">Ubah</a>
                                <form method="POST" action="{{ route('admin.jadwal.destroy', $j) }}"
                                      onsubmit="return confirm('Hapus jadwal ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger-soft btn-sm" type="submit">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="empty">Belum ada jadwal keberangkatan.</td></tr>
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
