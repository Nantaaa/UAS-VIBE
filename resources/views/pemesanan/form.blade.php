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

    <form method="POST" action="{{ route('pemesanan.store', $jadwal) }}" enctype="multipart/form-data" id="formBooking">
        @csrf

        @php
            $isPrivate = (request('opsi') === 'private');
            $tarifDewasa = $isPrivate ? (float) $jadwal->paket->tarifPrivate() : (float) $jadwal->paket->tarif;
            $tarifAnak = (float) round($tarifDewasa * \App\Models\Pemesanan::persenTarifAnak() / 100);
            $minTotal = $isPrivate ? 2 : (int) $jadwal->paket->minPeserta();
            $maksDewasa = min((int) config('travel.maks_peserta', 20), $jadwal->sisa_kuota);
            $maksAnak = min((int) config('travel.maks_anak', 10), $jadwal->sisa_kuota);
            $valDewasa = (int) old('dewasa', request('dewasa', request('jumlah', $minTotal)));
            $valAnak = (int) old('anak', request('anak', 0));
            if ($valDewasa + $valAnak < $minTotal) {
                $valDewasa = $minTotal;
                $valAnak = 0;
            }
        @endphp

        <input type="hidden" name="opsi_paket" value="{{ $isPrivate ? 'private' : 'open' }}">

        <div class="grid" style="grid-template-columns: 1fr 372px; align-items:start">
            <div class="stack">
                {{-- Info paket --}}
                <div class="card card-body">
                    <div class="row between wrap" style="gap:12px">
                        <div>
                            <h3 style="margin:0 0 6px;font-size:16px">{{ $jadwal->paket->nama_paket }}</h3>
                            <div class="row" style="gap:7px;flex-wrap:wrap">
                                <span class="pdp-badge">{{ $jadwal->paket->durasi_hari }} Hari {{ $jadwal->paket->durasi_hari - 1 }} Malam</span>
                                <span class="pdp-badge">{{ $isPrivate ? 'Private Group' : ($jadwal->paket->tipe_trip ?: 'Open Trip') }}</span>
                                <span class="pdp-badge orange">{{ $jadwal->tanggal_berangkat->translatedFormat('d M Y') }}</span>
                            </div>
                        </div>
                        <div class="right">
                            <div class="tiny muted">Sisa kuota</div>
                            <strong>{{ $jadwal->sisa_kuota }} kursi</strong>
                        </div>
                    </div>

                    @if($isPrivate)
                        <div style="background:#faf5ff;border:1.5px solid #e9d5ff;border-radius:10px;padding:12px 14px;margin-top:14px">
                            <div class="row" style="gap:8px;align-items:center">
                                <span style="background:#7c3aed;color:#fff;font-size:11px;font-weight:800;padding:2px 8px;border-radius:4px">👑 PRIVATE GROUP</span>
                                <strong style="color:#6b21a8;font-size:13px">Paket Eksklusif Rombongan Sendiri</strong>
                            </div>
                            <p class="tiny" style="color:#7e22ce;margin:6px 0 0;line-height:1.45">
                                Eksklusif untuk rombongan Anda sendiri tanpa gabung orang lain, bebas tentukan tanggal fleksibel.
                            </p>
                        </div>
                    @else
                        <div style="background:#f0f9ff;border:1.5px solid #bae6fd;border-radius:10px;padding:12px 14px;margin-top:14px">
                            <div class="row" style="gap:8px;align-items:center">
                                <span style="background:#0284c7;color:#fff;font-size:11px;font-weight:800;padding:2px 8px;border-radius:4px">👥 OPEN TRIP</span>
                                <strong style="color:#0369a1;font-size:13px">Paket Wisata Gabungan</strong>
                            </div>
                            <p class="tiny" style="color:#0284c7;margin:6px 0 0;line-height:1.45">
                                Gabung dengan peserta lain, minimal kuota terpenuhi (bisa solo traveler berangkat sendirian).
                            </p>
                        </div>
                    @endif

                    <div class="warn-box mt-16">
                        {!! view('partials.icon', ['name' => 'alert', 'size' => 15])->render() !!} Pastikan seluruh data peserta sesuai dengan KTP/Paspor. Data yang tidak sesuai dapat menyebabkan pemesanan dikembalikan untuk diperbaiki.
                    </div>

                    <div class="navy-box mt-16">
                        <strong>Syarat &amp; Ketentuan</strong> — kuota ditahan sementara setelah pemesanan dibuat.
                        Pembayaran wajib diselesaikan dalam <strong>1 jam</strong>, lewat dari itu pesanan kedaluwarsa dan kuota dilepas otomatis.
                    </div>
                </div>

                {{-- Peserta --}}
                <div class="row" style="gap:12px;flex-wrap:wrap;align-items:flex-start">
                    <div class="field" style="flex:1 1 0;min-width:140px;max-width:190px">
                        <label for="dewasa">Jumlah Dewasa</label>
                        <input id="dewasa" type="number" name="dewasa" value="{{ $valDewasa }}"
                               min="1" max="{{ $maksDewasa }}" required>
                    </div>
                    <div class="field" style="flex:1 1 0;min-width:140px;max-width:190px">
                        <label for="anak">Jumlah Anak</label>
                        <input id="anak" type="number" name="anak" value="{{ $valAnak }}"
                               min="0" max="{{ $maksAnak }}">
                    </div>
                    <input type="hidden" name="jumlah_peserta" id="jumlah_peserta" value="{{ $valDewasa + $valAnak }}">
                </div>
                <span class="hint" style="display:block;margin-top:6px">
                    Pemesanan minimal {{ $jadwal->paket->minPeserta() }} peserta{{ $jadwal->paket->minPeserta() === 1 ? ' (bisa solo traveler / berangkat sendirian)' : '' }}. Tarif anak {{ \App\Models\Pemesanan::persenTarifAnak() }}% dari tarif dewasa.
                </span>

                @php($titikAktif = $jadwal->titikJemput->where('urutan', '<', 99))
                @if($titikAktif->isNotEmpty())
                    <div class="field mt-16">
                        <label>Pilih Titik Jemput</label>
                        <div class="titik-pilih">
                            @foreach($titikAktif as $t)
                                <label class="titik-opsi">
                                    <input type="radio" name="id_titik_jemput" value="{{ $t->id }}"
                                           data-biaya="{{ (float) $t->biaya_tambahan }}"
                                           @checked($loop->first)>
                                    <span class="titik-isi">
                                        <span class="titik-nama">{{ $t->nama }}</span>
                                        <span class="titik-meta">
                                            @if($t->jam){{ $t->jam->format('H:i') }}@endif
                                            @if((float) $t->biaya_tambahan > 0)
                                                · +Rp {{ number_format($t->biaya_tambahan, 0, ',', '.') }}
                                            @else
                                                · gratis
                                            @endif
                                        </span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

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
                <div class="card">
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
                            <div class="summary-line"><span class="muted">Tarif dewasa / orang</span><span>Rp {{ number_format($tarifDewasa, 0, ',', '.') }}{{ $isPrivate ? ' (Private)' : '' }}</span></div>
                            <div class="summary-line"><span class="muted">Tarif anak / orang ({{ \App\Models\Pemesanan::persenTarifAnak() }}%)</span><span>Rp {{ number_format($tarifAnak, 0, ',', '.') }}</span></div>
                            <div class="summary-line"><span class="muted">Komposisi peserta</span><span id="txtJumlah">1 dewasa</span></div>
                            <div class="summary-line" id="barisJemput" style="display:none">
                                <span class="muted">Biaya titik jemput</span><span id="txtJemput">Rp 0</span>
                            </div>
                            <div class="summary-line"><span class="muted">Biaya layanan</span><span>Rp 0</span></div>
                            <div class="summary-line total"><span>Total</span><span id="txtTotal">Rp 0</span></div>
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
                    <p>Tim Customer Service kami siap membantu pertanyaan paket dan rute perjalanan.</p>
                    <a href="https://wa.me/6281234567890" class="btn-wa">{!! view('partials.icon', ['name' => 'chat', 'size' => 15])->render() !!} Chat WhatsApp</a>
                </div>
            </aside>
        </div>
    </form>

    <div class="benefit-strip">
        @foreach([
            ['ico' => 'lock', 't' => 'Pembayaran Aman', 'd' => 'Verifikasi bukti transfer oleh admin'],
            ['ico' => 'refresh', 't' => 'Pembatalan Mudah', 'd' => 'Selama belum terkonfirmasi'],
            ['ico' => 'phone', 't' => 'Layanan 24 Jam', 'd' => 'CS siap membantu'],
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
const TARIF = {{ (int) $tarifDewasa }};
const TARIF_ANAK = {{ (int) $tarifAnak }};
const MIN_TOTAL = {{ (int) $minTotal }};
const MAKS = {{ min(20, $jadwal->sisa_kuota) }};
const MAKS_ANAK = {{ min(10, $jadwal->sisa_kuota) }};
const wrap = document.getElementById('pesertaWrap');
const tpl = document.getElementById('tplPeserta');
const inputDewasa = document.getElementById('dewasa');
const inputAnak = document.getElementById('anak');
const inputJumlah = document.getElementById('jumlah_peserta');

function render() {
    // Simpan input yang sudah diketik agar tidak hilang saat ubah jumlah
    const existing = [];
    wrap.querySelectorAll('[data-peserta]').forEach((card, idx) => {
        existing[idx] = {};
        card.querySelectorAll('[data-name]').forEach(inp => {
            if (inp.type !== 'file') existing[idx][inp.dataset.name] = inp.value;
        });
    });

    let dewasa = Math.max(1, Math.min(parseInt(inputDewasa.value || 1, 10), MAKS));
    let anak = Math.max(0, Math.min(parseInt(inputAnak.value || 0, 10), MAKS_ANAK));

    if (dewasa + anak < MIN_TOTAL) {
        if (anak === 0 && dewasa < MIN_TOTAL) {
            dewasa = MIN_TOTAL;
            inputDewasa.value = MIN_TOTAL;
        } else if (dewasa < 1) {
            dewasa = 1;
            inputDewasa.value = 1;
        }
    }

    const n = Math.max(MIN_TOTAL, dewasa + anak);
    inputJumlah.value = n;
    wrap.innerHTML = '';
    for (let i = 0; i < n; i++) {
        const node = tpl.content.cloneNode(true);
        const kategori = i < dewasa ? 'Dewasa' : 'Anak';
        node.querySelector('[data-nomor]').textContent = i + 1;
        node.querySelector('[data-peran]').textContent = i === 0 ? '— Data Pemesan' : '— ' + kategori;
        node.querySelectorAll('[data-name]').forEach(el => {
            el.name = `peserta[${i}][${el.dataset.name}]`;
            const name = el.dataset.name;
            if (existing[i] && existing[i][name] !== undefined) {
                el.value = existing[i][name];
            } else if (i === 0) {
                if (name === 'nama_peserta') el.value = @json(auth()->user()->nama_lengkap ?? '');
                if (name === 'email') el.value = @json(auth()->user()->email ?? '');
                if (name === 'no_hp') el.value = @json(auth()->user()->no_hp ?? '');
                if (name === 'jenis_identitas' && @json(auth()->user()->jenis_identitas ?? '')) el.value = @json(auth()->user()->jenis_identitas ?? 'KTP');
                if (name === 'no_identitas') el.value = @json(auth()->user()->no_identitas ?? '');
                if (name === 'kota') el.value = @json(auth()->user()->kota ?? '');
                if (name === 'alamat') el.value = @json(auth()->user()->alamat ?? '');
            }
        });
        wrap.appendChild(node);
    }
    document.getElementById('txtJumlah').textContent = dewasa + ' dewasa' + (anak > 0 ? ' + ' + anak + ' anak' : '');
    const dipilih = document.querySelector('input[name="id_titik_jemput"]:checked');
    const biayaJemput = dipilih ? parseFloat(dipilih.dataset.biaya || 0) : 0;
    const barisJemput = document.getElementById('barisJemput');
    if (barisJemput) {
        barisJemput.style.display = biayaJemput > 0 ? '' : 'none';
        document.getElementById('txtJemput').textContent = 'Rp ' + biayaJemput.toLocaleString('id-ID');
    }
    document.getElementById('txtTotal').textContent = 'Rp ' + (TARIF * dewasa + TARIF_ANAK * anak + biayaJemput).toLocaleString('id-ID');
}
inputDewasa.addEventListener('input', render);
inputAnak.addEventListener('input', render);
inputDewasa.addEventListener('change', () => {
    let d = parseInt(inputDewasa.value || 1, 10);
    let a = parseInt(inputAnak.value || 0, 10);
    if (d + a < MIN_TOTAL) {
        inputDewasa.value = Math.max(1, MIN_TOTAL - a);
    }
    render();
});
inputAnak.addEventListener('change', () => {
    let d = parseInt(inputDewasa.value || 1, 10);
    let a = parseInt(inputAnak.value || 0, 10);
    if (d + a < MIN_TOTAL) {
        inputDewasa.value = Math.max(1, MIN_TOTAL - a);
    }
    render();
});
document.querySelectorAll('input[name="id_titik_jemput"]').forEach(r => r.addEventListener('change', render));
render();

</script>
@endsection
