@extends('layouts.app')

@section('title', 'Form Pemesanan')

@section('content')
<div class="container" style="padding-top:22px">
    <div class="crumb">
        <span><a href="{{ route('beranda') }}">Beranda</a> / <a href="{{ route('paket.detail', $jadwal->id_paket) }}">{{ Str::limit($jadwal->paket->nama_paket, 34) }}</a> / <strong>Form Pemesanan</strong></span>
    </div>

    <div class="stepper-bar">
        <div class="step active"><span class="num">1</span> Isi Data</div>
        <div class="line"></div>
        <div class="step"><span class="num">2</span> Pembayaran</div>
        <div class="line"></div>
        <div class="step"><span class="num">3</span> Selesai</div>
    </div>

    <form method="POST" action="{{ route('pemesanan.store', $jadwal) }}" enctype="multipart/form-data" id="formPesan">
        @csrf

        <div class="grid" style="grid-template-columns: 1fr 372px; align-items:start">
            <div class="stack">
                {{-- Info paket --}}
                <div class="card card-body">
                    <div class="row between wrap" style="gap:12px">
                        <div>
                            <h3 style="margin:0 0 6px;font-size:16px">{{ $jadwal->paket->nama_paket }}</h3>
                            <div class="row" style="gap:7px;flex-wrap:wrap">
                                <span class="pdp-badge">{{ $jadwal->paket->durasi_hari }} Hari {{ $jadwal->paket->durasi_hari - 1 }} Malam</span>
                                <span class="pdp-badge">{{ $jadwal->paket->tipe_trip }}</span>
                                <span class="pdp-badge orange">{{ $jadwal->tanggal_berangkat->translatedFormat('d M Y') }}</span>
                            </div>
                        </div>
                        <div class="right">
                            <div class="tiny muted">Sisa kuota</div>
                            <strong>{{ $jadwal->sisa_kuota }} kursi</strong>
                        </div>
                    </div>

                    <div class="warn-box mt-16">
                        {!! view('partials.icon', ['name' => 'alert', 'size' => 15])->render() !!} Pastikan seluruh data peserta sesuai dengan KTP/Paspor. Data yang tidak sesuai dapat menyebabkan pemesanan dikembalikan untuk diperbaiki.
                    </div>

                    <div class="navy-box mt-16">
                        <strong>Syarat &amp; Ketentuan</strong> — kuota ditahan sementara setelah pemesanan dibuat.
                        Pembayaran wajib diselesaikan dalam <strong>1 jam</strong>, lewat dari itu pesanan kedaluwarsa dan kuota dilepas otomatis.
                    </div>
                </div>

                {{-- Peserta --}}
                @php
                    $maksDewasa = min((int) config('travel.maks_peserta', 20), $jadwal->sisa_kuota);
                    $maksAnak = min((int) config('travel.maks_anak', 10), $jadwal->sisa_kuota);
                    $valDewasa = (int) old('dewasa', request('dewasa', request('jumlah', 1)));
                    $valAnak = (int) old('anak', request('anak', 0));
                @endphp

                <div class="row" style="gap:12px;flex-wrap:wrap;align-items:flex-start">
                    <div class="field" style="flex:1 1 0;min-width:140px;max-width:190px">
                        <label for="dewasa">Jumlah Dewasa</label>
                        <input id="dewasa" type="number" name="dewasa" value="{{ $valDewasa }}"
                               min="0" max="{{ $maksDewasa }}" required>
                    </div>
                    <div class="field" style="flex:1 1 0;min-width:140px;max-width:190px">
                        <label for="anak">Jumlah Anak</label>
                        <input id="anak" type="number" name="anak" value="{{ $valAnak }}"
                               min="0" max="{{ $maksAnak }}">
                    </div>
                    <input type="hidden" name="jumlah_peserta" id="jumlah_peserta" value="{{ $valDewasa + $valAnak }}">
                </div>
                <span class="hint" style="display:block;margin-top:6px">
                    Tarif anak {{ \App\Models\Pemesanan::persenTarifAnak() }}% dari tarif dewasa. Data peserta dewasa diisi lebih dulu, lalu anak.
                </span>

                <div id="pesertaWrap" class="stack"></div>

                {{-- Persetujuan --}}
                <div class="card card-body">
                    <h3 style="font-size:14px">Persetujuan &amp; Konfirmasi</h3>
                    <label class="row" style="gap:9px;align-items:flex-start;font-size:12px;color:var(--muted)">
                        <input type="checkbox" required style="width:auto;margin-top:2px">
                        <span>Saya menyatakan data peserta benar dan menyetujui Syarat &amp; Ketentuan serta Kebijakan Privasi Nanta Travel.</span>
                    </label>
                    <button class="btn btn-primary btn-block mt-16" type="submit">Lanjut ke Pembayaran →</button>
                </div>
            </div>

            {{-- Ringkasan --}}
            <aside>
                <div class="card" style="position:sticky;top:88px">
                    <div class="card-head"><h3>Ringkasan Pesanan</h3></div>
                    <div class="card-body">
                        <div class="row" style="gap:10px">
                            <div style="width:64px;height:52px;border-radius:8px;background:linear-gradient(140deg,#0e7490,#0f172a);flex:0 0 auto"></div>
                            <div>
                                <div style="font-size:12.5px;font-weight:800;line-height:1.3">{{ Str::limit($jadwal->paket->nama_paket, 46) }}</div>
                                <div class="tiny muted">{{ $jadwal->paket->destinasi->nama_destinasi }}</div>
                            </div>
                        </div>

                        <div class="mt-16">
                            <div class="summary-line"><span class="muted">Tarif dewasa / orang</span><span>Rp {{ number_format($jadwal->paket->tarif, 0, ',', '.') }}</span></div>
                            <div class="summary-line"><span class="muted">Tarif anak / orang ({{ \App\Models\Pemesanan::persenTarifAnak() }}%)</span><span>Rp {{ number_format(\App\Models\Pemesanan::tarifKategori((float) $jadwal->paket->tarif, 'anak'), 0, ',', '.') }}</span></div>
                            <div class="summary-line"><span class="muted">Komposisi peserta</span><span id="txtJumlah">1 dewasa</span></div>
                            <div class="summary-line"><span class="muted">Biaya layanan</span><span>Rp 0</span></div>
                            <div class="summary-line" id="barisDiskon" style="display:none">
                                <span class="muted">Diskon promo</span><span style="color:#15803d" id="txtDiskon">-Rp 0</span>
                            </div>
                            <div class="summary-line total"><span>Total</span><span id="txtTotal">Rp 0</span></div>
                        </div>

                        <div class="field mt-16">
                            <label>Kode Promo</label>
                            <div class="row" style="gap:6px">
                                <input type="text" id="inputPromo" placeholder="HEMAT10" autocomplete="off">
                                <button type="button" class="btn btn-ghost btn-sm" id="btnPromo">Terapkan</button>
                            </div>
                            <span class="hint" id="statusPromo">Coba: HEMAT10 · NANTA500 · WEEKEND15</span>
                            <input type="hidden" name="kode_promo" id="kodePromoHidden">
                        </div>
                    </div>
                </div>

                <div class="card card-pad mt-16" style="background:#fff7ed;border-color:#fed7aa">
                    <div class="tiny" style="color:#9a3412;font-weight:700">Penting</div>
                    <div class="tiny" style="color:#b45309;margin-top:4px">
                        Pesanan otomatis kedaluwarsa bila bukti pembayaran belum diunggah dalam 1 jam sejak pemesanan dibuat.
                    </div>
                </div>

                <div class="help-card mt-16">
                    <h4>Butuh Bantuan?</h4>
                    <p>Tim concierge kami siap membantu penyusunan itinerary dan pertanyaan paket.</p>
                    <a href="https://wa.me/6281234567890" class="btn-wa">{!! view('partials.icon', ['name' => 'chat', 'size' => 15])->render() !!} Chat WhatsApp</a>
                </div>
            </aside>
        </div>
    </form>

    <div class="benefit-strip">
        @foreach([
            ['ico' => 'lock', 't' => 'Pembayaran Aman', 'd' => 'Verifikasi bukti transfer oleh admin'],
            ['ico' => 'refresh', 't' => 'Pembatalan Mudah', 'd' => 'Selama belum terkonfirmasi'],
            ['ico' => 'phone', 't' => 'Layanan 24 Jam', 'd' => 'Concierge siap membantu'],
            ['ico' => 'tag', 't' => 'Harga Terbaik', 'd' => 'Tanpa biaya tersembunyi'],
        ] as $b)
            <div class="benefit">
                <div class="ico">@include('partials.icon', ['name' => $b['ico'], 'size' => 16])</div>
                <div><strong>{{ $b['t'] }}</strong><span>{{ $b['d'] }}</span></div>
            </div>
        @endforeach
    </div>
