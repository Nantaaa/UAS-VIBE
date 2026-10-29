@extends('layouts.admin')

@section('title', 'Periksa Pemesanan')
@section('heading', 'Periksa Pemesanan ' . $pemesanan->kode_pemesanan)

@section('content')
<div class="row between mb-16">
    <a href="{{ route('admin.verifikasi.index') }}" class="btn btn-ghost btn-sm">← Kembali ke daftar</a>
    <span class="badge badge-{{ $pemesanan->badgeStatus() }}">{{ $pemesanan->labelStatus() }}</span>
</div>

<div class="grid" style="grid-template-columns: 1.5fr 1fr; align-items:start">
    <div class="stack">
        <div class="card">
            <div class="card-head"><h3>Data pemesanan</h3></div>
            <div class="card-body">
                <dl class="kv">
                    <dt>Wisatawan</dt><dd>{{ $pemesanan->user->nama_lengkap }}</dd>
                    <dt>Email / HP</dt><dd>{{ $pemesanan->user->email }} · {{ $pemesanan->user->no_hp }}</dd>
                    <dt>Paket</dt><dd>{{ $pemesanan->jadwal->paket->nama_paket }}</dd>
                    <dt>Destinasi</dt><dd>{{ $pemesanan->jadwal->paket->destinasi->nama_destinasi }}</dd>
                    <dt>Berangkat</dt><dd>{{ $pemesanan->jadwal->tanggal_berangkat->translatedFormat('d F Y') }}</dd>
                    <dt>Jumlah peserta</dt><dd>{{ $pemesanan->jumlah_peserta }} orang</dd>
                    <dt>Total bayar</dt><dd style="color:var(--primary-dark)">Rp {{ number_format($pemesanan->total_bayar, 0, ',', '.') }}</dd>
                    <dt>Dibuat</dt><dd>{{ $pemesanan->tanggal_pemesanan->translatedFormat('d M Y H:i') }}</dd>
                </dl>
            </div>
        </div>

        <div class="card">
            <div class="card-head"><h3>Dokumen identitas peserta</h3></div>
            <div class="table-wrap">
                <table class="data">
                    <thead><tr><th>Nama</th><th>Jenis</th><th>Nomor</th><th>Berkas</th><th>Status</th></tr></thead>
                    <tbody>
                        @foreach($pemesanan->peserta as $ps)
                            <tr>
                                <td><strong>{{ $ps->nama_peserta }}</strong></td>
                                <td>{{ $ps->jenis_identitas }}</td>
                                <td>{{ $ps->no_identitas }}</td>
                                <td>
                                    @if($ps->file_identitas)
                                        <a href="{{ route('pemesanan.berkas', [$pemesanan, 'identitas', $ps->id]) }}"
                                           target="_blank" class="btn btn-ghost btn-sm">Buka berkas</a>
                                    @else
                                        <span class="muted tiny">Tidak ada</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-{{ $ps->status === 'valid' ? 'success' : ($ps->status === 'perlu_perbaikan' ? 'danger' : 'warning') }}">
                                        {{ ucfirst(str_replace('_', ' ', $ps->status)) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-head"><h3>Bukti pembayaran</h3></div>
            <div class="card-body">
                @forelse($pemesanan->pembayaran as $bayar)
                    <div class="row between" style="padding:10px 0;border-bottom:1px solid #f0f2f6">
                        <div>
                            <div style="font-weight:700;font-size:13.5px">Rp {{ number_format($bayar->jumlah_bayar, 0, ',', '.') }}</div>
                            <div class="tiny muted">{{ $bayar->tanggal_upload->translatedFormat('d M Y H:i') }}</div>
                        </div>
                        <div class="row" style="gap:8px">
                            <span class="badge badge-{{ $bayar->status_verifikasi === 'valid' ? 'success' : ($bayar->status_verifikasi === 'tidak_valid' ? 'danger' : 'warning') }}">
                                {{ ucfirst(str_replace('_', ' ', $bayar->status_verifikasi)) }}
                            </span>
                            <a href="{{ route('pemesanan.berkas', [$pemesanan, 'pembayaran', $bayar->id]) }}"
                               target="_blank" class="btn btn-ghost btn-sm">Lihat bukti</a>
                        </div>
                    </div>
                @empty
                    <div class="empty">Belum ada bukti pembayaran.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="stack" style="position:sticky;top:88px">
        <div class="card card-pad">
            <h3 style="font-size:14px">Konfirmasi pemesanan</h3>
            <p class="tiny muted">Pastikan dokumen identitas dan bukti transfer sesuai sebelum konfirmasi. Voucher akan terbit otomatis.</p>
            <form method="POST" action="{{ route('admin.verifikasi.setujui', $pemesanan) }}"
                  onsubmit="return confirm('Konfirmasi pemesanan dan terbitkan voucher?')">
                @csrf
                <button class="btn btn-primary btn-block" type="submit"
                        @disabled($pemesanan->status_pemesanan !== 'menunggu_verifikasi')>
                    ✓ Konfirmasi &amp; terbitkan voucher
                </button>
            </form>
        </div>

        <div class="card card-pad">
            <h3 style="font-size:14px">Kembalikan untuk perbaikan</h3>
            <form method="POST" action="{{ route('admin.verifikasi.tolak', $pemesanan) }}">
                @csrf
                <div class="field">
                    <label for="alasan">Alasan perbaikan</label>
                    <textarea id="alasan" name="alasan" placeholder="Contoh: Bukti transfer buram, mohon unggah ulang." required></textarea>
                </div>
                <button class="btn btn-danger-soft btn-block mt-16" type="submit">Kembalikan ke wisatawan</button>
            </form>
        </div>

        @if($pemesanan->catatan_status)
            <div class="card card-pad">
                <h3 style="font-size:14px">Catatan terakhir</h3>
                <p class="tiny muted mb-0">{{ $pemesanan->catatan_status }}</p>
            </div>
        @endif
    </div>
</div>
@endsection
