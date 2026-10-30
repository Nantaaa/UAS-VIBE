@extends('layouts.admin')

@section('title', $paket->exists ? 'Ubah Paket Wisata' : 'Tambah Paket Wisata')

@section('content')
<div class="adm-head">
    <div>
        <div class="crumb">Admin Portal › Master Data › Paket Wisata › {{ $paket->exists ? 'Ubah' : 'Tambah' }}</div>
        <h1>{{ $paket->exists ? 'Ubah Paket Wisata' : 'Tambah Paket Wisata Baru' }}</h1>
        <p>Lengkapi data paket, fasilitas, itinerary, dan tarif resmi.</p>
    </div>
    <div class="actions">
        <a href="{{ route('admin.paket.index') }}" class="btn-adm ghost">Kembali</a>
    </div>
</div>

<div class="adm-card" style="max-width:900px">
    <div class="adm-card-head"><div><h3>Formulir Paket</h3><p>Field bertanda * wajib diisi.</p></div></div>
    <div class="adm-card-body">
        <form method="POST"
              action="{{ $paket->exists ? route('admin.paket.update', $paket) : route('admin.paket.store') }}"
              enctype="multipart/form-data">
            @csrf
            @if($paket->exists) @method('PUT') @endif

            <div class="form-grid">
                <div class="field">
                    <label for="id_destinasi">Destinasi *</label>
                    <select id="id_destinasi" name="id_destinasi" required>
                        <option value="">— Pilih destinasi —</option>
                        @foreach($destinasi as $d)
                            <option value="{{ $d->id }}" @selected(old('id_destinasi', $paket->id_destinasi) == $d->id)>{{ $d->nama_destinasi }} — {{ $d->lokasi }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="nama_paket">Nama Paket *</label>
                    <input id="nama_paket" type="text" name="nama_paket" value="{{ old('nama_paket', $paket->nama_paket) }}" required>
                </div>
                <div class="field">
                    <label for="tipe_trip">Tipe Trip</label>
                    <select id="tipe_trip" name="tipe_trip">
                        @foreach(['Open Trip','Private Trip','Honeymoon','Family','Cultural'] as $t)
                            <option value="{{ $t }}" @selected(old('tipe_trip', $paket->tipe_trip) === $t)>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="durasi_hari">Durasi (hari) *</label>
                    <input id="durasi_hari" type="number" name="durasi_hari" min="1" max="30" value="{{ old('durasi_hari', $paket->durasi_hari) }}" required>
                </div>
                <div class="field">
                    <label for="tarif">Tarif / Orang (Rp) *</label>
                    <input id="tarif" type="number" name="tarif" min="0" step="1000" value="{{ old('tarif', (int) $paket->tarif) }}" required>
                </div>
                <div class="field">
                    <label for="harga_asli">Harga Sebelum Diskon (Rp)</label>
                    <input id="harga_asli" type="number" name="harga_asli" min="0" step="1000" value="{{ old('harga_asli', (int) $paket->harga_asli) }}">
                </div>
                <div class="field full">
                    <label for="highlight">Highlight Singkat</label>
                    <input id="highlight" type="text" name="highlight" value="{{ old('highlight', $paket->highlight) }}" placeholder="Open Trip • Snorkeling • Sunset">
                </div>
                <div class="field full">
                    <label for="deskripsi">Deskripsi</label>
                    <textarea id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi', $paket->deskripsi) }}</textarea>
                </div>
                <div class="field full">
                    <label for="fasilitas">Fasilitas (satu per baris)</label>
                    <textarea id="fasilitas" name="fasilitas" rows="4" placeholder="Hotel bintang 3&#10;Transport AC">{{ old('fasilitas', $paket->fasilitas) }}</textarea>
                </div>
                <div class="field full">
                    <label for="termasuk">Sudah Termasuk (satu per baris)</label>
                    <textarea id="termasuk" name="termasuk" rows="4" placeholder="Hotel 3 malam&#10;Transport AC + driver&#10;Guide lokal">{{ old('termasuk', $paket->termasuk) }}</textarea>
                </div>
                <div class="field full">
                    <label for="tidak_termasuk">Belum Termasuk (satu per baris)</label>
                    <textarea id="tidak_termasuk" name="tidak_termasuk" rows="4" placeholder="Tiket pesawat PP&#10;Hotel H-1&#10;Pengeluaran pribadi">{{ old('tidak_termasuk', $paket->tidak_termasuk) }}</textarea>
                </div>
                <div class="field full">
                    <label for="itinerary">Itinerary (satu hari per baris)</label>
                    <textarea id="itinerary" name="itinerary" rows="4" placeholder="Hari 1: ...&#10;Hari 2: ...">{{ old('itinerary', $paket->itinerary) }}</textarea>
                </div>
                <div class="field">
                    <label for="foto">Foto Paket</label>
                    <input id="foto" type="file" name="foto" accept="image/*">
                    <span class="hint">JPG/PNG/WebP · maks 3 MB</span>
                </div>
                <div class="field">
                    <label>Status</label>
                    <label class="row" style="gap:8px;font-weight:600;font-size:12px">
                        <input type="checkbox" name="is_active" value="1" style="width:auto" @checked(old('is_active', $paket->is_active ?? true))>
                        Tampilkan di katalog
                    </label>
                    <label class="row" style="gap:8px;font-weight:600;font-size:12px">
                        <input type="checkbox" name="is_bestseller" value="1" style="width:auto" @checked(old('is_bestseller', $paket->is_bestseller ?? false))>
                        Tandai Best Seller
                    </label>
                    <label class="row" style="gap:8px;font-weight:600;font-size:12px">
                        <input type="checkbox" name="include_penerbangan" value="1" style="width:auto" @checked(old('include_penerbangan', $paket->include_penerbangan ?? false))>
                        Tiket pesawat sudah termasuk
                    </label>
                </div>
            </div>

            <div class="row mt-24" style="gap:10px">
                <button class="btn-adm orange" type="submit">@include('partials.icon', ['name' => 'check', 'size' => 13]) Simpan Paket</button>
                <a href="{{ route('admin.paket.index') }}" class="btn-adm ghost">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
