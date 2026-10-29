@extends('layouts.admin')

@section('title', 'Validasi Voucher')
@section('heading', 'Validasi E-Voucher')

@section('content')
<div class="grid" style="grid-template-columns: 1fr 1.2fr; align-items:start">
    <div class="card card-pad">
        <h3 style="font-size:15px">Pindai / masukkan kode</h3>
        <p class="tiny muted">Masukkan kode pemesanan (contoh: TRV-261029-ABCDE) atau kode QR dari voucher.</p>
        <form method="POST" action="{{ route('admin.voucher.validasi') }}" class="stack">
            @csrf
            <div class="field">
                <label for="kode">Kode pemesanan / QR</label>
                <input id="kode" type="text" name="kode" value="{{ $kode }}" placeholder="TRV-..." autofocus required>
            </div>
            <button class="btn btn-primary btn-block" type="submit">Validasi voucher</button>
        </form>
    </div>

    <div>
        @if($hasil === null)
            <div class="card empty">
                <div class="big">◫</div>
                <h3>Belum ada validasi</h3>
                <p class="mb-0">Hasil pemeriksaan keaslian voucher akan tampil di sini.</p>
            </div>
        @elseif($hasil['valid'])
            <div class="alert alert-success">✓ <div><strong>Voucher ASLI.</strong> {{ $hasil['alasan'] }}</div></div>
            <div class="card">
                <div class="card-head"><h3>Data pemesanan terkait</h3></div>
                <div class="card-body">
                    <dl class="kv">
                        <dt>Kode</dt><dd>{{ $hasil['pemesanan']->kode_pemesanan }}</dd>
                        <dt>Wisatawan</dt><dd>{{ $hasil['pemesanan']->user->nama_lengkap }}</dd>
                        <dt>Paket</dt><dd>{{ $hasil['pemesanan']->jadwal->paket->nama_paket }}</dd>
                        <dt>Destinasi</dt><dd>{{ $hasil['pemesanan']->jadwal->paket->destinasi->nama_destinasi }}</dd>
                        <dt>Berangkat</dt><dd>{{ $hasil['pemesanan']->jadwal->tanggal_berangkat->translatedFormat('d F Y') }}</dd>
                        <dt>Peserta</dt><dd>{{ $hasil['pemesanan']->jumlah_peserta }} orang</dd>
                        <dt>Status</dt><dd><span class="badge badge-success">{{ $hasil['pemesanan']->labelStatus() }}</span></dd>
                    </dl>
                </div>
            </div>
        @else
            <div class="alert alert-error">✕ <div><strong>Voucher TIDAK VALID.</strong> {{ $hasil['alasan'] }}</div></div>
            @if($hasil['pemesanan'])
                <div class="card card-pad">
                    <dl class="kv">
                        <dt>Kode</dt><dd>{{ $hasil['pemesanan']->kode_pemesanan }}</dd>
                        <dt>Wisatawan</dt><dd>{{ $hasil['pemesanan']->user->nama_lengkap }}</dd>
                        <dt>Status</dt><dd><span class="badge badge-{{ $hasil['pemesanan']->badgeStatus() }}">{{ $hasil['pemesanan']->labelStatus() }}</span></dd>
                    </dl>
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
