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
        <a href="{{ route('admin.jadwal.create') }}" class="btn-adm orange">@include('partials.icon', ['name' => 'plus', 'size' => 13]) Tambah Jadwal</a>
    </div>
</div>

{{-- KPI Ringkasan --}}
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

{{-- FORM PENCARIAN & FILTER JADWAL --}}
<div class="adm-card" style="margin-bottom:18px;padding:16px 18px">
    <form method="GET" action="{{ route('admin.jadwal.index') }}" class="row wrap" style="gap:10px;align-items:center">
        {{-- Input Pencarian Keyword --}}
        <div style="flex:1 1 240px;position:relative">
            <input type="text" name="q" value="{{ request('q') }}"
                   placeholder="Cari nama paket, destinasi, kota kumpul, atau pemandu..."
                   style="width:100%;font-size:12px;padding:9px 12px 9px 34px;border:1px solid var(--adm-line);border-radius:8px">
            <span style="position:absolute;left:11px;top:50%;transform:translateY(-50%);opacity:.5">
                @include('partials.icon', ['name' => 'search', 'size' => 14])
            </span>
        </div>

        {{-- Filter Paket Wisata --}}
        <div style="flex:0 0 200px">
            <select name="paket" style="width:100%;font-size:12px;padding:9px 10px;border:1px solid var(--adm-line);border-radius:8px">
                <option value="">Semua Paket Wisata</option>
                @foreach($daftarPaket as $p)
                    <option value="{{ $p->id }}" @selected(request('paket') == $p->id)>{{ Str::limit($p->nama_paket, 26) }}</option>
                @endforeach
            </select>
        </div>

        {{-- Filter Status --}}
        <div style="flex:0 0 150px">
            <select name="status" style="width:100%;font-size:12px;padding:9px 10px;border:1px solid var(--adm-line);border-radius:8px">
                <option value="">Semua Status</option>
                <option value="mendatang" @selected(request('status') === 'mendatang')>Mendatang</option>
                <option value="tersedia" @selected(request('status') === 'tersedia')>Masih Ada Kuota</option>
                <option value="penuh" @selected(request('status') === 'penuh')>Kuota Penuh</option>
                <option value="lewat" @selected(request('status') === 'lewat')>Sudah Lewat</option>
            </select>
        </div>

        {{-- Filter Tanggal --}}
        <div style="flex:0 0 140px">
            <input type="date" name="tanggal" value="{{ request('tanggal') }}"
                   style="width:100%;font-size:12px;padding:8px 10px;border:1px solid var(--adm-line);border-radius:8px">
        </div>

        <button type="submit" class="btn-adm orange" style="padding:9px 18px">Cari Jadwal</button>
        @if(request()->anyFilled(['q', 'paket', 'status', 'tanggal']))
            <a href="{{ route('admin.jadwal.index') }}" class="btn-adm ghost" style="padding:9px 14px">Reset</a>
        @endif
    </form>
</div>

