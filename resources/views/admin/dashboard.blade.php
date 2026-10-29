@extends('layouts.admin')

@section('title', 'Dashboard')
@section('heading', 'Dashboard')

@php
    $maks = max(1, collect($grafik)->max('nilai'));
    $totalPesertaBulan = (int) \App\Models\Pemesanan::where('status_pemesanan', 'terkonfirmasi')
        ->whereYear('tanggal_pemesanan', now()->year)
        ->whereMonth('tanggal_pemesanan', now()->month)
        ->sum('jumlah_peserta');
    $antrean = \App\Models\Pemesanan::with(['user', 'jadwal.paket', 'pembayaranTerakhir'])
        ->whereIn('status_pemesanan', ['menunggu_verifikasi', 'perlu_perbaikan'])
        ->orderBy('tanggal_pemesanan')
        ->limit(5)->get();
@endphp

@section('content')
<div class="adm-head">
    <div>
        <div class="crumb">Admin Portal › Dashboard</div>
        <h1>Ringkasan Operasional &amp; Reservasi</h1>
        <p>Monitor real-time manifest, pembayaran, dan okupansi armada untuk operasional harian travel Nanta.</p>
    </div>
    <div class="actions">
        <a href="{{ route('admin.laporan.excel', ['bulan' => now()->month, 'tahun' => now()->year]) }}" class="btn-adm ghost">
            @include('partials.icon', ['name' => 'download', 'size' => 13]) Unduh Rekap (XLS)
        </a>
        <a href="{{ route('admin.paket.create') }}" class="btn-adm orange">
            @include('partials.icon', ['name' => 'plus', 'size' => 13]) Buat Paket Baru
        </a>
    </div>
</div>

<div class="adm-kpi">
    <div class="kpi">
        <div class="lbl"><span class="dot-green" style="background:#f59e0b"></span> Perlu Approval</div>
        <div class="val">{{ $menungguVerifikasi }} <span style="font-size:12px;font-weight:700;color:var(--adm-muted)">Pesanan</span></div>
        <div class="sub">{{ $perluPerbaikan }} perlu perbaikan dokumen</div>
        <a href="{{ route('admin.verifikasi.index') }}" class="lnk">Tinjau antrean sekarang →</a>
    </div>

    <div class="kpi">
        <div class="lbl"><span class="dot-green" style="background:#0d9488"></span> Peserta Bulan Ini</div>
        <div class="val">{{ $totalPesertaBulan }} <span style="font-size:12px;font-weight:700;color:var(--adm-muted)">Peserta</span></div>
        <div class="sub">{{ $terkonfirmasiBulanIni }} pemesanan terkonfirmasi</div>
        <a href="{{ route('admin.manifes.index') }}" class="lnk">Lihat manifest peserta</a>
    </div>

    <div class="kpi">
        <div class="lbl"><span class="dot-green" style="background:#2563eb"></span> Paket Aktif</div>
        <div class="val">{{ \App\Models\PaketWisata::where('is_active', true)->count() }} <span style="font-size:12px;font-weight:700;color:var(--adm-muted)">Paket</span></div>
        <div class="sub">{{ \App\Models\Destinasi::count() }} destinasi terdaftar</div>
        <a href="{{ route('admin.paket.index') }}" class="lnk">Kelola paket wisata</a>
    </div>

    <div class="kpi dark">
        <div class="lbl"><span class="dot-green"></span> Pendapatan Bulan Ini</div>
        <div class="val">Rp {{ number_format($pendapatanBulanIni, 0, ',', '.') }}</div>
        <div class="sub">Dari pemesanan terkonfirmasi</div>
        <a href="{{ route('admin.laporan.index') }}" class="lnk">Laporan keuangan lengkap →</a>
    </div>
</div>

