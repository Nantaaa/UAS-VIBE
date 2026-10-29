@extends('layouts.admin')

@section('title', 'Dashboard')
@section('heading', 'Dashboard')

@section('content')
<div class="grid grid-4">
    <div class="card stat">
        <div class="ico">✓</div>
        <div>
            <div class="val">{{ $menungguVerifikasi }}</div>
            <div class="lbl">Menunggu verifikasi</div>
        </div>
    </div>
    <div class="card stat">
        <div class="ico red">!</div>
        <div>
            <div class="val">{{ $perluPerbaikan }}</div>
            <div class="lbl">Perlu perbaikan</div>
        </div>
    </div>
    <div class="card stat">
        <div class="ico green">▤</div>
        <div>
            <div class="val">{{ $terkonfirmasiBulanIni }}</div>
            <div class="lbl">Terkonfirmasi bulan ini</div>
        </div>
    </div>
    <div class="card stat">
        <div class="ico orange">Rp</div>
        <div>
            <div class="val" style="font-size:18px">Rp {{ number_format($pendapatanBulanIni, 0, ',', '.') }}</div>
            <div class="lbl">Pendapatan bulan ini</div>
        </div>
    </div>
</div>

<div class="grid mt-24" style="grid-template-columns: 1.5fr 1fr; align-items:start">
    <div class="card">
        <div class="card-head">
            <h3>Pendapatan 6 bulan terakhir</h3>
            <span class="badge badge-info">Terkonfirmasi</span>
        </div>
        <div class="card-body">
            @php($maks = max(1, collect($grafik)->max('nilai')))
            <div class="row" style="align-items:flex-end;gap:14px;height:190px">
                @foreach($grafik as $g)
                    <div class="grow center">
                        <div style="height:{{ max(6, (int) ($g['nilai'] / $maks * 150)) }}px;background:linear-gradient(180deg,#4ea1ff,#1b6ff3);border-radius:8px 8px 4px 4px"
                             title="Rp {{ number_format($g['nilai'], 0, ',', '.') }}"></div>
                        <div class="tiny muted mt-8">{{ $g['label'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-head">
            <h3>Keberangkatan terdekat</h3>
            <a href="{{ route('admin.jadwal.index') }}" class="btn btn-ghost btn-sm">Semua</a>
        </div>
        <div class="card-body" style="padding:0">
            @forelse($keberangkatanTerdekat as $j)
                <div style="padding:14px 22px;border-bottom:1px solid #f0f2f6" class="row between">
                    <div>
                        <div style="font-weight:700;font-size:13.5px">{{ $j->paket->nama_paket }}</div>
                        <div class="tiny muted">{{ $j->paket->destinasi->nama_destinasi }}</div>
                    </div>
                    <div class="right">
                        <div class="tiny" style="font-weight:700">{{ $j->tanggal_berangkat->translatedFormat('d M') }}</div>
                        <div class="tiny muted">{{ $j->sisa_kuota }} kursi</div>
                    </div>
                </div>
            @empty
                <div class="empty">Belum ada jadwal.</div>
            @endforelse
        </div>
    </div>
</div>

<div class="card mt-24">
    <div class="card-head">
        <h3>Pemesanan terbaru</h3>
        <a href="{{ route('admin.verifikasi.index') }}" class="btn btn-ghost btn-sm">Verifikasi</a>
    </div>
    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr><th>Kode</th><th>Wisatawan</th><th>Paket</th><th>Peserta</th><th>Total</th><th>Status</th><th></th></tr>
            </thead>
            <tbody>
                @forelse($pemesananTerbaru as $p)
                    <tr>
                        <td><strong>{{ $p->kode_pemesanan }}</strong></td>
                        <td>{{ $p->user->nama_lengkap }}</td>
                        <td>{{ Str::limit($p->jadwal->paket->nama_paket, 28) }}</td>
                        <td>{{ $p->jumlah_peserta }}</td>
                        <td>Rp {{ number_format($p->total_bayar, 0, ',', '.') }}</td>
                        <td><span class="badge badge-{{ $p->badgeStatus() }}">{{ $p->labelStatus() }}</span></td>
                        <td><a href="{{ route('admin.verifikasi.show', $p) }}" class="btn btn-ghost btn-sm">Buka</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty">Belum ada pemesanan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