{{-- TABEL JADWAL KEBERANGKATAN --}}
<div class="adm-card">
    <div class="adm-card-head">
        <div>
            <h3>Daftar Keberangkatan &amp; Daftar Peserta</h3>
            <p>Klik tombol <strong>👥 Peserta</strong> pada jadwal untuk melihat daftar nama peserta langsung, atau klik Manifes untuk laporan lengkap.</p>
        </div>
    </div>
    <div style="overflow-x:auto">
        <table class="adm-table">
            <thead>
                <tr>
                    <th>Paket Wisata</th>
                    <th>Berangkat</th>
                    <th>Kembali</th>
                    <th>Kuota</th>
                    <th>Daftar Peserta Terisi</th>
                    <th>Kapasitas</th>
                    <th>Pemandu / Kru</th>
                    <th style="width:145px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jadwal as $j)
                    @php
                        $terpakai = $j->kuota_peserta - $j->sisa_kuota;
                        $persen = $j->kuota_peserta > 0 ? (int) round($terpakai / $j->kuota_peserta * 100) : 0;
                        $warna = $persen >= 90 ? 'err' : ($persen >= 70 ? 'warn' : '');

                        // Kumpulkan daftar peserta lengkap untuk modal
                        $listPeserta = $j->pemesanan->flatMap(function ($order) {
                            return $order->peserta->map(function ($p) use ($order) {
                                return [
                                    'nama' => $p->nama_peserta,
                                    'kategori' => ucfirst($p->kategori ?? 'dewasa'),
                                    'jenis_id' => $p->jenis_identitas,
                                    'no_id' => $p->no_identitas,
                                    'status_dokumen' => $p->status,
                                    'pemesan' => $order->user->nama_lengkap ?? '—',
                                    'kontak' => $order->user->no_hp ?? '—',
                                    'kode' => $order->kode_pemesanan,
                                    'status_order' => $order->status_pemesanan,
                                ];
                            });
                        })->values()->all();
                    @endphp
                    <tr>
                        <td>
                            <div class="nm">{{ Str::limit($j->paket->nama_paket, 32) }}</div>
                            <div class="sub">{{ $j->paket->destinasi->nama_destinasi }} · ID #{{ $j->id }}</div>
                        </td>
                        <td>
                            <div class="nm">{{ $j->tanggal_berangkat->translatedFormat('d M Y') }}</div>
                            <div class="sub">{{ $j->paket->durasi_hari }} Hari {{ max(1, $j->paket->durasi_hari - 1) }} Malam</div>
                        </td>
                        <td>{{ $j->tanggal_kembali?->translatedFormat('d M Y') ?? '—' }}</td>
                        <td><strong>{{ $j->kuota_peserta }}</strong> <span class="sub">pax</span></td>
                        <td>
                            @if($terpakai > 0)
                                <button type="button" class="btn-adm sm"
                                        style="background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;font-weight:700;display:inline-flex;align-items:center;gap:5px;cursor:pointer"
                                        onclick='bukaModalPeserta(@json($j->paket->nama_paket), @json($j->tanggal_berangkat->translatedFormat("d M Y")), @json($j->kuota_peserta), @json($terpakai), @json($listPeserta), @json(route("admin.manifes.show", $j)))'
                                        title="Klik untuk melihat nama-nama peserta">
                                    <span>👥 {{ $terpakai }} Peserta</span>
                                </button>
                            @else
                                <span class="sub" style="font-size:12px;color:var(--adm-muted)">0 Peserta</span>
                            @endif
                            <span class="pill {{ $persen >= 90 ? 'err' : ($persen >= 70 ? 'warn' : 'ok') }}" style="margin-left:4px">{{ $persen }}%</span>
                        </td>
                        <td>
                            <div class="bar"><i class="{{ $warna }}" style="width:{{ $persen }}%"></i></div>
                            <div class="sub" style="margin-top:4px">{{ $j->sisa_kuota }}/{{ $j->kuota_peserta }} kursi sisa</div>
                        </td>
                        <td class="sub">{{ $j->penugasan->map(fn($p) => $p->petugas->nama_petugas)->join(', ') ?: '—' }}</td>
                        <td>
                            <div class="row" style="gap:6px">
                                <a href="{{ route('admin.manifes.show', $j) }}" class="btn-adm ghost sm" title="Buka Manifes Pelayaran Lengkap">
                                    @include('partials.icon', ['name' => 'users', 'size' => 12]) Manifes
                                </a>
                                <a href="{{ route('admin.jadwal.edit', $j) }}" class="btn-adm ghost sm" title="Edit Jadwal">
                                    @include('partials.icon', ['name' => 'edit', 'size' => 12])
                                </a>
                                <form method="POST" action="{{ route('admin.jadwal.destroy', $j) }}" onsubmit="return confirm('Hapus jadwal ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn-adm red sm" type="submit" title="Hapus Jadwal">
                                        @include('partials.icon', ['name' => 'trash', 'size' => 12])
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="empty" style="padding:28px">Tidak ditemukan jadwal keberangkatan yang sesuai.</td></tr>
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

