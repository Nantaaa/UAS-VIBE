@extends('layouts.admin')

@section('title', 'Jadwal Keberangkatan')

@php
    $totalPax = \App\Models\JadwalKeberangkatan::sum('kuota_peserta');
    $terisi = \App\Models\Peserta::whereHas('pemesanan', fn ($q) => $q->whereIn('status_pemesanan', ['terkonfirmasi','menunggu_verifikasi','perlu_perbaikan','menunggu_pembayaran']))->count();
@endphp

@section('content')
<div class="adm-head">
    <div>
        <div class="crumb">Admin Portal › Operasional › Jadwal Keberangkatan</div>
        <h1>Jadwal Keberangkatan &amp; Kuota</h1>
        <p>Kelola tanggal berangkat, kuota peserta, dan penugasan guide/driver. Kuota tidak boleh lebih kecil dari peserta aktif.</p>
    </div>
    <div class="actions">
        <form method="GET" class="adm-filter" style="padding:0">
            <select name="paket" onchange="this.form.submit()">
                <option value="">Semua Paket</option>
                @foreach($daftarPaket as $p)
                    <option value="{{ $p->id }}" @selected(request('paket') == $p->id)>{{ Str::limit($p->nama_paket, 32) }}</option>
                @endforeach
            </select>
        </form>
        <a href="{{ route('admin.jadwal.create') }}" class="btn-adm orange">@include('partials.icon', ['name' => 'plus', 'size' => 13]) Tambah Jadwal</a>
    </div>
</div>

<div class="adm-kpi">
    <div class="kpi">
        <div class="lbl">Total Jadwal</div>
        <div class="val">{{ $jadwal->total() }}</div>
        <div class="sub">Keberangkatan terdaftar</div>
    </div>
    <div class="kpi">
        <div class="lbl">Total Kuota</div>
        <div class="val">{{ $totalPax }}</div>
        <div class="sub">Kursi tersedia di semua jadwal</div>
    </div>
    <div class="kpi">
        <div class="lbl">Terisi</div>
        <div class="val">{{ $terisi }}</div>
        <div class="sub">Peserta pada pemesanan aktif</div>
    </div>
    <div class="kpi dark">
        <div class="lbl">Okupansi</div>
        <div class="val">{{ $totalPax > 0 ? round($terisi / $totalPax * 100) : 0 }}%</div>
        <div class="sub">Rata-rata keterisian</div>
    </div>
</div>

<div class="adm-card">
    <div class="adm-card-head">
        <div><h3>Daftar Keberangkatan</h3><p>Warna progress: hijau longgar, kuning hampir penuh, merah penuh.</p></div>
    </div>
    <div style="overflow-x:auto">
        <table class="adm-table">
            <thead>
                <tr>
                    <th>Paket</th><th>Berangkat</th><th>Kembali</th><th>Kuota</th><th>Terisi</th><th>Progress</th><th>Petugas</th><th style="width:140px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jadwal as $j)
                    @php
                        $terpakai = $j->kuota_peserta - $j->sisa_kuota;
                        $persen = $j->kuota_peserta > 0 ? (int) round($terpakai / $j->kuota_peserta * 100) : 0;
                        $warna = $persen >= 90 ? 'err' : ($persen >= 70 ? 'warn' : '');
                    @endphp
                    <tr>
                        <td>
                            <div class="nm">{{ Str::limit($j->paket->nama_paket, 30) }}</div>
                            <div class="sub">{{ $j->paket->destinasi->nama_destinasi }} · ID {{ $j->id }}</div>
                        </td>
                        <td>
                            <div class="nm">{{ $j->tanggal_berangkat->translatedFormat('d M Y') }}</div>
                            <div class="sub">{{ $j->paket->durasi_hari }} Hari {{ $j->paket->durasi_hari - 1 }} Malam</div>
                        </td>
                        <td>{{ $j->tanggal_kembali?->translatedFormat('d M Y') ?? '—' }}</td>
                        <td><strong>{{ $j->kuota_peserta }}</strong> <span class="sub">pax</span></td>
                        <td>
                            {{ $terpakai }}
                            <span class="pill {{ $persen >= 90 ? 'err' : ($persen >= 70 ? 'warn' : 'ok') }}" style="margin-left:4px">{{ $persen }}%</span>
                        </td>
                        <td>
                            <div class="bar"><i class="{{ $warna }}" style="width:{{ $persen }}%"></i></div>
                            <div class="sub" style="margin-top:4px">{{ $j->sisa_kuota }}/{{ $j->kuota_peserta }} sisa</div>
                        </td>
                        <td class="sub">{{ $j->penugasan->map(fn($p) => $p->petugas->nama_petugas)->join(', ') ?: '—' }}</td>
                        <td>
                            <div class="row" style="gap:6px">
                                <a href="{{ route('admin.jadwal.edit', $j) }}" class="btn-adm ghost sm">@include('partials.icon', ['name' => 'edit', 'size' => 12])</a>
                                <form method="POST" action="{{ route('admin.jadwal.destroy', $j) }}" onsubmit="return confirm('Hapus jadwal ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn-adm red sm" type="submit">@include('partials.icon', ['name' => 'trash', 'size' => 12])</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="empty" style="padding:28px">Belum ada jadwal keberangkatan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="adm-foot">
        <span>Menampilkan {{ $jadwal->firstItem() ?? 0 }} - {{ $jadwal->lastItem() ?? 0 }} dari {{ $jadwal->total() }} jadwal</span>
        <div class="pagination" style="margin:0">
            @if($jadwal->onFirstPage()) <span class="disabled">‹</span> @else <a href="{{ $jadwal->previousPageUrl() }}">‹</a> @endif
            @foreach($jadwal->getUrlRange(1, max(1, $jadwal->lastPage())) as $page => $url)
                <a href="{{ $url }}" class="{{ $page == $jadwal->currentPage() ? 'active' : '' }}">{{ $page }}</a>
            @endforeach
            @if($jadwal->hasMorePages()) <a href="{{ $jadwal->nextPageUrl() }}">›</a> @else <span class="disabled">›</span> @endif
        </div>
    </div>
</div>

<div class="adm-card adm-dark" style="margin-top:16px;padding:18px 20px">
    <div class="row between wrap" style="gap:14px">
        <div>
            <h4 style="margin:0 0 5px;font-size:13.5px">SOP Perubahan Jadwal &amp; Kuota</h4>
            <p style="margin:0;font-size:11px;max-width:640px">Setiap perubahan kuota di bawah jumlah peserta aktif akan ditolak sistem. Batalkan atau pindahkan pemesanan terlebih dahulu melalui menu Verifikasi Pemesanan.</p>
        </div>
        <a href="{{ route('admin.verifikasi.index') }}" class="btn-adm orange">Buka Verifikasi</a>
    </div>
</div>
@endsection
