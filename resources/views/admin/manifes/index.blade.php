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

{{-- FORM PENCARIAN & FILTER MANIFES --}}
<div class="adm-card" style="margin-bottom:18px;padding:16px 18px">
    <form method="GET" action="{{ route('admin.manifes.index') }}" class="row wrap" style="gap:10px;align-items:center">
        <div style="flex:1 1 240px;position:relative">
            <input type="text" name="q" value="{{ request('q') }}"
                   placeholder="Cari armada, paket wisata, destinasi, atau kru/guide..."
                   style="width:100%;font-size:12px;padding:9px 12px 9px 34px;border:1px solid var(--adm-line);border-radius:8px">
            <span style="position:absolute;left:11px;top:50%;transform:translateY(-50%);opacity:.5">
                @include('partials.icon', ['name' => 'search', 'size' => 14])
            </span>
        </div>

        <div style="flex:0 0 190px">
            <select name="destinasi" style="width:100%;font-size:12px;padding:9px 10px;border:1px solid var(--adm-line);border-radius:8px">
                <option value="">Semua Destinasi</option>
                @foreach($daftarDestinasi ?? [] as $d)
                    <option value="{{ $d->id }}" @selected(request('destinasi') == $d->id)>{{ $d->nama_destinasi }}</option>
                @endforeach
            </select>
        </div>

        <div style="flex:0 0 160px">
            <select name="status" style="width:100%;font-size:12px;padding:9px 10px;border:1px solid var(--adm-line);border-radius:8px">
                <option value="">Semua Jadwal</option>
                <option value="siap" @selected(request('status') === 'siap')>Ada Penumpang (Siap)</option>
                <option value="kosong" @selected(request('status') === 'kosong')>Belum Ada Penumpang</option>
            </select>
        </div>

        <button type="submit" class="btn-adm orange" style="padding:9px 18px">Cari Manifes</button>
        @if(request()->anyFilled(['q', 'destinasi', 'status']))
            <a href="{{ route('admin.manifes.index') }}" class="btn-adm ghost" style="padding:9px 14px">Reset</a>
        @endif
    </form>
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