</div>

<template id="tplPeserta">
    <div class="card" data-peserta>
        <div class="card-head">
            <h3>Peserta <span data-nomor>1</span> <span data-peran style="font-weight:600;color:var(--muted)">— Data Pemesan</span></h3>
            <span class="badge badge-info">Wajib Diisi</span>
        </div>
        <div class="card-body">
            <div class="form-grid">
                <div class="field">
                    <label>Nama Lengkap</label>
                    <input type="text" data-name="nama_peserta" placeholder="Sesuai KTP/Paspor" required>
                </div>
                <div class="field">
                    <label>Jenis Identitas</label>
                    <select data-name="jenis_identitas" required>
                        <option value="KTP">KTP</option>
                        <option value="Paspor">Paspor</option>
                    </select>
                </div>
                <div class="field">
                    <label>NIK / No. Paspor</label>
                    <input type="text" data-name="no_identitas" placeholder="16 digit NIK" required>
                </div>
                <div class="field">
                    <label>Tempat Lahir</label>
                    <input type="text" data-name="tempat_lahir" placeholder="Contoh: Surakarta">
                </div>
                <div class="field">
                    <label>Tanggal Lahir</label>
                    <input type="date" data-name="tanggal_lahir">
                </div>
                <div class="field">
                    <label>Jenis Kelamin</label>
                    <select data-name="jenis_kelamin">
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
                <div class="field">
                    <label>No. Telepon / WhatsApp</label>
                    <input type="tel" data-name="no_hp" placeholder="0812xxxxxxx">
                </div>
                <div class="field">
                    <label>Email</label>
                    <input type="email" data-name="email" placeholder="nama@email.com">
                </div>
                <div class="field full">
                    <label>Alamat Lengkap</label>
                    <input type="text" data-name="alamat" placeholder="Jalan, nomor, RT/RW">
                </div>
                <div class="field">
                    <label>Kota/Kabupaten</label>
                    <input type="text" data-name="kota">
                </div>
                <div class="field">
                    <label>Provinsi</label>
                    <input type="text" data-name="provinsi">
                </div>
                <div class="field full">
                    <label>Berkas KTP/Paspor</label>
                    <input type="file" data-name="file_identitas" accept=".jpg,.jpeg,.png,.pdf" required>
                    <span class="hint">JPG/PNG/PDF, maks 2 MB.</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
