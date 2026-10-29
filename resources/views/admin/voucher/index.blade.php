@extends('layouts.admin')

@section('title', 'Validasi Voucher')

@php
    $tiketHariIni = \App\Models\ETicket::whereDate('tanggal_terbit', today())->count();
    $terakhir = \App\Models\ETicket::with(['pemesanan.user', 'pemesanan.jadwal.paket'])->latest('tanggal_terbit')->limit(8)->get();
@endphp

@section('content')
<div class="adm-head">
    <div>
        <div class="crumb">Admin Portal › Operasional › Validasi Voucher</div>
        <h1>Validasi Voucher &amp; E-Tiket</h1>
        <p>Periksa keaslian voucher dengan memindai QR Code atau memasukkan kode pemesanan.</p>
    </div>
    <div class="actions">
        <span class="pill info">Operator: {{ auth()->user()->nama_lengkap }}</span>
        <span class="pill gray">Shift: {{ now()->format('H:i') }} WIB</span>
    </div>
</div>

<div class="adm-kpi">
    <div class="kpi">
        <div class="lbl">Voucher Terbit Hari Ini</div>
        <div class="val">{{ $tiketHariIni }} <span style="font-size:11px;color:var(--adm-muted)">Tiket</span></div>
        <div class="sub">Diterbitkan otomatis setelah verifikasi</div>
    </div>
    <div class="kpi">
        <div class="lbl">Total Voucher</div>
        <div class="val">{{ \App\Models\ETicket::count() }}</div>
        <div class="sub">Seluruh pemesanan terkonfirmasi</div>
    </div>
    <div class="kpi">
        <div class="lbl">Status Sistem</div>
        <div class="val" style="font-size:15px;color:#15803d">Online</div>
        <div class="sub">Siap menerima validasi</div>
    </div>
    <div class="kpi dark">
        <div class="lbl">Mode Validasi</div>
        <div class="val" style="font-size:14px">Kode / QR</div>
        <div class="sub">Input manual atau pindai</div>
    </div>
</div>

