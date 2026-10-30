@extends('layouts.admin')

@section('title', 'Periksa Pemesanan')

@section('content')
<div class="adm-head">
    <div>
        <div class="crumb">Admin Portal › Verifikasi › {{ $pemesanan->kode_pemesanan }}</div>
        <h1>Verifikasi Pemesanan</h1>
        <p>Periksa dokumen identitas dan bukti pembayaran sebelum konfirmasi. Voucher terbit otomatis setelah disetujui.</p>
    </div>
    <div class="actions">
        <a href="{{ route('admin.verifikasi.index') }}" class="btn-adm ghost">← Kembali ke antrean</a>
        <span class="pill {{ $pemesanan->status_pemesanan === 'perlu_perbaikan' ? 'err' : ($pemesanan->status_pemesanan === 'terkonfirmasi' ? 'ok' : 'warn') }}">{{ $pemesanan->labelStatus() }}</span>
    </div>
</div>

<div class="adm-grid">
    <div style="display:flex;flex-direction:column;gap:16px">
        {{-- Detail booking --}}
        <div class="adm-card">
            <div class="adm-card-head">
                <div>
                    <h3>Kode Booking</h3>
                    <p>{{ $pemesanan->tanggal_pemesanan->translatedFormat('d M Y • H:i') }}</p>
                </div>
                <strong style="font-family:ui-monospace,monospace;font-size:13px">{{ $pemesanan->kode_pemesanan }}</strong>
            </div>
            <div class="adm-card-body">
                <div style="font-size:9px;letter-spacing:.1em;text-transform:uppercase;color:var(--adm-muted);font-weight:800;margin-bottom:8px">Informasi Keberangkatan</div>
                <div class="nm" style="font-size:13px">{{ $pemesanan->jadwal->paket->nama_paket }}</div>
                <div class="sub mt-8">{{ $pemesanan->jadwal->tanggal_berangkat->translatedFormat('d M Y') }} – {{ $pemesanan->jadwal->tanggal_kembali?->translatedFormat('d M Y') }}</div>
                <div class="sub">Destinasi: {{ $pemesanan->jadwal->paket->destinasi->nama_destinasi }} · {{ $pemesanan->jumlah_peserta }} peserta</div>

                <div class="form-grid mt-16">
                    <div>
                        <div style="font-size:9px;letter-spacing:.1em;text-transform:uppercase;color:var(--adm-muted);font-weight:800;margin-bottom:6px">Pemesan Utama</div>
                        <div class="nm">{{ $pemesanan->user->nama_lengkap }}</div>
                        <div class="sub">{{ $pemesanan->user->no_hp }}</div>
                        <div class="sub">{{ $pemesanan->user->email }}</div>
                    </div>
                    <div>
                        <div style="font-size:9px;letter-spacing:.1em;text-transform:uppercase;color:var(--adm-muted);font-weight:800;margin-bottom:6px">Catatan Status</div>
                        <div class="sub">{{ $pemesanan->catatan_status ?: '—' }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Peserta --}}
        <div class="adm-card">
            <div class="adm-card-head">
                <div><h3>Daftar Peserta &amp; Dokumen Identitas</h3><p>{{ $pemesanan->peserta->count() }} peserta</p></div>
                <span class="pill info">{{ $pemesanan->peserta->where('status','valid')->count() }}/{{ $pemesanan->peserta->count() }} dokumen valid</span>
            </div>
            <div class="adm-card-body" style="display:grid;gap:12px">
                @foreach($pemesanan->peserta as $ps)
                    <div class="row between wrap" style="gap:12px;border:1px solid var(--adm-line);border-radius:10px;padding:12px">
                        <div class="row" style="gap:10px">
                            <span class="adm-user"><span class="ava">{{ strtoupper(substr($ps->nama_peserta,0,2)) }}</span></span>
                            <div>
                                <div class="nm">{{ $ps->nama_peserta }}</div>
                                <div class="sub">{{ $ps->jenis_identitas }}: {{ $ps->no_identitas }}</div>
                                <div class="sub">{{ $ps->kota ?: '—' }} · {{ $ps->tanggal_lahir?->translatedFormat('d M Y') ?: '—' }}</div>
                            </div>
                        </div>
                        <div class="row" style="gap:8px">
                            <span class="pill {{ $ps->status === 'valid' ? 'ok' : ($ps->status === 'perlu_perbaikan' ? 'err' : 'warn') }}">{{ ucfirst(str_replace('_',' ',$ps->status)) }}</span>
                            @if($ps->file_identitas)
                                <a href="{{ route('pemesanan.berkas', [$pemesanan, 'identitas', $ps->id]) }}" target="_blank" class="btn-adm ghost sm">Lihat Dokumen</a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Bukti bayar --}}
        <div class="adm-card">
            <div class="adm-card-head"><div><h3>Bukti Pembayaran</h3><p>Riwayat unggahan bukti transfer.</p></div></div>
            <div class="adm-card-body">
                @forelse($pemesanan->pembayaran as $bayar)
                    <div class="row between" style="padding:10px 0;border-bottom:1px solid #f1f3f7">
                        <div>
                            <div class="nm">Rp {{ number_format($bayar->jumlah_bayar, 0, ',', '.') }}</div>
                            <div class="sub">{{ $bayar->tanggal_upload->translatedFormat('d M Y • H:i') }}</div>
                        </div>
                        <div class="row" style="gap:8px">
                            <span class="pill {{ $bayar->status_verifikasi === 'valid' ? 'ok' : ($bayar->status_verifikasi === 'tidak_valid' ? 'err' : 'warn') }}">{{ ucfirst(str_replace('_',' ',$bayar->status_verifikasi)) }}</span>
                            <a href="{{ route('pemesanan.berkas', [$pemesanan, 'pembayaran', $bayar->id]) }}" target="_blank" class="btn-adm ghost sm">Perbesar Bukti</a>
                        </div>
                    </div>
                @empty
                    <div class="empty" style="padding:22px">Belum ada bukti pembayaran.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Kanan --}}
    <div style="display:flex;flex-direction:column;gap:16px">
        <div class="adm-card">
            <div class="adm-card-head"><div><h3>Ringkasan Tagihan</h3><p>Mata uang IDR</p></div></div>
            <div class="adm-card-body">
                @php($rincian = $pemesanan->rincianBiaya())
                <div class="row between" style="font-size:11.5px;padding:4px 0"><span class="sub">Dewasa {{ $rincian['dewasa'] }} × Rp {{ number_format($rincian['tarif'], 0, ',', '.') }}</span><span>Rp {{ number_format($rincian['dewasa'] * $rincian['tarif'], 0, ',', '.') }}</span></div>
                @if($rincian['anak'] > 0)
                    <div class="row between" style="font-size:11.5px;padding:4px 0"><span class="sub">Anak {{ $rincian['anak'] }} × Rp {{ number_format($rincian['tarif_anak'], 0, ',', '.') }}</span><span>Rp {{ number_format($rincian['anak'] * $rincian['tarif_anak'], 0, ',', '.') }}</span></div>
                @endif
                @if($pemesanan->diskon > 0)
                    <div class="row between" style="font-size:11.5px;padding:4px 0"><span class="sub">Diskon promo{{ $pemesanan->kode_promo ? ' ('.$pemesanan->kode_promo.')' : '' }}</span><span style="color:#15803d">-Rp {{ number_format($pemesanan->diskon, 0, ',', '.') }}</span></div>
                @endif
                <div class="row between" style="font-size:11.5px;padding:4px 0"><span class="sub">Biaya administrasi</span><span>Rp 0</span></div>
                <div class="row between" style="border-top:1px solid var(--adm-line);margin-top:8px;padding-top:10px">
                    <strong style="font-size:10px;letter-spacing:.08em;text-transform:uppercase;color:var(--adm-muted)">Total Pembayaran</strong>
                    <strong style="font-size:15px;color:var(--adm-blue)">Rp {{ number_format($pemesanan->total_bayar, 0, ',', '.') }}</strong>
                </div>

                <div style="margin-top:14px;background:#f7f8fa;border-radius:9px;padding:12px">
                    <div style="font-size:9px;letter-spacing:.1em;text-transform:uppercase;color:var(--adm-muted);font-weight:800;margin-bottom:6px">Audit Trail</div>
                    <div class="sub">Bank / Tujuan: BCA Virtual Account</div>
                    <div class="sub">No. VA: 8808 0812 3456 7890</div>
                    <div class="sub">Nama: PT Nanta Ekspedisi Nusantara</div>
                    <div class="sub">Transfer: {{ $pemesanan->pembayaranTerakhir?->tanggal_upload?->translatedFormat('d M Y • H:i:s') ?? '—' }}</div>
                </div>
            </div>
        </div>

        <div class="adm-card adm-card-body">
            <h3 style="font-size:12.5px;margin:0 0 8px">Keputusan Verifikasi</h3>
            <form method="POST" action="{{ route('admin.verifikasi.setujui', $pemesanan) }}" onsubmit="return confirm('Konfirmasi pemesanan dan terbitkan voucher?')">
                @csrf
                <button class="btn-adm teal" style="width:100%;justify-content:center" @disabled($pemesanan->status_pemesanan !== 'menunggu_verifikasi')>
                    @include('partials.icon', ['name' => 'check', 'size' => 13]) Setujui &amp; Terbitkan E-Tiket
                </button>
            </form>

            <form method="POST" action="{{ route('admin.verifikasi.tolak', $pemesanan) }}" class="mt-8">
                @csrf
                <div class="field">
                    <label for="alasan">Alasan penolakan / perbaikan</label>
                    <textarea id="alasan" name="alasan" rows="2" placeholder="Contoh: Bukti transfer buram, mohon unggah ulang." required></textarea>
                </div>
                <button class="btn-adm red mt-8" style="width:100%;justify-content:center" type="submit">Tolak / Minta Bukti Ulang</button>
            </form>

            @if($pemesanan->status_pemesanan === 'terkonfirmasi')
                <a href="{{ route('pemesanan.voucher', $pemesanan) }}" class="btn-adm ghost mt-8" style="width:100%;justify-content:center">Lihat E-Voucher</a>
            @endif
        </div>
    </div>
</div>
@endsection