const TARIF = {{ (int) $jadwal->paket->tarif }};
const TARIF_ANAK = {{ (int) \App\Models\Pemesanan::tarifKategori((float) $jadwal->paket->tarif, 'anak') }};
const MAKS = {{ min(20, $jadwal->sisa_kuota) }};
const MAKS_ANAK = {{ min(10, $jadwal->sisa_kuota) }};
const wrap = document.getElementById('pesertaWrap');
const tpl = document.getElementById('tplPeserta');
const inputDewasa = document.getElementById('dewasa');
const inputAnak = document.getElementById('anak');
const inputJumlah = document.getElementById('jumlah_peserta');

function render() {
    const dewasa = Math.max(0, Math.min(parseInt(inputDewasa.value || 0, 10), MAKS));
    const anak = Math.max(0, Math.min(parseInt(inputAnak.value || 0, 10), MAKS_ANAK));
    const n = Math.max(1, dewasa + anak);
    inputJumlah.value = n;
    wrap.innerHTML = '';
    for (let i = 0; i < n; i++) {
        const node = tpl.content.cloneNode(true);
        const kategori = i < dewasa ? 'Dewasa' : 'Anak';
        node.querySelector('[data-nomor]').textContent = i + 1;
        node.querySelector('[data-peran]').textContent = i === 0 ? '— Data Pemesan' : '— ' + kategori;
        node.querySelectorAll('[data-name]').forEach(el => {
            el.name = `peserta[${i}][${el.dataset.name}]`;
            if (i === 0 && el.dataset.name === 'email') el.value = @json(auth()->user()->email ?? '');
            if (i === 0 && el.dataset.name === 'no_hp') el.value = @json(auth()->user()->no_hp ?? '');
        });
        wrap.appendChild(node);
    }
    document.getElementById('txtJumlah').textContent = dewasa + ' dewasa' + (anak > 0 ? ' + ' + anak + ' anak' : '');
    document.getElementById('txtTotal').textContent = 'Rp ' + (TARIF * dewasa + TARIF_ANAK * anak).toLocaleString('id-ID');
}
inputDewasa.addEventListener('input', render);
inputAnak.addEventListener('input', render);
render();

