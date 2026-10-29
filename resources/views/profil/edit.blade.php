@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="container" style="padding:30px 20px 0;max-width:900px">
    <div class="card card-pad mb-16" style="background:linear-gradient(115deg,#0b2440,#14406b 60%,#1b6ff3);color:#fff;border:0">
        <div class="row" style="gap:16px">
            <div class="avatar" style="width:56px;height:56px;font-size:20px;background:rgba(255,255,255,.18);color:#fff">
                {{ strtoupper(substr($user->nama_lengkap, 0, 1)) }}
            </div>
            <div class="grow">
                <h2 style="margin:0;font-size:20px">{{ $user->nama_lengkap }}</h2>
                <div class="tiny" style="opacity:.8">{{ $user->email }} · {{ $user->no_hp }}</div>
            </div>
            <div class="row" style="gap:22px">
                <div class="center">
                    <div style="font-size:20px;font-weight:800">{{ $statistik['total'] }}</div>
                    <div class="tiny" style="opacity:.75">Pemesanan</div>
                </div>
                <div class="center">
                    <div style="font-size:20px;font-weight:800">{{ $statistik['terkonfirmasi'] }}</div>
                    <div class="tiny" style="opacity:.75">Terkonfirmasi</div>
                </div>
                <div class="center">
                    <div style="font-size:20px;font-weight:800">{{ $statistik['perjalanan'] }}</div>
                    <div class="tiny" style="opacity:.75">Peserta</div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-2">
        <div class="card">
            <div class="card-head"><h3>Data profil</h3></div>
            <div class="card-body">
                <form method="POST" action="{{ route('profil.update') }}" class="stack">
                    @csrf
                    @method('PUT')
                    <div class="field">
                        <label for="nama_lengkap">Nama lengkap</label>
                        <input id="nama_lengkap" type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $user->nama_lengkap) }}" required>
                    </div>
                    <div class="field">
                        <label for="email">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required>
                    </div>
                    <div class="field">
                        <label for="no_hp">Nomor HP</label>
                        <input id="no_hp" type="tel" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}" required>
                    </div>
                    <button class="btn btn-primary" type="submit">Simpan perubahan</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-head"><h3>Ganti kata sandi</h3></div>
            <div class="card-body">
                <form method="POST" action="{{ route('profil.password') }}" class="stack">
                    @csrf
                    @method('PUT')
                    <div class="field">
                        <label for="password_lama">Kata sandi lama</label>
                        <input id="password_lama" type="password" name="password_lama" required>
                    </div>
                    <div class="field">
                        <label for="password">Kata sandi baru</label>
                        <input id="password" type="password" name="password" placeholder="Minimal 8 karakter" required>
                    </div>
                    <div class="field">
                        <label for="password_confirmation">Konfirmasi kata sandi baru</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required>
                    </div>
                    <button class="btn btn-navy" type="submit">Perbarui kata sandi</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
