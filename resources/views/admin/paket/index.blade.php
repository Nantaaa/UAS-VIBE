@extends('layouts.admin')

@section('title', 'Kelola Paket Wisata')

@php
    $semua = \App\Models\PaketWisata::all();
    $aktifCount = $semua->where('is_active', true)->count();
    $rataTarif = (int) round($semua->avg('tarif') ?? 0);
    $openTrip = $semua->where('tipe_trip', 'Open Trip')->count();
    $privateTrip = $semua->where('tipe_trip', 'Private Trip')->count();
@endphp

@section('content')
<div class="adm-head">
    <div>
        <div class="crumb">Admin Portal › Master Data › Kelola Paket Wisata</div>
        <h1>Kelola Paket Wisata</h1>
        <p>Katalog inventori ekspedisi bahari, armada, dan overlay tour, serta manajemen kuota dan tarif resmi Nanta Travel.</p>
    </div>
    <div class="actions">
        <a href="{{ route('admin.paket.index') }}" class="btn-adm dark">@include('partials.icon', ['name' => 'list', 'size' => 13]) Daftar Paket</a>
        <a href="{{ route('admin.paket.create') }}" class="btn-adm ghost">Formulir Editor</a>
        <a href="{{ route('admin.laporan.excel', ['bulan' => now()->month, 'tahun' => now()->year]) }}" class="btn-adm ghost">@include('partials.icon', ['name' => 'download', 'size' => 13]) Export</a>
        <a href="{{ route('admin.paket.create') }}" class="btn-adm orange">@include('partials.icon', ['name' => 'plus', 'size' => 13]) Tambah Paket Baru</a>
    </div>
</div>

<div class="adm-kpi">
    <div class="kpi">
        <div class="lbl"><span class="dot-green"></span> Total Paket Aktif</div>
        <div class="val">{{ $aktifCount }}</div>
        <div class="sub">Tersedia online</div>
    </div>
    <div class="kpi">
        <div class="lbl">Open Trip</div>
        <div class="val">{{ $openTrip }}</div>
        <div class="sub">Rute berbagi peserta</div>
    </div>
    <div class="kpi">
        <div class="lbl">Private Trip</div>
        <div class="val">{{ $privateTrip }}</div>
        <div class="sub">Rute eksklusif</div>
    </div>
    <div class="kpi dark">
        <div class="lbl">Rata-rata Tarif / Pax</div>
        <div class="val">Rp {{ number_format($rataTarif, 0, ',', '.') }}</div>
        <div class="sub">{{ $semua->count() }} paket terdaftar</div>
    </div>
</div>

<div class="adm-card">
    <form method="GET" class="adm-filter" style="border-bottom:1px solid var(--adm-line)">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari paket ekspedisi...">
        <select name="destinasi">
            <option value="">Semua Destinasi</option>
            @foreach(\App\Models\Destinasi::orderBy('nama_destinasi')->get() as $d)
                <option value="{{ $d->id }}" @selected(request('destinasi') == $d->id)>{{ $d->nama_destinasi }}</option>
            @endforeach
        </select>
        <select name="durasi">
            <option value="">Semua Durasi</option>
            @foreach([2,3,4,5] as $dur)
                <option value="{{ $dur }}" @selected(request('durasi') == $dur)>{{ $dur }} Hari</option>
            @endforeach
        </select>
        <select name="status">
            <option value="">Semua Status</option>
            <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
            <option value="nonaktif" @selected(request('status') === 'nonaktif')>Nonaktif</option>
        </select>
        <button class="btn-adm ghost sm" type="submit">Terapkan</button>
        <a href="{{ route('admin.paket.index') }}" class="btn-adm ghost sm">Reset</a>
    </form>

    <div style="overflow-x:auto">
        <table class="adm-table">
            <thead>
                <tr>
                    <th>Paket Wisata</th><th>Destinasi</th><th>Durasi</th><th>Tarif / Orang</th><th>Status</th><th style="width:170px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($paket as $p)
                    <tr>
                        <td>
                            <div class="row" style="gap:10px">
                                @if($p->foto)
                                    <img src="{{ '/storage/'.$p->foto }}" class="thumb" alt="">
                                @else
                                    <span class="thumb"></span>
                                @endif
                                <div>
                                    <div class="nm">{{ Str::limit($p->nama_paket, 34) }}</div>
                                    <div class="sub">{{ $p->tipe_trip }} · {{ $p->jadwal_count }} jadwal</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="pill info">@include('partials.icon', ['name' => 'map-pin', 'size' => 10]) {{ $p->destinasi->nama_destinasi }}</span></td>
                        <td>{{ $p->durasi_hari }} Hari {{ $p->durasi_hari - 1 }} Malam</td>
                        <td><strong>Rp {{ number_format($p->tarif, 0, ',', '.') }}</strong></td>
                        <td>
                            <span class="pill {{ $p->is_active ? 'ok' : 'gray' }}">{{ $p->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                        </td>
                        <td>
                            <div class="row" style="gap:6px">
                                <a href="{{ route('admin.paket.edit', $p) }}" class="btn-adm ghost sm" title="Ubah">@include('partials.icon', ['name' => 'edit', 'size' => 12])</a>
                                <a href="{{ route('paket.detail', $p) }}" class="btn-adm ghost sm" title="Lihat di situs" target="_blank">@include('partials.icon', ['name' => 'arrow-right', 'size' => 12])</a>
                                <form method="POST" action="{{ route('admin.paket.destroy', $p) }}" onsubmit="return confirm('Nonaktifkan paket ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn-adm red sm" type="submit" title="Nonaktifkan">@include('partials.icon', ['name' => 'trash', 'size' => 12])</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty" style="padding:28px">Belum ada paket wisata.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="adm-foot">
        <span>Menampilkan {{ $paket->firstItem() ?? 0 }} - {{ $paket->lastItem() ?? 0 }} dari {{ $paket->total() }} paket wisata</span>
        <div class="pagination" style="margin:0">
            @if($paket->onFirstPage()) <span class="disabled">‹</span> @else <a href="{{ $paket->previousPageUrl() }}">‹</a> @endif
            @foreach($paket->getUrlRange(1, max(1, $paket->lastPage())) as $page => $url)
                <a href="{{ $url }}" class="{{ $page == $paket->currentPage() ? 'active' : '' }}">{{ $page }}</a>
            @endforeach
            @if($paket->hasMorePages()) <a href="{{ $paket->nextPageUrl() }}">›</a> @else <span class="disabled">›</span> @endif
        </div>
    </div>
</div>
@endsection
