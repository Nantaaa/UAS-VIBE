@extends('layouts.app')

@section('title', 'Daftar Akun')

@section('content')
<div class="auth-wrap">
    <div class="auth-visual">
        <a href="{{ route('beranda') }}" class="brand" style="color:#fff">
            <span class="brand-mark">✈</span> JavaTrip
        </a>
        <div>
            <h2>Satu akun untuk semua perjalanan Anda.</h2>
            <ul>
                <li>Riwayat pemesanan tersimpan rapi</li>
                <li>Berkas identitas aman di penyimpanan privat</li>
                <li>Notifikasi status verifikasi</li>
            </ul>
        </div>
        <div class="tiny" style="color:rgba(255,255,255,.6)">© {{ date('Y') }} JavaTrip Travel</div>
    </div>

    <div class="auth-form">
        <div class="inner">
            <h1>Buat akun wisatawan</h1>
            <p class="muted">Isi data berikut untuk mulai memesan paket wisata.</p>

            <form method="POST" action="{{ route('register.store') }}" class="stack mt-24">
                @csrf
                <div class="field">
                    <label for="nama_lengkap">Nama Lengkap</label>
                    <input id="nama_lengkap" type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}" placeholder="Sesuai KTP" required>
                </div>
                <div class="field">
                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required>
                </div>
                <div class="field">
                    <label for="no_hp">Nomor HP</label>
                    <input id="no_hp" type="tel" name="no_hp" value="{{ old('no_hp') }}" placeholder="0812xxxxxxx" required>
                </div>
                <div class="field">
                    <label for="password">Kata Sandi</label>
                    <input id="password" type="password" name="password" placeholder="Minimal 8 karakter" required>
                </div>
                <div class="field">
                    <label for="password_confirmation">Konfirmasi Kata Sandi</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" placeholder="Ulangi kata sandi" required>
                </div>
                <button class="btn btn-primary btn-block" type="submit">Daftar</button>
            </form>

            <p class="tiny muted center mt-24 mb-0">
                Sudah punya akun? <a href="{{ route('login') }}" style="color:var(--primary);font-weight:700">Masuk</a>
            </p>
        </div>
    </div>
</div>
@endsection
