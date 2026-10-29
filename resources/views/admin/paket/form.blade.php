@extends('layouts.admin')

@section('title', $paket->exists ? 'Ubah Paket Wisata' : 'Tambah Paket Wisata')
@section('heading', $paket->exists ? 'Ubah Paket Wisata' : 'Tambah Paket Wisata')

@section('content')
<div class="card" style="max-width:860px">
    <div class="card-head">
        <h3>Data paket</h3>
        <a href="{{ route('admin.paket.index') }}" class="btn btn-ghost btn-sm">Kembali</a>
    </div>
    <div class="card-body">
        <form method="POST"
              action="{{ $paket->exists ? route('admin.paket.update', $paket) : route('admin.paket.store') }}"
              enctype="multipart/form-data">
            @csrf
            @if($paket->exists) @method('PUT') @endif

            <div class="form-grid">
                <div class="field">
                    <label for="id_destinasi">Destinasi</label>
                    <select id="id_destinasi" name="id_destinasi" required>
                        <option value="">— Pilih destinasi —</option>
                        @foreach($destinasi as $d)
                            <option value="{{ $d->id }}" @selected(old('id_destinasi', $paket->id_destinasi) == $d->id)>
                                {{ $d->nama_destinasi }} — {{ $d->lokasi }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label for="nama_paket">Nama paket</label>
                    <input id="nama_paket" type="text" name="nama_paket" value="{{ old('nama_paket', $paket->nama_paket) }}" required>
                </div>

                <div class="field">
                    <label for="durasi_hari">Durasi (hari)</label>
                    <input id="durasi_hari" type="number" name="durasi_hari" min="1" max="30"
                           value="{{ old('durasi_hari', $paket->durasi_hari) }}" required>
                </div>

                <div class="field">
                    <label for="tarif">Tarif per peserta (Rp)</label>
                    <input id="tarif" type="number" name="tarif" min="0" step="1000"
                           value="{{ old('tarif', (int) $paket->tarif) }}" required>
                </div>

                <div class="field full">
                    <label for="deskripsi">Deskripsi</label>
                    <textarea id="deskripsi" name="deskripsi">{{ old('deskripsi', $paket->deskripsi) }}</textarea>
                </div>

                <div class="field full">
                    <label for="fasilitas">Fasilitas (satu per baris)</label>
                    <textarea id="fasilitas" name="fasilitas" placeholder="Hotel bintang 3&#10;Transport AC">{{ old('fasilitas', $paket->fasilitas) }}</textarea>
                </div>

                <div class="field full">
                    <label for="itinerary">Itinerary (satu per baris)</label>
                    <textarea id="itinerary" name="itinerary" placeholder="Hari 1: ...&#10;Hari 2: ...">{{ old('itinerary', $paket->itinerary) }}</textarea>
                </div>

                <div class="field">
                    <label for="foto">Foto paket</label>
                    <input id="foto" type="file" name="foto" accept="image/*">
                    <span class="hint">JPG/PNG/WebP, maks 3 MB.</span>
                </div>

                <div class="field">
                    <label>Status</label>
                    <label class="row" style="gap:8px;font-weight:600">
                        <input type="checkbox" name="is_active" value="1" style="width:auto"
                               @checked(old('is_active', $paket->is_active ?? true))>
                        Paket aktif dan tampil di katalog
                    </label>
                </div>
            </div>

            <div class="row mt-24" style="gap:10px">
                <button class="btn btn-primary" type="submit">Simpan</button>
                <a href="{{ route('admin.paket.index') }}" class="btn btn-ghost">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
