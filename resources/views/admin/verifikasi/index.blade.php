@extends('layouts.admin')

@section('title', 'Verifikasi Pemesanan')
@section('heading', 'Verifikasi Pemesanan & Pembayaran')

@section('content')
<div class="tabs mb-16" style="display:inline-flex">
    <a href="{{ route('admin.verifikasi.index') }}" class="{{ $status === 'menunggu_verifikasi' ? 'active' : '' }}">
        Menunggu Verifikasi ({{ $jumlahMenunggu }})
    </a>
    <a href="{{ route('admin.verifikasi.index', ['status' => 'perlu_perbaikan']) }}" class="{{ $status === 'perlu_perbaikan' ? 'active' : '' }}">
        Perlu Perbaikan ({{ $jumlahPerbaikan }})
    </a>
</div>

<div class="card">
    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr><th>Kode</th><th>Wisatawan</th><th>Paket</th><th>Berangkat</th><th>Peserta</th><th>Total</th><th>Diunggah</th><th></th></tr>
            </thead>
            <tbody>
                @forelse($daftar as $p)
                    <tr>
                        <td><strong>{{ $p->kode_pemesanan }}</strong></td>
                        <td>{{ $p->user->nama_lengkap }}<div class="tiny muted">{{ $p->user->no_hp }}</div></td>
                        <td>{{ Str::limit($p->jadwal->paket->nama_paket, 26) }}</td>
                        <td>{{ $p->jadwal->tanggal_berangkat->translatedFormat('d M Y') }}</td>
                        <td>{{ $p->jumlah_peserta }}</td>
                        <td>Rp {{ number_format($p->total_bayar, 0, ',', '.') }}</td>
                        <td class="tiny muted">{{ $p->pembayaranTerakhir?->tanggal_upload?->translatedFormat('d M H:i') ?? '-' }}</td>
                        <td><a href="{{ route('admin.verifikasi.show', $p) }}" class="btn btn-primary btn-sm">Periksa</a></td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="empty">Tidak ada pemesanan pada tab ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="pagination">
    @foreach($daftar->getUrlRange(1, $daftar->lastPage()) as $page => $url)
        <a href="{{ $url }}" class="{{ $page == $daftar->currentPage() ? 'active' : '' }}">{{ $page }}</a>
    @endforeach
</div>
@endsection
