@extends('layouts.admin')

@section('title', $destinasi->exists ? 'Ubah Destinasi' : 'Tambah Destinasi')
@section('heading', $destinasi->exists ? 'Ubah Destinasi' : 'Tambah Destinasi')

@section('content')
<div class="card" style="max-width:760px">
    <div class="card-head">
        <h3>Data destinasi</h3>
        <a href="{{ route('admin.destinasi.index') }}" class="btn btn-ghost btn-sm">Kembali</a>
    </div>
    <div class="card-body">
        <form method="POST"
              action="{{ $destinasi->exists ? route('admin.destinasi.update', $destinasi) : route('admin.destinasi.store') }}"
              enctype="multipart/form-data" class="stack">
            @csrf
            @if($destinasi->exists) @method('PUT') @endif

            <div class="field">
                <label for="nama_destinasi">Nama destinasi</label>
                <input id="nama_destinasi" type="text" name="nama_destinasi"
                       value="{{ old('nama_destinasi', $destinasi->nama_destinasi) }}" placeholder="Contoh: Bali" required>
            </div>

            <div class="field">
                <label for="lokasi">Lokasi</label>
                <input id="lokasi" type="text" name="lokasi"
                       value="{{ old('lokasi', $destinasi->lokasi) }}" placeholder="Contoh: Denpasar, Bali" required>
            </div>

            <div class="field">
                <label for="deskripsi">Deskripsi</label>
                <textarea id="deskripsi" name="deskripsi" placeholder="Deskripsi singkat destinasi">{{ old('deskripsi', $destinasi->deskripsi) }}</textarea>
            </div>

            <div class="field">
                <label for="foto">Foto destinasi</label>
                <input id="foto" type="file" name="foto" accept="image/*">
                <span class="hint">JPG/PNG/WebP, maks 3 MB.</span>
            </div>

            <div class="row" style="gap:10px">
                <button class="btn btn-primary" type="submit">Simpan</button>
                <a href="{{ route('admin.destinasi.index') }}" class="btn btn-ghost">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
