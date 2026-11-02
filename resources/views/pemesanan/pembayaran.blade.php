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
                <div class="ttl">{!! view('partials.icon', ['name' => 'alert', 'size' => 15])->render() !!} Selesaikan Pembayaran Anda Sebelum Waktu Berakhir</div>
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
                    @if($pemesanan->titikJemput)
                        <dt>Titik jemput</dt>
                        <dd>
                            {{ $pemesanan->titikJemput->nama }}
                            @if($pemesanan->titikJemput->jam) · {{ $pemesanan->titikJemput->jam->format('H:i') }} @endif
                        </dd>
                    @endif
                    <dt>Titik kumpul</dt>
                    <dd>
                        {{ $pemesanan->jadwal->kota_kumpul ?? 'Dikonfirmasi admin' }}
                        @if($pemesanan->jadwal->jam_kumpul) · {{ $pemesanan->jadwal->jam_kumpul->format('H:i') }} @endif
                        @if($pemesanan->jadwal->titik_kumpul)
                            <div class="tiny muted">{{ $pemesanan->jadwal->titik_kumpul }}</div>
                        @endif
                    </dd>
                    <dt>Kode</dt><dd>{{ $pemesanan->kode_pemesanan }}</dd>
                </dl>
            </div>

            {{-- Metode pembayaran --}}
            <div class="card card-body">
                <div class="row between wrap" style="gap:8px">
                    <h3 style="font-size:14px;margin:0">Metode Pembayaran Resmi</h3>
                    <span class="badge badge-success">Pilih Saluran Transfer</span>
                </div>

                <div class="row wrap mt-12" style="gap:6px">
                    <button type="button" class="btn btn-sm btn-bank-opt active" onclick="pilihBank('bca', '8808 0812 3456 7890', 'BCA Virtual Account', 'bca-inst')">BCA VA</button>
                    <button type="button" class="btn btn-sm btn-bank-opt" onclick="pilihBank('mandiri', '137-00-1928374-1', 'Bank Mandiri', 'mandiri-inst')">Mandiri</button>
                    <button type="button" class="btn btn-sm btn-bank-opt" onclick="pilihBank('bni', '0293-8472-91', 'Bank BNI', 'bni-inst')">BNI</button>
                    <button type="button" class="btn btn-sm btn-bank-opt" onclick="pilihBank('bri', '0123-01-002938-50-4', 'Bank BRI', 'bri-inst')">BRI</button>
                </div>

                <div class="va-box mt-12">
                    <div>
                        <div class="tiny muted" id="bankLabel">Nomor BCA Virtual Account</div>
                        <div class="num" id="vaNum">8808 0812 3456 7890</div>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm" id="btnSalin" onclick="salinVA()">Salin Nomor</button>
                </div>
                <div class="tiny muted mt-8">a.n. <strong>PT Nanta Ekspedisi Nusantara</strong> — transfer sesuai nominal tagihan <strong>Rp {{ number_format($pemesanan->total_bayar, 0, ',', '.') }}</strong>.</div>

                <div class="mt-16" style="background:#f8fafc;border:1px solid var(--line);border-radius:10px;padding:14px 16px">
                    <div class="tiny" style="font-weight:800;margin-bottom:8px">Petunjuk Pembayaran</div>
                    <ol class="tiny muted" id="instruksiBayar" style="margin:0;padding-left:16px;line-height:1.9">
                        <li>Buka aplikasi Mobile Banking atau ATM bank pilihan Anda.</li>
                        <li>Pilih menu Transfer / Pembayaran ke rekening resmi di atas.</li>
                        <li>Pastikan nama penerima: <strong>PT Nanta Ekspedisi Nusantara</strong>.</li>
                        <li>Masukkan nominal persis: <strong>Rp {{ number_format($pemesanan->total_bayar, 0, ',', '.') }}</strong>.</li>
                        <li>Simpan bukti transfer dan unggah pada formulir di bawah ini.</li>
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
                        <div class="ico">{!! view('partials.icon', ['name' => 'cloud-up', 'size' => 15])->render() !!}</div>
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
            <div class="card">
                <div class="card-head"><h3>Rincian Harga</h3></div>
                <div class="card-body">
                    @php($rincian = $pemesanan->rincianBiaya())
                    <div class="summary-line"><span class="muted">Dewasa {{ $rincian['dewasa'] }} × Rp {{ number_format($rincian['tarif'], 0, ',', '.') }}</span><span>Rp {{ number_format($rincian['dewasa'] * $rincian['tarif'], 0, ',', '.') }}</span></div>
                    @if($rincian['anak'] > 0)
                        <div class="summary-line"><span class="muted">Anak {{ $rincian['anak'] }} × Rp {{ number_format($rincian['tarif_anak'], 0, ',', '.') }} ({{ \App\Models\Pemesanan::persenTarifAnak() }}%)</span><span>Rp {{ number_format($rincian['anak'] * $rincian['tarif_anak'], 0, ',', '.') }}</span></div>
                    @endif
                    <div class="summary-line"><span class="muted">Subtotal</span><span>Rp {{ number_format($rincian['subtotal'], 0, ',', '.') }}</span></div>
                    @if((float) $pemesanan->biaya_jemput > 0)
                        <div class="summary-line"><span class="muted">Biaya titik jemput</span><span>Rp {{ number_format($pemesanan->biaya_jemput, 0, ',', '.') }}</span></div>
                    @endif
                    <div class="summary-line"><span class="muted">Pajak &amp; biaya</span><span>Rp 0</span></div>
                    <div class="summary-line total"><span>Total Pembayaran</span><span>Rp {{ number_format($pemesanan->total_bayar, 0, ',', '.') }}</span></div>
                </div>
                </div>
            </div>

            <div class="help-card mt-16">
                <h4>Butuh Bantuan?</h4>
                <p>Hubungi Customer Service kami bila ada kendala pembayaran atau dokumen.</p>
                <a href="https://wa.me/6281234567890" class="btn-wa">{!! view('partials.icon', ['name' => 'chat', 'size' => 15])->render() !!} Hubungi Kami</a>
            </div>

            <div class="card card-pad mt-16">
                <div class="tiny muted">
                    {!! view('partials.icon', ['name' => 'lock', 'size' => 15])->render() !!} Transaksi aman · dokumen identitas disimpan di penyimpanan privat dan hanya dapat diakses pemilik serta admin.
                </div>
            </div>
        </aside>
    </div>
</div>

<script>
function pilihBank(key, nomor, label, instId) {
    document.querySelectorAll('.btn-bank-opt').forEach(b => b.classList.remove('active'));
    event.currentTarget.classList.add('active');
    document.getElementById('vaNum').textContent = nomor;
    document.getElementById('bankLabel').textContent = 'Nomor ' + label;
}

function salinVA() {
    const t = document.getElementById('vaNum').innerText.replace(/\s/g, '');
    navigator.clipboard?.writeText(t);
    const btn = document.getElementById('btnSalin');
    const old = btn.textContent;
    btn.textContent = '✓ Disalin!';
    btn.style.background = '#15803d';
    setTimeout(() => {
        btn.textContent = old;
        btn.style.background = '';
    }, 2000);
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
