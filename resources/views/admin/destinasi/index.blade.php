@extends('layouts.admin')

@section('title', 'Kelola Destinasi')
@section('heading', 'Kelola Destinasi')

@section('content')
<div class="row between mb-16">
    <p class="muted mb-0">Data destinasi dipakai sebagai kategori paket wisata.</p>
    <a href="{{ route('admin.destinasi.create') }}" class="btn btn-primary btn-sm">+ Tambah destinasi</a>
</div>

<div class="card">
    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr><th>Nama destinasi</th><th>Lokasi</th><th>Jumlah paket</th><th>Deskripsi</th><th style="width:150px">Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($destinasi as $d)
                    <tr>
                        <td><strong>{{ $d->nama_destinasi }}</strong></td>
                        <td>{{ $d->lokasi }}</td>
                        <td><span class="badge badge-info">{{ $d->paket_count }} paket</span></td>
                        <td class="muted">{{ Str::limit($d->deskripsi, 60) }}</td>
                        <td>
                            <div class="row" style="gap:6px">
                                <a href="{{ route('admin.destinasi.edit', $d) }}" class="btn btn-ghost btn-sm">Ubah</a>
                                <form method="POST" action="{{ route('admin.destinasi.destroy', $d) }}"
                                      onsubmit="return confirm('Hapus destinasi ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger-soft btn-sm" type="submit">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty">Belum ada destinasi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="pagination">
    @foreach($destinasi->getUrlRange(1, $destinasi->lastPage()) as $page => $url)
        <a href="{{ $url }}" class="{{ $page == $destinasi->currentPage() ? 'active' : '' }}">{{ $page }}</a>
    @endforeach
</div>
@endsection
