@extends('layouts.app')

@section('title', 'Form Pemesanan')

@section('content')
<div class="container" style="padding:28px 20px 0">
    <div class="tiny muted mb-16">
        <a href="{{ route('beranda') }}">Beranda</a> ›
        <a href="{{ route('paket.detail', $jadwal->id_paket) }}">{{ $jadwal->paket->nama_paket }}</a> ›
        <span>Form Pemesanan</span>
    </div>

    <form method="POST" action="{{ route('pemesanan.store', $jadwal) }}" enctype="multipart/form-data" id="formPesan">
        @csrf

        <div class="grid" style="grid-template-columns: 1.6fr 1fr; align-items:start">
            <div class="stack">
                <div class="card">
                    <div class="card-head">
                        <h3>Data peserta</h3>
                        <span class="badge badge-info">Maks. {{ $jadwal->sisa_kuota }} kursi tersisa</span>
                    </div>
                    <div class="card-body">
                        <div class="field" style="max-width:240px">
                            <label for="jumlah_peserta">Jumlah peserta</label>
                            <input id="jumlah_peserta" type="number" name="jumlah_peserta"
                                   value="{{ old('jumlah_peserta', 1) }}" min="1" max="{{ min(20, $jadwal->sisa_kuota) }}" required>
                            <span class="hint">Setiap peserta wajib mengunggah KTP/Paspor.</span>
                        </div>

                        <div id="pesertaWrap" class="stack mt-24"></div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-head"><h3>Ketentuan pemesanan</h3></div>
                    <div class="card-body">
                        <ul class="muted" style="font-size:13.5px;padding-left:18px;margin:0">
                            <li>Kuota ditahan sementara setelah pemesanan dibuat.</li>
                            <li>Batas pembayaran <strong>1 jam</strong> sejak pemesanan. Lewat dari itu pesanan <strong>kedaluwarsa</strong> dan kuota dilepas otomatis.</li>
                            <li>Berkas identitas disimpan di penyimpanan privat, hanya bisa diakses pemilik dan admin.</li>
                            <li>Dokumen yang tidak sesuai akan dikembalikan dengan alasan perbaikan.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="stack" style="position:sticky;top:88px">
                <div class="card card-pad">
                    <h3 style="font-size:14px">Ringkasan</h3>
                    <dl class="kv mt-16">
                        <dt>Paket</dt><dd>{{ $jadwal->paket->nama_paket }}</dd>
                        <dt>Destinasi</dt><dd>{{ $jadwal->paket->destinasi->nama_destinasi }}</dd>
                        <dt>Berangkat</dt><dd>{{ $jadwal->tanggal_berangkat->translatedFormat('d M Y') }}</dd>
                        <dt>Tarif</dt><dd>Rp {{ number_format($jadwal->paket->tarif, 0, ',', '.') }}</dd>
                    </dl>
                    <hr style="border:0;border-top:1px solid var(--line);margin:16px 0">
                    <div class="row between">
                        <span class="tiny muted">Total bayar</span>
                        <strong id="totalTeks" style="font-size:19px;color:var(--primary-dark)">Rp 0</strong>
                    </div>
                    <button class="btn btn-primary btn-block mt-16" type="submit">Buat Pemesanan</button>
                    <div class="tiny muted mt-8 center">Anda punya 1 jam untuk menyelesaikan pembayaran.</div>
                </div>
            </div>
        </div>
    </form>
</div>

<template id="tplPeserta">
    <div class="card" style="padding:16px;box-shadow:none;background:#fafbfe" data-peserta>
        <div class="row between mb-16">
            <strong style="font-size:13.5px">Peserta <span data-nomor>1</span></strong>
            <span class="badge badge-secondary">KTP / Paspor</span>
        </div>
        <div class="form-grid">
            <div class="field">
                <label>Nama lengkap</label>
                <input type="text" data-name="nama_peserta" placeholder="Sesuai identitas" required>
            </div>
            <div class="field">
                <label>Jenis identitas</label>
                <select data-name="jenis_identitas" required>
                    <option value="KTP">KTP</option>
                    <option value="Paspor">Paspor</option>
                </select>
            </div>
            <div class="field">
                <label>Nomor identitas</label>
                <input type="text" data-name="no_identitas" placeholder="16 digit NIK / no. paspor" required>
            </div>
            <div class="field">
                <label>Berkas KTP/Paspor</label>
                <input type="file" data-name="file_identitas" accept=".jpg,.jpeg,.png,.pdf" required>
                <span class="hint">JPG/PNG/PDF, maks 2 MB.</span>
            </div>
        </div>
    </div>
</template>

<script>
const TARIF = {{ (int) $jadwal->paket->tarif }};
const wrap = document.getElementById('pesertaWrap');
const tpl = document.getElementById('tplPeserta');
const inputJumlah = document.getElementById('jumlah_peserta');
const totalTeks = document.getElementById('totalTeks');

function render() {
    const n = Math.max(1, Math.min(parseInt(inputJumlah.value || 1), {{ min(20, $jadwal->sisa_kuota) }}));
    wrap.innerHTML = '';
    for (let i = 0; i < n; i++) {
        const node = tpl.content.cloneNode(true);
        node.querySelector('[data-nomor]').textContent = i + 1;
        node.querySelectorAll('[data-name]').forEach(el => {
            el.name = `peserta[${i}][${el.dataset.name}]`;
        });
        wrap.appendChild(node);
    }
    totalTeks.textContent = 'Rp ' + (TARIF * n).toLocaleString('id-ID');
}

inputJumlah.addEventListener('input', render);
render();
</script>
@endsection
