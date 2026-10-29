@extends('layouts.admin')

@section('title', $destinasi->exists ? 'Ubah Destinasi' : 'Tambah Destinasi')

@section('content')
<div class="adm-head">
    <div>
        <div class="crumb">Admin Portal › Master Data › Destinasi › {{ $destinasi->exists ? 'Ubah' : 'Tambah' }}</div>
        <h1>{{ $destinasi->exists ? 'Ubah Destinasi' : 'Tambah Destinasi Baru' }}</h1>
        <p>Lengkapi parameter destinasi untuk integrasi katalog paket wisata.</p>
    </div>
    <div class="actions">
        <a href="{{ route('admin.destinasi.index') }}" class="btn-adm ghost">Batal</a>
    </div>
</div>

<div class="adm-card" style="max-width:760px">
    <div class="adm-card-head">
        <div><h3>Formulir Destinasi</h3><p>Field bertanda * wajib diisi.</p></div>
    </div>
    <div class="adm-card-body">
        <form method="POST"
              action="{{ $destinasi->exists ? route('admin.destinasi.update', $destinasi) : route('admin.destinasi.store') }}"
              enctype="multipart/form-data">
            @csrf
            @if($destinasi->exists) @method('PUT') @endif

            <div class="field">
                <label for="nama_destinasi">Nama Destinasi Wisata *</label>
                <input id="nama_destinasi" type="text" name="nama_destinasi"
                       value="{{ old('nama_destinasi', $destinasi->nama_destinasi) }}" placeholder="Taman Nasional Komodo & Flores" required>
            </div>

            <div class="form-grid mt-16">
                <div class="field">
                    <label for="lokasi">Lokasi / Administrasi *</label>
                    <input id="lokasi" type="text" name="lokasi" value="{{ old('lokasi', $destinasi->lokasi) }}"
                           placeholder="Labuan Bajo, Nusa Tenggara Timur" required>
                </div>
                <div class="field">
                    <label for="deskripsi_ringkas">Deskripsi Singkat Katalog</label>
                    <input id="deskripsi_ringkas" type="text" name="deskripsi" value="{{ old('deskripsi', $destinasi->deskripsi) }}"
                           placeholder="Gugusan pulau eksotis, Pink Beach, Manta Point">
                </div>
            </div>

            <div class="field mt-16">
                <label for="deskripsi">Deskripsi Lengkap</label>
                <textarea id="deskripsi" name="deskripsi" rows="3" placeholder="Ceritakan daya tarik destinasi ini...">{{ old('deskripsi', $destinasi->deskripsi) }}</textarea>
            </div>

            <div class="field mt-16">
                <label for="foto">Upload Foto Utama Destinasi</label>
                <input id="foto" type="file" name="foto" accept="image/*">
                <span class="hint">JPG, PNG, WebP · maks 3 MB · rasio 16:9 lebih baik</span>
                @if($destinasi->foto)
                    <div class="row mt-8" style="gap:10px">
                        <img src="{{ '/storage/'.$destinasi->foto }}" style="width:64px;height:44px;object-fit:cover;border-radius:8px">
                        <span class="tiny muted">Foto saat ini</span>
                    </div>
                @endif
            </div>

            <div class="row mt-24" style="gap:10px">
                <button class="btn-adm orange" type="submit">
                    @include('partials.icon', ['name' => 'check', 'size' => 13]) Simpan Destinasi
                </button>
                <a href="{{ route('admin.destinasi.index') }}" class="btn-adm ghost">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
