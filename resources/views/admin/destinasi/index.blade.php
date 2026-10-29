@extends('layouts.admin')

@section('title', 'Kelola Destinasi')

@php($total = $destinasi->total())
@php($aktif = \App\Models\Destinasi::count())

@section('content')
<div class="adm-head">
    <div>
        <div class="crumb">Admin Portal › Master Data › Destinasi</div>
        <h1>Kelola Destinasi Wisata</h1>
        <p>Data destinasi menjadi kategori katalog paket wisata dan titik meeting point keberangkatan.</p>
    </div>
    <div class="actions">
        <a href="{{ route('admin.destinasi.create') }}" class="btn-adm orange">
            @include('partials.icon', ['name' => 'plus', 'size' => 13]) Tambah Destinasi
        </a>
    </div>
</div>

<div class="adm-kpi">
    <div class="kpi">
        <div class="lbl">Total Destinasi</div>
        <div class="val">{{ $aktif }}</div>
        <div class="sub">Terdaftar di katalog</div>
    </div>
    <div class="kpi">
        <div class="lbl">Dipakai Paket</div>
        <div class="val">{{ $destinasi->getCollection()->where('paket_count', '>', 0)->count() }}</div>
        <div class="sub">Destinasi dengan paket aktif</div>
    </div>
    <div class="kpi">
        <div class="lbl">Belum Dipakai</div>
        <div class="val">{{ $destinasi->getCollection()->where('paket_count', 0)->count() }}</div>
        <div class="sub">Bisa dihapus tanpa konflik</div>
    </div>
    <div class="kpi dark">
        <div class="lbl">Halaman</div>
        <div class="val">{{ $destinasi->currentPage() }}/{{ max(1, $destinasi->lastPage()) }}</div>
        <div class="sub">{{ $total }} destinasi total</div>
    </div>
</div>

<div class="adm-card">
    <div class="adm-card-head">
        <div>
            <h3>Daftar Destinasi</h3>
            <p>Kelola nama, lokasi, deskripsi, dan foto destinasi.</p>
        </div>
        <form method="GET" class="adm-filter" style="padding:0">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari destinasi...">
            <button class="btn-adm ghost sm" type="submit">Filter</button>
        </form>
    </div>

    <div style="overflow-x:auto">
        <table class="adm-table">
            <thead>
                <tr><th>Destinasi</th><th>Lokasi</th><th>Deskripsi</th><th>Paket</th><th>Status</th><th style="width:150px">Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($destinasi as $d)
                    <tr>
                        <td>
                            <div class="row" style="gap:10px">
                                @if($d->foto)
                                    <img src="{{ '/storage/'.$d->foto }}" class="thumb" alt="">
                                @else
                                    <span class="thumb" style="display:grid;place-items:center;color:#fff;font-size:12px">{{ strtoupper(substr($d->nama_destinasi,0,1)) }}</span>
                                @endif
                                <div>
                                    <div class="nm">{{ $d->nama_destinasi }}</div>
                                    <div class="sub">ID #{{ $d->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="pill info">@include('partials.icon', ['name' => 'map-pin', 'size' => 10]) {{ $d->lokasi }}</span>
                        </td>
                        <td class="sub" style="max-width:280px">{{ Str::limit($d->deskripsi, 70) }}</td>
                        <td>{{ $d->paket_count }} paket</td>
                        <td><span class="pill ok">Aktif</span></td>
                        <td>
                            <div class="row" style="gap:6px">
                                <a href="{{ route('admin.destinasi.edit', $d) }}" class="btn-adm ghost sm">Ubah</a>
                                <form method="POST" action="{{ route('admin.destinasi.destroy', $d) }}" onsubmit="return confirm('Hapus destinasi ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn-adm red sm" type="submit">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty" style="padding:28px">Belum ada destinasi. Tambahkan yang pertama.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="adm-foot">
        <span>Menampilkan {{ $destinasi->firstItem() ?? 0 }} - {{ $destinasi->lastItem() ?? 0 }} dari {{ $total }} destinasi</span>
        <div class="pagination" style="margin:0">
            @if($destinasi->onFirstPage()) <span class="disabled">‹</span> @else <a href="{{ $destinasi->previousPageUrl() }}">‹</a> @endif
            @foreach($destinasi->getUrlRange(1, max(1, $destinasi->lastPage())) as $page => $url)
                <a href="{{ $url }}" class="{{ $page == $destinasi->currentPage() ? 'active' : '' }}">{{ $page }}</a>
            @endforeach
            @if($destinasi->hasMorePages()) <a href="{{ $destinasi->nextPageUrl() }}">›</a> @else <span class="disabled">›</span> @endif
        </div>
    </div>
</div>
@endsection