<div class="adm-grid">
    <div style="display:flex;flex-direction:column;gap:16px">
        {{-- Antrean verifikasi --}}
        <div class="adm-card">
            <div class="adm-card-head">
                <div>
                    <h3>Pemesanan Menunggu Verifikasi</h3>
                    <p>Konfirmasi pembayaran dan validasi dokumen sebelum manifest dibuka.</p>
                </div>
                <form method="GET" action="{{ route('admin.verifikasi.index') }}" class="row" style="gap:6px">
                    <input type="text" name="q" placeholder="Cari pesanan..." style="font-size:11px;padding:7px 10px;border:1px solid var(--adm-line);border-radius:7px">
                    <button class="btn-adm ghost sm" type="submit">Filter</button>
                </form>
            </div>
            <div style="overflow-x:auto">
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th>Kode Reservasi</th><th>Peserta</th><th>Paket Ekspedisi</th><th>Tagihan</th><th>Metode &amp; Bank</th><th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($antrean as $p)
                            <tr>
                                <td><strong style="font-family:ui-monospace,monospace;font-size:11px">{{ $p->kode_pemesanan }}</strong></td>
                                <td>
                                    <div class="nm">{{ $p->user->nama_lengkap }}</div>
                                    <div class="sub">{{ $p->jumlah_peserta }} peserta</div>
                                </td>
                                <td>
                                    <div class="nm">{{ Str::limit($p->jadwal->paket->nama_paket, 28) }}</div>
                                    <div class="sub">{{ $p->jadwal->paket->tipe_trip }}</div>
                                </td>
                                <td>Rp {{ number_format($p->total_bayar, 0, ',', '.') }}</td>
                                <td>
                                    <div class="sub">BCA Transfer</div>
                                    <div class="sub">{{ $p->pembayaranTerakhir?->tanggal_upload?->translatedFormat('d M H:i') ?? '—' }}</div>
                                </td>
                                <td><a href="{{ route('admin.verifikasi.show', $p) }}" class="btn-adm dark sm">Periksa</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="empty" style="padding:26px">Tidak ada antrean verifikasi. 🎉</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="adm-foot">
                <span>Menampilkan {{ $antrean->count() }} dari {{ $menungguVerifikasi + $perluPerbaikan }} antrean</span>
                <a href="{{ route('admin.verifikasi.index') }}" style="color:var(--adm-orange-d);font-weight:800">Buka seluruh antrean →</a>
            </div>
        </div>

        {{-- Chart --}}
        <div class="adm-card">
            <div class="adm-card-head">
                <div>
                    <h3>Tren Pendapatan &amp; Target Bulanan</h3>
                    <p>Performa pendapatan dari pemesanan terkonfirmasi 6 bulan terakhir.</p>
                </div>
                <div class="chart-legend">
                    <span><i style="background:var(--adm-teal)"></i>Pendapatan</span>
                    <span><i style="background:#dbe3ee"></i>Target</span>
                </div>
            </div>
            <div class="adm-card-body">
                <div class="chart-bars">
                    @foreach($grafik as $g)
                        @php($target = $maks * 0.75)
                        <div class="col">
                            <div class="bars">
                                <div class="b" style="height:{{ max(4, (int) ($g['nilai'] / $maks * 140)) }}px"></div>
                                <div class="b target" style="height:{{ (int) ($target / $maks * 140) }}px"></div>
                            </div>
                            <div class="cap">{{ $g['label'] }}</div>
                        </div>
                    @endforeach
                </div>
                <div class="row between mt-16" style="border-top:1px solid var(--adm-line);padding-top:12px">
                    <div>
                        <div class="lbl" style="font-size:9px;letter-spacing:.1em;text-transform:uppercase;color:var(--adm-muted);font-weight:800">Bulan ini</div>
                        <strong style="font-size:14px">Rp {{ number_format($pendapatanBulanIni, 0, ',', '.') }}</strong>
                    </div>
                    <div>
                        <div style="font-size:9px;letter-spacing:.1em;text-transform:uppercase;color:var(--adm-muted);font-weight:800">Pemesanan</div>
                        <strong style="font-size:14px">{{ $terkonfirmasiBulanIni }}</strong>
                    </div>
                    <div>
                        <div style="font-size:9px;letter-spacing:.1em;text-transform:uppercase;color:var(--adm-muted);font-weight:800">Peserta</div>
                        <strong style="font-size:14px">{{ $totalPesertaBulan }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div style="display:flex;flex-direction:column;gap:16px">
        {{-- Keberangkatan terdekat --}}
        <div class="adm-card">
            <div class="adm-card-head">
                <div>
                    <h3>Keberangkatan Terdekat</h3>
                    <p>Jadwal dan sisa kuota terbaru.</p>
                </div>
            </div>
            <div class="adm-card-body" style="padding:0">
                @forelse($keberangkatanTerdekat as $j)
                    @php($terisi = $j->kuota_peserta - $j->sisa_kuota)
                    <div style="padding:12px 18px;border-bottom:1px solid #f1f3f7">
                        <div class="row between" style="gap:8px;align-items:flex-start">
                            <div>
                                <div class="nm" style="font-size:11.5px">{{ Str::limit($j->paket->nama_paket, 30) }}</div>
                                <div class="sub">{{ $j->tanggal_berangkat->translatedFormat('d M') }} – {{ $j->tanggal_kembali?->translatedFormat('d M Y') }}</div>
                            </div>
                            <span class="pill {{ $j->sisa_kuota <= 2 ? 'warn' : 'ok' }}">{{ $j->sisa_kuota }} kursi</span>
                        </div>
                        <div class="bar mt-8"><i style="width:{{ $j->kuota_peserta ? (int) ($terisi / $j->kuota_peserta * 100) : 0 }}%"></i></div>
                        <div class="sub mt-8">{{ $terisi }}/{{ $j->kuota_peserta }} kursi terisi</div>
                    </div>
                @empty
                    <div class="empty" style="padding:24px">Belum ada jadwal mendatang.</div>
                @endforelse
            </div>
        </div>

        {{-- Aksi cepat --}}
        <div class="adm-card adm-dark" style="padding:18px">
            <h4 style="margin:0 0 6px;font-size:13.5px">Aksi Cepat Concierge</h4>
            <p style="font-size:11px;margin:0 0 14px">Akses cepat untuk operasional harian dan pelayanan pelanggan.</p>
            <div style="display:grid;gap:8px">
                <a href="{{ route('admin.paket.create') }}" class="btn-adm orange" style="justify-content:flex-start">
                    @include('partials.icon', ['name' => 'plus', 'size' => 13]) Tambah Paket Wisata Baru
                </a>
                <a href="{{ route('admin.manifes.index') }}" class="btn-adm ghost" style="justify-content:flex-start;background:rgba(255,255,255,.08);border-color:rgba(255,255,255,.16);color:#fff">
                    @include('partials.icon', ['name' => 'users', 'size' => 13]) Input Manifest
                </a>
                <a href="{{ route('admin.laporan.pdf', ['bulan' => now()->month, 'tahun' => now()->year]) }}" class="btn-adm ghost" style="justify-content:flex-start;background:rgba(255,255,255,.08);border-color:rgba(255,255,255,.16);color:#fff">
                    @include('partials.icon', ['name' => 'download', 'size' => 13]) Unduh Rekap Hari Ini
                </a>
            </div>
            <div style="font-size:9.5px;color:rgba(255,255,255,.45);margin-top:12px">Respon cepat &lt; 5 menit</div>
        </div>
    </div>
</div>
@endsection
