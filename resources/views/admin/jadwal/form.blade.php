@extends('layouts.admin')

@section('title', $jadwal->exists ? 'Ubah Jadwal' : 'Tambah Jadwal')
@section('heading', $jadwal->exists ? 'Ubah Jadwal Keberangkatan' : 'Tambah Jadwal Keberangkatan')

@section('content')
<div class="card" style="max-width:760px">
    <div class="card-head">
        <h3>Data jadwal</h3>
        <a href="{{ route('admin.jadwal.index') }}" class="btn btn-ghost btn-sm">Kembali</a>
    </div>
    <div class="card-body">
        <form method="POST"
              action="{{ $jadwal->exists ? route('admin.jadwal.update', $jadwal) : route('admin.jadwal.store') }}">
            @csrf
            @if($jadwal->exists) @method('PUT') @endif

            <div class="form-grid">
                <div class="field full">
                    <label for="id_paket">Paket wisata</label>
                    <select id="id_paket" name="id_paket" required>
                        <option value="">— Pilih paket —</option>
                        @foreach($daftarPaket as $p)
                            <option value="{{ $p->id }}" @selected(old('id_paket', $jadwal->id_paket) == $p->id)>
                                {{ $p->nama_paket }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label for="tanggal_berangkat">Tanggal berangkat</label>
                    <input id="tanggal_berangkat" type="date" name="tanggal_berangkat"
                           value="{{ old('tanggal_berangkat', $jadwal->tanggal_berangkat?->format('Y-m-d')) }}" required>
                </div>

                <div class="field">
                    <label for="tanggal_kembali">Tanggal kembali</label>
                    <input id="tanggal_kembali" type="date" name="tanggal_kembali"
                           value="{{ old('tanggal_kembali', $jadwal->tanggal_kembali?->format('Y-m-d')) }}">
                </div>

                <div class="field">
                    <label for="kuota_peserta">Kuota peserta</label>
                    <input id="kuota_peserta" type="number" name="kuota_peserta" min="1" max="200"
                           value="{{ old('kuota_peserta', $jadwal->kuota_peserta ?? 20) }}" required>
                    @if($jadwal->exists)
                        <span class="hint">Sudah terpakai: {{ $jadwal->kuotaTerpakai() }} peserta. Kuota tidak boleh lebih kecil dari angka ini.</span>
                    @endif
                </div>

                <div class="field full">
                    <label>Petugas bertugas (guide/driver)</label>
                    <div class="grid grid-2" style="gap:8px">
                        @foreach($petugas as $pt)
                            <label class="row" style="gap:8px;font-weight:600;font-size:13.5px">
                                <input type="checkbox" name="petugas[]" value="{{ $pt->id }}" style="width:auto"
                                       @checked(in_array($pt->id, old('petugas', $jadwal->exists ? $jadwal->penugasan->pluck('id_petugas')->all() : [])))>
                                {{ $pt->nama_petugas }} <span class="tiny muted">({{ $pt->peran }})</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="row mt-24" style="gap:10px">
                <button class="btn btn-primary" type="submit">Simpan</button>
                <a href="{{ route('admin.jadwal.index') }}" class="btn btn-ghost">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
