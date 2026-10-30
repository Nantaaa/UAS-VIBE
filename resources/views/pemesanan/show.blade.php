@extends('layouts.app')

@section('title', 'Detail Pemesanan')

@section('content')
<div class="container" style="padding:28px 20px 0">
    <div class="tiny muted mb-16">
        <a href="{{ route('pemesanan.index') }}">Pemesanan Saya</a> › <span>{{ $pemesanan->kode_pemesanan }}</span>
    </div>

    <div class="row between wrap mb-16" style="gap:12px">
        <div>
            <h1 style="font-size:24px;margin-bottom:4px">{{ $pemesanan->jadwal->paket->nama_paket }}</h1>
            <div class="muted tiny">
                Kode <strong>{{ $pemesanan->kode_pemesanan }}</strong> ·
                dibuat {{ $pemesanan->tanggal_pemesanan->translatedFormat('d M Y H:i') }}
            </div>
        </div>
        <span class="badge badge-{{ $pemesanan->badgeStatus() }}" style="font-size:12.5px;padding:7px 14px">
            {{ $pemesanan->labelStatus() }}
        </span>
    </div>

    @if($pemesanan->status_pemesanan === 'menunggu_pembayaran' && $pemesanan->sisaDetikPembayaran() > 0)
        <div class="alert alert-info">
            ⏱ Batas pembayaran: <strong>{{ $pemesanan->batas_pembayaran->translatedFormat('d M Y H:i') }}</strong> —
            sisa <span id="countdown" class="countdown">--:--:--</span>
        </div>
    @endif

    @if($pemesanan->status_pemesanan === 'perlu_perbaikan')
        <div class="alert alert-error">
            {!! view('partials.icon', ['name' => 'edit', 'size' => 15])->render() !!} <div><strong>Perlu perbaikan:</strong> {{ $pemesanan->catatan_status }}<br>
            <span class="tiny">Unggah ulang berkas yang benar di bawah, lalu admin akan memeriksa kembali.</span></div>
        </div>
    @endif

    <div class="grid" style="grid-template-columns: 1.6fr 1fr; align-items:start">
        <div class="stack">
            <div class="card">
                <div class="card-head"><h3>Informasi perjalanan</h3></div>
                <div class="card-body">
                    <dl class="kv">
                        <dt>Destinasi</dt><dd>{{ $pemesanan->jadwal->paket->destinasi->nama_destinasi }}</dd>
                        <dt>Lokasi</dt><dd>{{ $pemesanan->jadwal->paket->destinasi->lokasi }}</dd>
                        <dt>Tanggal berangkat</dt><dd>{{ $pemesanan->jadwal->tanggal_berangkat->translatedFormat('d F Y') }}</dd>
                        <dt>Tanggal kembali</dt><dd>{{ $pemesanan->jadwal->tanggal_kembali?->translatedFormat('d F Y') ?? '-' }}</dd>
                        <dt>Titik kumpul</dt>
                        <dd>
                            {{ $pemesanan->jadwal->kota_kumpul ?? 'Dikonfirmasi admin' }}
                            @if($pemesanan->jadwal->jam_kumpul)
                                <span class="tiny muted">· kumpul {{ $pemesanan->jadwal->jam_kumpul->format('H:i') }}</span>
                            @endif
                            @if($pemesanan->jadwal->titik_kumpul)
                                <div class="tiny muted">{{ $pemesanan->jadwal->titik_kumpul }}</div>
                            @endif
                        </dd>
                        <dt>Jumlah peserta</dt>
                        <dd>
                            {{ $pemesanan->jumlah_peserta }} orang
                            <span class="tiny muted">
                                ({{ $pemesanan->jumlah_dewasa ?: $pemesanan->jumlah_peserta }} dewasa{{ $pemesanan->jumlah_anak > 0 ? ' + '.$pemesanan->jumlah_anak.' anak' : '' }})
                            </span>
                        </dd>
                        @if($pemesanan->diskon > 0)
                            <dt>Diskon promo</dt>
                            <dd style="color:#15803d">-Rp {{ number_format($pemesanan->diskon, 0, ',', '.') }}
                                @if($pemesanan->kode_promo)<span class="badge badge-success">{{ $pemesanan->kode_promo }}</span>@endif
                            </dd>
                        @endif
                        <dt>Total bayar</dt><dd style="color:var(--primary-dark)">Rp {{ number_format($pemesanan->total_bayar, 0, ',', '.') }}</dd>
                    </dl>
                </div>
            </div>

            <div class="card">
                <div class="card-head">
                    <h3>Data peserta</h3>
                    <span class="badge badge-secondary">{{ $pemesanan->peserta->count() }} peserta</span>
                </div>
                <div class="table-wrap">
                    <table class="data">
                        <thead>
                            <tr><th>Nama</th><th>Identitas</th><th>Nomor</th><th>Berkas</th><th>Status</th></tr>
                        </thead>
                        <tbody>
                            @foreach($pemesanan->peserta as $ps)
                                <tr>
                                    <td><strong>{{ $ps->nama_peserta }}</strong></td>
                                    <td>{{ $ps->jenis_identitas }}</td>
                                    <td>{{ $ps->no_identitas }}</td>
                                    <td>
                                        @if($ps->file_identitas)
                                            <a href="{{ route('pemesanan.berkas', [$pemesanan, 'identitas', $ps->id]) }}"
                                               class="btn btn-ghost btn-sm" target="_blank">Lihat</a>
                                        @else
                                            <span class="muted tiny">-</span>
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

            @if(in_array($pemesanan->status_pemesanan, ['menunggu_pembayaran', 'perlu_perbaikan']))
                <div class="card">
                    <div class="card-head"><h3>Unggah bukti pembayaran</h3></div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <span style="font-size:15px">{!! view('partials.icon', ['name' => 'info', 'size' => 15])->render() !!}</span>
                            <span>
                                Transfer ke <strong>Bank BCA 1234567890</strong> a.n. <strong>Nanta Travel</strong>
                                sebesar <strong>Rp {{ number_format($pemesanan->total_bayar, 0, ',', '.') }}</strong>
                            </span>
                        </div>

                        <form method="POST" action="{{ route('pemesanan.bayar', $pemesanan) }}" enctype="multipart/form-data">
                            @csrf
                            <label class="dropzone" for="bukti" style="display:block;cursor:pointer">
                                <div class="ico">{!! view('partials.icon', ['name' => 'cloud-up', 'size' => 15])->render() !!}</div>
                                <div class="cap">Tarik berkas ke sini atau klik untuk memilih</div>
                                <div class="sub">Format JPG, PNG, atau PDF — maksimal 2 MB</div>
                            </label>
                            <input id="bukti" type="file" name="bukti" accept=".jpg,.jpeg,.png,.pdf" required style="display:none"
                                   onchange="const c=this.closest('form').querySelector('.dropzone .cap'); c.textContent = this.files[0] ? '{!! view('partials.icon', ['name' => 'paperclip', 'size' => 15])->render() !!} '+this.files[0].name : 'Tarik berkas ke sini atau klik untuk memilih'; c.style.color = this.files[0] ? '#1d4ed8' : '';">
                            <button class="btn btn-primary btn-block mt-16" type="submit">Kirim Bukti Pembayaran</button>
                        </form>
                    </div>
                </div>
            @endif

            @if($pemesanan->pembayaran->isNotEmpty())
                <div class="card">
                    <div class="card-head"><h3>Riwayat pembayaran</h3></div>
                    <div class="table-wrap">
                        <table class="data">
                            <thead><tr><th>Tanggal upload</th><th>Jumlah</th><th>Bukti</th><th>Status</th></tr></thead>
                            <tbody>
                                @foreach($pemesanan->pembayaran as $bayar)
                                    <tr>
                                        <td>{{ $bayar->tanggal_upload->translatedFormat('d M Y H:i') }}</td>
                                        <td>Rp {{ number_format($bayar->jumlah_bayar, 0, ',', '.') }}</td>
                                        <td>
                                            <a href="{{ route('pemesanan.berkas', [$pemesanan, 'pembayaran', $bayar->id]) }}"
                                               class="btn btn-ghost btn-sm" target="_blank">Lihat</a>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $bayar->status_verifikasi === 'valid' ? 'success' : ($bayar->status_verifikasi === 'tidak_valid' ? 'danger' : 'warning') }}">
                                                {{ ucfirst(str_replace('_', ' ', $bayar->status_verifikasi)) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <div class="stack" style="position:sticky;top:88px">
            <div class="card">
                <div class="card-head"><h3>Status pemesanan</h3></div>
                <div class="card-body">
                @php
                    $tahap = [
                        'menunggu_pembayaran' => 'Pemesanan dibuat & kuota ditahan',
                        'menunggu_verifikasi' => 'Bukti pembayaran diunggah',
                        'perlu_perbaikan' => 'Dokumen perlu diperbaiki',
                        'terkonfirmasi' => 'Dokumen & pembayaran valid',
                        'kedaluwarsa' => 'Kedaluwarsa',
                        'dibatalkan' => 'Dibatalkan',
                    ];
                    $urut = array_keys($tahap);
                    $posisi = array_search($pemesanan->status_pemesanan, $urut, true);
                @endphp
                <ul class="timeline">
                    @foreach($tahap as $key => $label)
                        @php($idx = array_search($key, $urut, true))
                        <li class="{{ $idx < $posisi ? 'done' : ($idx === $posisi ? 'current' : '') }}">
                            <span class="dot"></span>
                            <div>
                                <div style="font-weight:700;font-size:13px">{{ $label }}</div>
                                <div class="tiny muted">{{ $key === $pemesanan->status_pemesanan ? 'Status saat ini' : '' }}</div>
                            </div>
                        </li>
                    @endforeach
                </ul>
                </div>
            </div>

            @if($pemesanan->status_pemesanan === 'terkonfirmasi')
                <div class="card">
                    <div class="card-head"><h3>E-Voucher siap</h3></div>
                    <div class="card-body">
                        <p class="tiny muted">Tunjukkan QR Code voucher saat keberangkatan.</p>
                        <a href="{{ route('pemesanan.voucher', $pemesanan) }}" class="btn btn-primary btn-block">Lihat E-Voucher</a>
                        <a href="{{ route('pemesanan.voucher.unduh', $pemesanan) }}" class="btn btn-ghost btn-block mt-8">Unduh PDF</a>
                    </div>
                </div>
            @endif

            @if($pemesanan->bisaDibatalkan())
                <div class="card">
                    <div class="card-head"><h3>Batalkan pemesanan</h3></div>
                    <div class="card-body">
                        <p class="tiny muted">Kuota yang ditahan akan dilepas kembali ke jadwal.</p>
                        <form method="POST" action="{{ route('pemesanan.batal', $pemesanan) }}"
                              onsubmit="return confirm('Batalkan pemesanan ini?')">
                            @csrf
                            <button class="btn btn-danger-soft btn-block" type="submit">Batalkan pemesanan</button>
                        </form>
                    </div>
                </div>
            @endif

            @if($pemesanan->catatan_status)
                <div class="card">
                    <div class="card-head"><h3>Catatan admin</h3></div>
                    <div class="card-body">
                        <p class="tiny muted mb-0">{{ $pemesanan->catatan_status }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@if($pemesanan->status_pemesanan === 'menunggu_pembayaran' && $pemesanan->sisaDetikPembayaran() > 0)
<script>
let sisa = {{ $pemesanan->sisaDetikPembayaran() }};
const el = document.getElementById('countdown');
function tick() {
    if (!el) return;
    const h = String(Math.floor(sisa / 3600)).padStart(2, '0');
    const m = String(Math.floor((sisa % 3600) / 60)).padStart(2, '0');
    const s = String(sisa % 60).padStart(2, '0');
    el.textContent = `${h}:${m}:${s}`;
    if (sisa <= 300) el.classList.add('urgent');
    if (sisa <= 0) { el.textContent = 'Kedaluwarsa'; return; }
    sisa--; setTimeout(tick, 1000);
}
tick();
</script>
@endif
@endsection
