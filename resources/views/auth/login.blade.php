@extends('layouts.app')

@section('title', 'Masuk')

@section('content')
<div class="auth-wrap">
    <div class="auth-visual">
        <a href="{{ route('beranda') }}" class="brand" style="color:#fff">
            <span class="brand-mark">✈</span> JavaTrip
        </a>
        <div>
            <h2>Pesan perjalanan impian tanpa antre di WhatsApp.</h2>
            <ul>
                <li>Katalog paket dengan sisa kuota terkini</li>
                <li>Unggah KTP/Paspor langsung di sistem</li>
                <li>E-voucher PDF ber-QR Code</li>
                <li>Pantau status pemesanan kapan saja</li>
            </ul>
        </div>
        <div class="tiny" style="color:rgba(255,255,255,.6)">© {{ date('Y') }} JavaTrip Travel — UAS Proyek Sistem Aplikasi</div>
    </div>

    <div class="auth-form">
        <div class="inner">
            <h1>Masuk ke akun</h1>
            <p class="muted">Gunakan email dan kata sandi yang terdaftar.</p>

            <form method="POST" action="{{ route('login.attempt') }}" class="stack mt-24">
                @csrf
                <div class="field">
                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required autofocus>
                </div>
                <div class="field">
                    <label for="password">Kata Sandi</label>
                    <input id="password" type="password" name="password" placeholder="••••••••" required>
                </div>
                <label class="row tiny" style="gap:8px">
                    <input type="checkbox" name="remember" style="width:auto"> Ingat saya
                </label>
                <button class="btn btn-primary btn-block" type="submit">Masuk</button>
            </form>

            <div class="divider">atau</div>
            <p class="tiny muted center mb-0">
                Belum punya akun? <a href="{{ route('register') }}" style="color:var(--primary);font-weight:700">Daftar sekarang</a>
            </p>

            <div class="card card-pad mt-24" style="background:#f8fafc">
                <div class="tiny muted mb-0">
                    <strong style="color:var(--ink)">Akun demo:</strong><br>
                    Admin — admin@javatrip.test / password<br>
                    Wisatawan — wisatawan@javatrip.test / password
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
