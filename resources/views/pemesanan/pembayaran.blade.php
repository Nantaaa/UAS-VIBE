@extends('layouts.app')

@section('title', 'Pembayaran')

@section('content')
<div class="container" style="padding-top:22px">
    <div class="crumb">
        <span><a href="{{ route('pemesanan.index') }}">Pesanan Saya</a> / <strong>Pembayaran</strong></span>
    </div>

    <div class="stepper-bar">
        <div class="step done"><span class="num">✓</span> Isi Data</div>
        <div class="line"></div>
        <div class="step active"><span class="num">2</span> Pembayaran</div>
        <div class="line"></div>
        <div class="step"><span class="num">3</span> Selesai</div>
    </div>

    @if($pemesanan->status_pemesanan === 'menunggu_pembayaran' && $pemesanan->sisaDetikPembayaran() > 0)
        <div class="deadline">
            <div>
                <div class="ttl">⚠ Selesaikan Pembayaran Anda Sebelum Waktu Berakhir</div>
                <div class="tiny" style="color:#b45309">Batas pembayaran {{ $pemesanan->batas_pembayaran->translatedFormat('d M Y H:i') }} WIB</div>
            </div>
            <div class="clock" id="clock" data-sisa="{{ $pemesanan->sisaDetikPembayaran() }}">
                <span data-h>00</span><i>:</i><span data-m>00</span><i>:</i><span data-s>00</span>
            </div>
        </div>
    @endif

    <div class="grid" style="grid-template-columns: 1fr 372px; align-items:start">
        <div class="stack">
            {{-- Ringkasan pesanan --}}
            <div class="card card-body">
                <div class="row between wrap" style="gap:10px">
                    <div>
                        <div class="tiny muted">Nama pemesan</div>
                        <strong style="font-size:14px">{{ $pemesanan->user->nama_lengkap }}</strong>
                    </div>
                    <a href="{{ route('pemesanan.show', $pemesanan) }}" class="tiny" style="color:#2563eb;font-weight:700">Lihat detail</a>
                </div>

                <div class="row between mt-16" style="align-items:flex-end">
                    <div>
                        <div class="tiny muted">Total pembayaran</div>
                        <div style="font-size:24px;font-weight:800;color:#1d4ed8">Rp {{ number_format($pemesanan->total_bayar, 0, ',', '.') }}</div>
                    </div>
                    <span class="badge badge-{{ $pemesanan->badgeStatus() }}">{{ $pemesanan->labelStatus() }}</span>
                </div>

                <hr style="border:0;border-top:1px solid var(--line);margin:16px 0">
                <div class="tiny" style="font-weight:800;margin-bottom:8px">Rincian Pesanan</div>
                <dl class="kv">
                    <dt>Paket</dt><dd>{{ $pemesanan->jadwal->paket->nama_paket }}</dd>
                    <dt>Destinasi</dt><dd>{{ $pemesanan->jadwal->paket->destinasi->nama_destinasi }}</dd>
                    <dt>Keberangkatan</dt><dd>{{ $pemesanan->jadwal->tanggal_berangkat->translatedFormat('d F Y') }}</dd>
                    <dt>Jumlah peserta</dt><dd>{{ $pemesanan->jumlah_peserta }} orang</dd>
                    <dt>Kode</dt><dd>{{ $pemesanan->kode_pemesanan }}</dd>
                </dl>
            </div>

            {{-- Metode pembayaran --}}
            <div class="card card-body">
                <h3 style="font-size:14px">Metode Pembayaran — BCA Virtual Account</h3>
                <div class="va-box mt-8">
                    <div>
                        <div class="tiny muted">Nomor Virtual Account</div>
                        <div class="num" id="vaNum">8808 0812 3456 7890</div>
                    </div>
                    <button type="button" class="btn btn-teal btn-sm" onclick="salinVA()">Salin</button>
                </div>
                <div class="tiny muted mt-8">a.n. PT Nanta Ekspedisi Nusantara · Nominal harus sama persis dengan total tagihan.</div>

                <div class="mt-16" style="background:#f8fafc;border:1px solid var(--line);border-radius:10px;padding:14px 16px">
                    <div class="tiny" style="font-weight:800;margin-bottom:8px">Cara Bayar</div>
                    <ol class="tiny muted" style="margin:0;padding-left:16px;line-height:1.9">
                        <li>Buka aplikasi BCA mobile / internet banking.</li>
                        <li>Pilih m-BCA → m-Transfer → BCA Virtual Account.</li>
                        <li>Masukkan nomor Virtual Account di atas.</li>
                        <li>Periksa nominal tagihan, lalu konfirmasi pembayaran.</li>
                        <li>Simpan bukti transfer dan unggah pada form di bawah.</li>
                    </ol>
                </div>
            </div>

            {{-- Unggah bukti --}}
            <div class="card card-body">
                <h3 style="font-size:14px">Unggah Bukti Transfer Bank</h3>

                @if($pemesanan->pembayaran->isNotEmpty())
                    <div class="alert alert-info mt-8">
                        Bukti terakhir diunggah {{ $pemesanan->pembayaran->first()->tanggal_upload->translatedFormat('d M Y H:i') }} —
                        status: <strong>{{ ucfirst(str_replace('_', ' ', $pemesanan->pembayaran->first()->status_verifikasi)) }}</strong>
                    </div>
                @endif

                <form method="POST" action="{{ route('pemesanan.bayar', $pemesanan) }}" enctype="multipart/form-data" class="mt-8">
                    @csrf
                    <label class="dropzone" for="bukti" style="display:block;cursor:pointer">
                        <div class="ico">☁</div>
                        <div class="cap">Tarik berkas ke sini atau klik untuk memilih</div>
                        <div class="sub">Format JPG, PNG, atau PDF · maksimal 2 MB</div>
                        <div class="tiny mt-8" id="namaFile" style="color:#1d4ed8;font-weight:700"></div>
                    </label>
                    <input id="bukti" type="file" name="bukti" accept=".jpg,.jpeg,.png,.pdf" required
                           style="display:none" onchange="document.getElementById('namaFile').textContent = this.files[0] ? this.files[0].name : ''">

                    <button class="btn btn-primary btn-block mt-16" type="submit">Unggah Bukti Pembayaran</button>
                </form>

                <div class="tiny muted mt-8">
                    Setelah diunggah, status pesanan berubah menjadi <strong>Menunggu Verifikasi</strong> dan admin akan memeriksa dokumen Anda.
                </div>
            </div>
        </div>

        <aside>
            <div class="card card-pad" style="position:sticky;top:88px">
                <h3 style="font-size:13.5px">Rincian Harga</h3>
                <div class="mt-8">
                    <div class="summary-line"><span class="muted">Harga per orang</span><span>Rp {{ number_format($pemesanan->jadwal->paket->tarif, 0, ',', '.') }}</span></div>
                    <div class="summary-line"><span class="muted">Jumlah peserta</span><span>{{ $pemesanan->jumlah_peserta }}x</span></div>
                    <div class="summary-line"><span class="muted">Subtotal</span><span>Rp {{ number_format($pemesanan->total_bayar, 0, ',', '.') }}</span></div>
                    <div class="summary-line"><span class="muted">Pajak &amp; biaya</span><span>Rp 0</span></div>
                    <div class="summary-line total"><span>Total Pembayaran</span><span>Rp {{ number_format($pemesanan->total_bayar, 0, ',', '.') }}</span></div>
                </div>
            </div>

            <div class="help-card mt-16">
                <h4>Butuh Bantuan?</h4>
                <p>Hubungi concierge kami bila ada kendala pembayaran atau dokumen.</p>
                <a href="https://wa.me/6281234567890" class="btn-wa">💬 Hubungi Kami</a>
            </div>

            <div class="card card-pad mt-16">
                <div class="tiny muted">
                    🔒 Transaksi aman · dokumen identitas disimpan di penyimpanan privat dan hanya dapat diakses pemilik serta admin.
                </div>
            </div>
        </aside>
    </div>
</div>

<script>
function salinVA() {
    const t = document.getElementById('vaNum').innerText.replace(/\s/g, '');
    navigator.clipboard?.writeText(t);
    alert('Nomor Virtual Account disalin: ' + t);
}

const clock = document.getElementById('clock');
if (clock) {
    let s = parseInt(clock.dataset.sisa, 10);
    const h = clock.querySelector('[data-h]'), m = clock.querySelector('[data-m]'), sec = clock.querySelector('[data-s]');
    const tick = () => {
        h.textContent = String(Math.floor(s / 3600)).padStart(2, '0');
        m.textContent = String(Math.floor((s % 3600) / 60)).padStart(2, '0');
        sec.textContent = String(s % 60).padStart(2, '0');
        if (s <= 0) return;
        s--; setTimeout(tick, 1000);
    };
    tick();
}
</script>
@endsection
