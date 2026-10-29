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
                <div class="field" style="max-width:240px">
                    <label for="jumlah_peserta">Jumlah peserta</label>
                    <input id="jumlah_peserta" type="number" name="jumlah_peserta"
                           value="{{ old('jumlah_peserta', request('jumlah', 1)) }}"
                           min="1" max="{{ min(20, $jadwal->sisa_kuota) }}" required>
                </div>

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
                            <div class="summary-line"><span class="muted">Harga per orang</span><span>Rp {{ number_format($jadwal->paket->tarif, 0, ',', '.') }}</span></div>
                            <div class="summary-line"><span class="muted">Jumlah peserta</span><span id="txtJumlah">1 orang</span></div>
                            <div class="summary-line"><span class="muted">Biaya layanan</span><span>Rp 0</span></div>
                            <div class="summary-line total"><span>Total</span><span id="txtTotal">Rp 0</span></div>
                        </div>

                        <div class="field mt-16">
                            <label>Kode Promo</label>
                            <div class="row" style="gap:6px">
                                <input type="text" placeholder="PROMO2026">
                                <button type="button" class="btn btn-ghost btn-sm">Terapkan</button>
                            </div>
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
const MAKS = {{ min(20, $jadwal->sisa_kuota) }};
const wrap = document.getElementById('pesertaWrap');
const tpl = document.getElementById('tplPeserta');
const inputJumlah = document.getElementById('jumlah_peserta');

function render() {
    const n = Math.max(1, Math.min(parseInt(inputJumlah.value || 1), MAKS));
    wrap.innerHTML = '';
    for (let i = 0; i < n; i++) {
        const node = tpl.content.cloneNode(true);
        node.querySelector('[data-nomor]').textContent = i + 1;
        node.querySelector('[data-peran]').textContent = i === 0 ? '— Data Pemesan' : '— Data Tambahan';
        node.querySelectorAll('[data-name]').forEach(el => {
            el.name = `peserta[${i}][${el.dataset.name}]`;
            if (i === 0 && el.dataset.name === 'email') el.value = @json(auth()->user()->email ?? '');
            if (i === 0 && el.dataset.name === 'no_hp') el.value = @json(auth()->user()->no_hp ?? '');
        });
        wrap.appendChild(node);
    }
    document.getElementById('txtJumlah').textContent = n + ' orang';
    document.getElementById('txtTotal').textContent = 'Rp ' + (TARIF * n).toLocaleString('id-ID');
}
inputJumlah.addEventListener('input', render);
render();
</script>
@endsection