<div class="adm-grid">
    <div class="adm-card">
        <div class="adm-card-head"><div><h3>Validasi Manual</h3><p>Masukkan kode pemesanan atau kode QR voucher.</p></div></div>
        <div class="adm-card-body">
            <form method="POST" action="{{ route('admin.voucher.validasi') }}">
                @csrf
                <div class="field">
                    <label for="kode">Kode Pemesanan / Kode Voucher</label>
                    <input id="kode" type="text" name="kode" value="{{ $kode }}" placeholder="TRV-261029-XXXXX" autofocus required>
                </div>
                <button class="btn-adm orange mt-16" style="width:100%;justify-content:center" type="submit">
                    @include('partials.icon', ['name' => 'check-circle', 'size' => 13]) Validasi Tiket Sekarang
                </button>
                <div class="sub mt-8" style="text-align:center">Validasi bersifat pemeriksaan — tidak mengubah status pemesanan.</div>
            </form>

            <div class="mt-16">
                <div style="font-size:9px;letter-spacing:.1em;text-transform:uppercase;color:var(--adm-muted);font-weight:800;margin-bottom:8px">Pilihan Cepat</div>
                <div class="row wrap" style="gap:6px">
                    @foreach($terakhir->take(4) as $t)
                        <a href="{{ route('admin.voucher.index') }}?kode={{ $t->pemesanan->kode_pemesanan }}"
                           class="btn-adm ghost sm" style="font-family:ui-monospace,monospace">{{ $t->pemesanan->kode_pemesanan }}</a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div>
        @if($hasil === null)
            <div class="adm-card adm-card-body">
                <div class="empty" style="padding:30px">
                    <div style="font-size:30px;margin-bottom:8px">@include('partials.icon', ['name' => 'ticket', 'size' => 30])</div>
                    <h3 style="font-size:13px">Belum ada validasi</h3>
                    <p class="sub mb-0">Hasil pemeriksaan voucher akan tampil di sini.</p>
                </div>
            </div>
        @elseif($hasil['valid'])
            <div class="adm-card" style="border-color:#bbf7d0">
                <div class="adm-card-head" style="background:#f0fdf4;border-color:#bbf7d0">
                    <div class="row" style="gap:8px">
                        <span class="pill ok">✓ TIKET VALID</span>
                        <span class="sub">Diverifikasi {{ now()->format('H:i:s') }}</span>
                    </div>
                </div>
                <div class="adm-card-body">
                    <div class="sub">Kode Booking</div>
                    <div class="nm" style="font-family:ui-monospace,monospace">{{ $hasil['pemesanan']->kode_pemesanan }}</div>
                    <div class="sub mt-8">Nama Pemesan</div>
                    <div class="nm">{{ $hasil['pemesanan']->user->nama_lengkap }}</div>
                    <div class="row mt-8" style="gap:6px">
                        <span class="pill info">{{ $hasil['pemesanan']->jumlah_peserta }} pax</span>
                        <span class="pill ok">Terkonfirmasi</span>
                    </div>

                    <div style="margin-top:14px;background:#f7f8fa;border-radius:9px;padding:12px">
                        <div style="font-size:9px;letter-spacing:.1em;text-transform:uppercase;color:var(--adm-muted);font-weight:800;margin-bottom:6px">Armada &amp; Jadwal</div>
                        <div class="nm" style="font-size:12px">{{ $hasil['pemesanan']->jadwal->paket->nama_paket }}</div>
                        <div class="sub">{{ $hasil['pemesanan']->jadwal->paket->destinasi->nama_destinasi }}</div>
                        <div class="sub">{{ $hasil['pemesanan']->jadwal->tanggal_berangkat->translatedFormat('d F Y') }}</div>
                    </div>

                    <div class="row mt-16" style="gap:8px">
                        <a href="{{ route('pemesanan.voucher.unduh', $hasil['pemesanan']) }}" class="btn-adm ghost" style="flex:1;justify-content:center">Cetak Voucher</a>
                        <a href="{{ route('admin.verifikasi.show', $hasil['pemesanan']) }}" class="btn-adm teal" style="flex:1;justify-content:center">Detail Pesanan</a>
                    </div>
                </div>
            </div>
        @else
            <div class="adm-card" style="border-color:#fecaca">
                <div class="adm-card-head" style="background:#fef2f2;border-color:#fecaca">
                    <div class="row" style="gap:8px">
                        <span class="pill err">✕ VALIDASI DITOLAK</span>
                    </div>
                </div>
                <div class="adm-card-body">
                    <div class="sub">Kode yang dicari</div>
                    <div class="nm" style="font-family:ui-monospace,monospace">{{ $kode }}</div>
                    <div class="mt-8" style="background:#fef2f2;border:1px solid #fecaca;border-radius:9px;padding:12px">
                        <div class="nm" style="color:#b91c1c;font-size:12px">{{ $hasil['alasan'] }}</div>
                        <ul class="sub" style="margin:6px 0 0;padding-left:16px;color:#b91c1c">
                            <li>Pastikan kode diketik lengkap tanpa spasi.</li>
                            <li>Voucher hanya sah bila pemesanan berstatus terkonfirmasi.</li>
                        </ul>
                    </div>
                    @if($hasil['pemesanan'])
                        <div class="row mt-16" style="gap:8px">
                            <span class="pill {{ $hasil['pemesanan']->badgeStatus() === 'success' ? 'ok' : 'warn' }}">{{ $hasil['pemesanan']->labelStatus() }}</span>
                            <a href="{{ route('admin.verifikasi.show', $hasil['pemesanan']) }}" class="btn-adm ghost sm">Buka Pemesanan</a>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

<div class="adm-card" style="margin-top:16px">
    <div class="adm-card-head">
        <div><h3>Aktivitas Voucher Terbaru</h3><p>Log penerbitan voucher terakhir.</p></div>
        <span class="pill gray">{{ $terakhir->count() }} entri</span>
    </div>
    <div style="overflow-x:auto">
        <table class="adm-table">
            <thead>
                <tr><th>Waktu</th><th>Kode Booking</th><th>Nama Pemesan</th><th>Paket</th><th>Peserta</th><th>Status</th></tr>
            </thead>
            <tbody>
                @forelse($terakhir as $t)
                    <tr>
                        <td>{{ $t->tanggal_terbit->translatedFormat('d M • H:i') }}</td>
                        <td style="font-family:ui-monospace,monospace;font-size:10.5px">{{ $t->pemesanan->kode_pemesanan }}</td>
                        <td>{{ $t->pemesanan->user->nama_lengkap }}</td>
                        <td class="sub">{{ Str::limit($t->pemesanan->jadwal->paket->nama_paket, 26) }}</td>
                        <td>{{ $t->pemesanan->jumlah_peserta }}</td>
                        <td><span class="pill ok">VALID</span></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty" style="padding:24px">Belum ada voucher terbit.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
