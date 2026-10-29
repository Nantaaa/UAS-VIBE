@extends('layouts.admin')

@section('title', 'Kelola Paket Wisata')
@section('heading', 'Kelola Paket Wisata')

@section('content')
<form method="GET" class="row between wrap mb-16" style="gap:12px">
    <div class="row" style="gap:8px">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama paket..." style="min-width:240px">
        <button class="btn btn-ghost btn-sm" type="submit">Cari</button>
    </div>
    <a href="{{ route('admin.paket.create') }}" class="btn btn-primary btn-sm">+ Tambah paket</a>
</form>

<div class="card">
    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr><th>Paket</th><th>Destinasi</th><th>Durasi</th><th>Tarif</th><th>Jadwal</th><th>Status</th><th style="width:160px">Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($paket as $p)
                    <tr>
                        <td><strong>{{ $p->nama_paket }}</strong></td>
                        <td>{{ $p->destinasi->nama_destinasi }}</td>
                        <td>{{ $p->durasi_hari }} hari</td>
                        <td>Rp {{ number_format($p->tarif, 0, ',', '.') }}</td>
                        <td>{{ $p->jadwal_count }}</td>
                        <td>
                            <span class="badge badge-{{ $p->is_active ? 'success' : 'secondary' }}">
                                {{ $p->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td>
                            <div class="row" style="gap:6px">
                                <a href="{{ route('admin.paket.edit', $p) }}" class="btn btn-ghost btn-sm">Ubah</a>
                                <form method="POST" action="{{ route('admin.paket.destroy', $p) }}"
                                      onsubmit="return confirm('Nonaktifkan paket ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger-soft btn-sm" type="submit">Nonaktifkan</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty">Belum ada paket wisata.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="pagination">
    @foreach($paket->getUrlRange(1, $paket->lastPage()) as $page => $url)
        <a href="{{ $url }}" class="{{ $page == $paket->currentPage() ? 'active' : '' }}">{{ $page }}</a>
    @endforeach
</div>
@endsection