// ===== Kode promo =====
const btnPromo = document.getElementById('btnPromo');
if (btnPromo) {
    btnPromo.addEventListener('click', async () => {
        const kode = document.getElementById('inputPromo').value.trim();
        const status = document.getElementById('statusPromo');
        const dewasaVal = parseInt(document.getElementById('dewasa').value || 0, 10);
        const anakVal = parseInt(document.getElementById('anak').value || 0, 10);
        const jadwal = {{ $jadwal->id }};

        if (!kode) {
            status.textContent = 'Masukkan kode promo terlebih dahulu.';
            status.style.color = '#dc2626';
            return;
        }

        btnPromo.disabled = true;
        status.textContent = 'Memeriksa kode...';
        status.style.color = '#6b7280';

        try {
            const res = await fetch('{{ route('promo.cek') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ kode: kode, id_jadwal: jadwal, dewasa: dewasaVal, anak: anakVal, jumlah: dewasaVal + anakVal })
            });
            const data = await res.json();

            if (data.valid) {
                document.getElementById('kodePromoHidden').value = data.kode;
                document.getElementById('barisDiskon').style.display = '';
                document.getElementById('txtDiskon').textContent = '-Rp ' + Math.round(data.diskon).toLocaleString('id-ID');
                document.getElementById('txtTotal').textContent = 'Rp ' + Math.round(data.total).toLocaleString('id-ID');
                status.textContent = data.pesan;
                status.style.color = '#15803d';
            } else {
                document.getElementById('kodePromoHidden').value = '';
                document.getElementById('barisDiskon').style.display = 'none';
                document.getElementById('txtTotal').textContent = 'Rp ' + (TARIF * dewasaVal + TARIF_ANAK * anakVal).toLocaleString('id-ID');
                status.textContent = data.pesan || 'Kode promo tidak valid.';
                status.style.color = '#dc2626';
            }
        } catch (e) {
            status.textContent = 'Gagal memeriksa kode promo.';
            status.style.color = '#dc2626';
        } finally {
            btnPromo.disabled = false;
        }
    });
}

</script>
@endsection
