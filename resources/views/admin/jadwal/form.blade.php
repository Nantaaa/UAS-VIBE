@extends('layouts.admin')

@section('title', $jadwal->exists ? 'Ubah Jadwal' : 'Tambah Jadwal')

@php($terpakai = $jadwal->exists ? $jadwal->kuotaTerpakai() : 0)

@section('content')
<div class="adm-head">
    <div>
        <div class="crumb">Admin Portal › Operasional › Jadwal › {{ $jadwal->exists ? 'Ubah' : 'Tambah' }}</div>
        <h1>{{ $jadwal->exists ? 'Edit Jadwal Keberangkatan' : 'Tambah Jadwal Keberangkatan' }}</h1>
        <p>{{ $jadwal->exists ? 'Paket: '.$jadwal->paket->nama_paket : 'Pilih paket, tanggal, dan kuota peserta.' }}</p>
    </div>
    <div class="actions"><a href="{{ route('admin.jadwal.index') }}" class="btn-adm ghost">Kembali</a></div>
</div>

@if($jadwal->exists && $terpakai > 0)
    <div class="alert alert-info" style="font-size:12px">
        ℹ Jadwal ini sudah memiliki <strong>{{ $terpakai }} peserta aktif</strong>. Kuota tidak boleh lebih kecil dari angka tersebut.
    </div>
@endif

<div class="adm-card" style="max-width:780px">
    <div class="adm-card-head"><div><h3>Formulir Jadwal</h3><p>Field bertanda * wajib diisi.</p></div></div>
    <div class="adm-card-body">
        <form method="POST" action="{{ $jadwal->exists ? route('admin.jadwal.update', $jadwal) : route('admin.jadwal.store') }}">
            @csrf
            @if($jadwal->exists) @method('PUT') @endif

            <div class="field">
                <label for="id_paket">Paket Wisata *</label>
                <select id="id_paket" name="id_paket" required>
                    <option value="">— Pilih paket —</option>
                    @foreach($daftarPaket as $p)
                        <option value="{{ $p->id }}" @selected(old('id_paket', $jadwal->id_paket) == $p->id)>{{ $p->nama_paket }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-grid mt-16">
                <div class="field">
                    <label for="tanggal_berangkat">Tanggal Berangkat *</label>
                    <input id="tanggal_berangkat" type="date" name="tanggal_berangkat"
                           value="{{ old('tanggal_berangkat', $jadwal->tanggal_berangkat?->format('Y-m-d')) }}" required>
                </div>
                <div class="field">
                    <label for="tanggal_kembali">Tanggal Kembali</label>
                    <input id="tanggal_kembali" type="date" name="tanggal_kembali"
                           value="{{ old('tanggal_kembali', $jadwal->tanggal_kembali?->format('Y-m-d')) }}">
                </div>
                <div class="field">
                    <label for="kuota_peserta">Batas Kuota Peserta (Pax) *</label>
                    <input id="kuota_peserta" type="number" name="kuota_peserta" min="1" max="200"
                           value="{{ old('kuota_peserta', $jadwal->kuota_peserta ?? 20) }}" required>
                    <span class="hint">Minimal {{ $terpakai + 1 }} pax karena sudah ada {{ $terpakai }} peserta aktif.</span>
                </div>
                <div class="field">
                    <label>Petugas Bertugas</label>
                    <div style="display:grid;gap:6px;max-height:120px;overflow:auto">
                        @foreach($petugas as $pt)
                            <label class="row" style="gap:7px;font-weight:600;font-size:11.5px">
                                <input type="checkbox" name="petugas[]" value="{{ $pt->id }}" style="width:auto"
                                       @checked(in_array($pt->id, old('petugas', $jadwal->exists ? $jadwal->penugasan->pluck('id_petugas')->all() : [])))>
                                {{ $pt->nama_petugas }} <span class="sub">({{ $pt->peran }})</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="adm-card-head" style="padding:16px 0 10px;border:0">
                <div>
                    <h3 style="font-size:12.5px">Titik Kumpul Keberangkatan</h3>
                    <p>Informasi ini tampil di halaman paket, e-ticket, dan manifes peserta.</p>
                </div>
            </div>

            <div class="form-grid">
                <div class="field">
                    <label for="kota_kumpul">Kota Kumpul</label>
                    <input id="kota_kumpul" type="text" name="kota_kumpul"
                           value="{{ old('kota_kumpul', $jadwal->kota_kumpul) }}" placeholder="Labuan Bajo">
                </div>
                <div class="field">
                    <label for="jam_kumpul">Jam Kumpul</label>
                    <input id="jam_kumpul" type="time" name="jam_kumpul"
                           value="{{ old('jam_kumpul', $jadwal->jam_kumpul?->format('H:i')) }}">
                </div>
                <div class="field full">
                    <label for="titik_kumpul">Titik Kumpul Lengkap</label>
                    <input id="titik_kumpul" type="text" name="titik_kumpul"
                           value="{{ old('titik_kumpul', $jadwal->titik_kumpul) }}"
                           placeholder="Bandara Komodo (LBJ) — area penjemputan, depan gerbang kedatangan">
                </div>
                <div class="field full">
                    <label for="catatan_keberangkatan">Catatan Keberangkatan</label>
                    <textarea id="catatan_keberangkatan" name="catatan_keberangkatan" rows="2"
                              placeholder="Penerbangan dari kota asal belum termasuk. Peserta disarankan tiba H-1.">{{ old('catatan_keberangkatan', $jadwal->catatan_keberangkatan) }}</textarea>
                </div>
            </div>

            <div class="row mt-24" style="gap:10px">
                <button class="btn-adm orange" type="submit">@include('partials.icon', ['name' => 'check', 'size' => 13]) Simpan Perubahan</button>
                <a href="{{ route('admin.jadwal.index') }}" class="btn-adm ghost">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