{{-- MODAL POPUP: DAFTAR NAMA PESERTA JADWAL --}}
<div id="modalPeserta" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,0.72);backdrop-filter:blur(4px);z-index:99999;align-items:center;justify-content:center;padding:18px" onclick="if(event.target===this)tutupModalPeserta()">
    <div style="background:#ffffff;border-radius:14px;max-width:860px;width:100%;max-height:88vh;display:flex;flex-direction:column;box-shadow:0 25px 50px -12px rgba(0,0,0,0.35);overflow:hidden">
        {{-- Modal Head --}}
        <div style="padding:18px 22px;border-bottom:1px solid #e2e8f0;display:flex;justify-content:space-between;align-items:flex-start">
            <div>
                <span class="badge badge-info" style="font-size:10px;text-transform:uppercase;font-weight:800;margin-bottom:4px;display:inline-block">Daftar Manifest Peserta</span>
                <h3 id="modalPaketTitle" style="margin:0;font-size:16.5px;color:#0f172a">Paket Wisata</h3>
                <p id="modalJadwalSubtitle" style="margin:4px 0 0;font-size:12px;color:#64748b">Berangkat: — · Terisi: —</p>
            </div>
            <button type="button" onclick="tutupModalPeserta()" style="background:none;border:none;font-size:24px;line-height:1;cursor:pointer;color:#64748b">×</button>
        </div>

        {{-- Modal Body (Scrollable Table) --}}
        <div style="padding:16px 22px 28px;overflow-y:auto;flex:1;max-height:58vh">
            <table class="adm-table" style="font-size:11.5px">
                <thead>
                    <tr>
                        <th style="width:36px">No</th>
                        <th>Nama Peserta</th>
                        <th>Kategori</th>
                        <th>Nomor Identitas (KTP/Paspor)</th>
                        <th>Pemesan &amp; No. WA</th>
                        <th>Kode Booking</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="modalPesertaBody">
                    {{-- Diisi secara dinamis via JavaScript --}}
                </tbody>
            </table>
        </div>

        {{-- Modal Footer --}}
        <div style="padding:14px 22px;border-top:1px solid #e2e8f0;background:#f8fafc;display:flex;justify-content:space-between;align-items:center">
            <span class="tiny muted" id="modalTotalPesertaFooter">Total peserta terdaftar: 0</span>
            <div class="row" style="gap:8px">
                <a id="btnBukaManifes" href="#" class="btn-adm orange sm" target="_blank">
                    @include('partials.icon', ['name' => 'printer', 'size' => 12]) Buka &amp; Cetak Manifes Resmi
                </a>
                <button type="button" onclick="tutupModalPeserta()" class="btn-adm ghost sm">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
function bukaModalPeserta(namaPaket, tglBerangkat, kuota, terpakai, listPeserta, urlManifes) {
    document.getElementById('modalPaketTitle').textContent = namaPaket;
    document.getElementById('modalJadwalSubtitle').textContent = 'Keberangkatan: ' + tglBerangkat + ' · Kuota: ' + terpakai + ' / ' + kuota + ' kursi terisi';
    document.getElementById('modalTotalPesertaFooter').textContent = 'Total peserta terdaftar: ' + listPeserta.length + ' orang';
    document.getElementById('btnBukaManifes').href = urlManifes;

    const tbody = document.getElementById('modalPesertaBody');
    tbody.innerHTML = '';

    if (!listPeserta || listPeserta.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="empty" style="padding:24px;text-align:center">Belum ada peserta yang mendaftar pada jadwal ini.</td></tr>';
    } else {
        listPeserta.forEach((p, idx) => {
            const tr = document.createElement('tr');
            
            let statusBadge = '<span class="pill ok" style="background:#dcfce7;color:#166534;font-weight:700">✓ Terkonfirmasi</span>';
            if (p.status_order === 'menunggu_verifikasi') {
                statusBadge = '<span class="pill warn" style="background:#fef3c7;color:#92400e;font-weight:700">⏳ Menunggu Verif</span>';
            } else if (p.status_order === 'perlu_perbaikan') {
                statusBadge = '<span class="pill err" style="background:#fee2e2;color:#991b1b;font-weight:700">⚠️ Perlu Perbaikan</span>';
            } else if (p.status_order === 'menunggu_pembayaran') {
                statusBadge = '<span class="pill" style="background:#f1f5f9;color:#475569;font-weight:700;border:1px solid #cbd5e1">⏳ Belum Bayar</span>';
            }

            tr.innerHTML = `
                <td><strong>${idx + 1}</strong></td>
                <td>
                    <div style="font-weight:700;color:#0f172a">${p.nama}</div>
                </td>
                <td><span class="badge ${p.kategori.toLowerCase() === 'anak' ? 'badge-info' : ''}" style="font-size:10px">${p.kategori}</span></td>
                <td>
                    <code>${p.no_id || '—'}</code>
                    <span class="sub" style="font-size:10px;margin-left:4px">(${p.jenis_id})</span>
                </td>
                <td>
                    <div>${p.pemesan}</div>
                    <a href="https://wa.me/${(p.kontak || '').replace(/[^0-9]/g, '')}" target="_blank" class="sub" style="color:#0284c7;font-weight:600">
                        ${p.kontak}
                    </a>
                </td>
                <td><code style="font-weight:700">${p.kode}</code></td>
                <td>${statusBadge}</td>
            `;
            tbody.appendChild(tr);
        });
    }

    document.getElementById('modalPeserta').style.display = 'flex';
}

function tutupModalPeserta() {
    document.getElementById('modalPeserta').style.display = 'none';
}
</script>
@endsection
