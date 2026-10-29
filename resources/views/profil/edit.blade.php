@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="container" style="padding-top:24px">
    <div class="crumb">
        <span><a href="{{ route('beranda') }}">Beranda</a> / <strong>Profil Saya</strong></span>
    </div>

    <h1 style="font-size:23px;margin:0 0 4px">Profil Saya &amp; Pengaturan Akun</h1>
    <p class="muted" style="font-size:12.5px">Kelola informasi pribadi dan pengaturan keamanan akun agar data perjalanan Anda selalu mutakhir.</p>

    {{-- Hero profil --}}
    <div class="profil-hero">
        <div class="row" style="gap:16px">
            <div class="profil-ava">{{ strtoupper(substr($user->nama_lengkap, 0, 1)) }}</div>
            <div>
                <h2 style="margin:0;font-size:19px;color:#fff">{{ $user->nama_lengkap }}</h2>
                <div class="row" style="gap:7px;margin-top:6px">
                    <span class="badge badge-success">✓ Terverifikasi</span>
                    <span class="badge" style="background:rgba(255,255,255,.14);color:#fff">Member Nanta</span>
                </div>
                <div class="tiny" style="color:rgba(255,255,255,.72);margin-top:8px">
                    {{ $user->email }} · {{ $user->no_hp }}
                </div>
            </div>
        </div>
        <div class="row" style="gap:22px">
            <div class="center">
                <div style="font-size:20px;font-weight:800;color:#fff">{{ $statistik['total'] }}</div>
                <div class="tiny" style="color:rgba(255,255,255,.7)">Pesanan</div>
            </div>
            <div class="center">
                <div style="font-size:20px;font-weight:800;color:#fff">{{ $statistik['terkonfirmasi'] }}</div>
                <div class="tiny" style="color:rgba(255,255,255,.7)">Terkonfirmasi</div>
            </div>
            <div class="center">
                <div style="font-size:20px;font-weight:800;color:#fff">{{ $statistik['perjalanan'] }}</div>
                <div class="tiny" style="color:rgba(255,255,255,.7)">Peserta</div>
            </div>
        </div>
    </div>

    <div class="grid" style="grid-template-columns: 248px 1fr; align-items:start; margin-top:18px">
        {{-- Sidebar --}}
        <aside class="card" style="padding:12px">
            <div class="side-label" style="color:var(--muted)">Pengaturan Akun</div>
            <a href="{{ route('profil.edit') }}" class="profil-link active">👤 Profil Saya</a>
            <a href="{{ route('pemesanan.index') }}" class="profil-link">🎫 Pesanan Saya</a>
            <a href="{{ route('profil.edit') }}#keamanan" class="profil-link">🔒 Keamanan Akun</a>
            <a href="#" class="profil-link">📄 Dokumen &amp; Paspor</a>
            <a href="#" class="profil-link">💳 Metode Pembayaran</a>
            <a href="#" class="profil-link">🔔 Notifikasi</a>

            <form method="POST" action="{{ route('logout') }}" class="mt-8">
                @csrf
                <button type="submit" class="profil-link danger" style="width:100%;background:none;border:0;text-align:left;font-family:inherit;cursor:pointer">
                    ⏻ Keluar
                </button>
            </form>

            <div class="card card-pad mt-16" style="background:#f8fafc">
                <div class="tiny" style="font-weight:800">Butuh bantuan?</div>
                <div class="tiny muted" style="margin:4px 0 8px">Hubungi customer service kami.</div>
                <a href="https://wa.me/6281234567890" class="btn btn-teal btn-sm btn-block">Chat CS</a>
            </div>
        </aside>

        <div class="stack">
            {{-- Info pribadi --}}
            <div class="card">
                <div class="card-head">
                    <h3>Informasi Pribadi &amp; Kontak</h3>
                    <span class="tiny" style="color:#2563eb;font-weight:700">Ubah Informasi</span>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('profil.update') }}">
                        @csrf
                        @method('PUT')
                        <div class="form-grid">
                            <div class="field">
                                <label>Nama Lengkap</label>
                                <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $user->nama_lengkap) }}" required>
                            </div>
                            <div class="field">
                                <label>Email</label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
                            </div>
                            <div class="field">
                                <label>Nomor Telepon</label>
                                <input type="tel" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}" required>
                            </div>
                            <div class="field">
                                <label>Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $user->tanggal_lahir?->format('Y-m-d')) }}">
                            </div>
                            <div class="field">
                                <label>Jenis Kelamin</label>
                                <select name="jenis_kelamin">
                                    <option value="">— Pilih —</option>
                                    <option value="L" @selected(old('jenis_kelamin', $user->jenis_kelamin) === 'L')>Laki-laki</option>
                                    <option value="P" @selected(old('jenis_kelamin', $user->jenis_kelamin) === 'P')>Perempuan</option>
                                </select>
                            </div>
                            <div class="field">
                                <label>Kewarganegaraan</label>
                                <input type="text" name="kewarganegaraan" value="{{ old('kewarganegaraan', $user->kewarganegaraan ?? 'Indonesia') }}">
                            </div>
                            <div class="field">
                                <label>Nomor Identitas / KTP</label>
                                <input type="text" name="no_identitas" value="{{ old('no_identitas', $user->no_identitas) }}" placeholder="16 digit NIK">
                            </div>
                            <div class="field">
                                <label>Kota</label>
                                <input type="text" name="kota" value="{{ old('kota', $user->kota) }}">
                            </div>
                            <div class="field full">
                                <label>Alamat</label>
                                <input type="text" name="alamat" value="{{ old('alamat', $user->alamat) }}">
                            </div>
                            <div class="field">
                                <label>Kode Pos</label>
                                <input type="text" name="kode_pos" value="{{ old('kode_pos', $user->kode_pos) }}">
                            </div>
                        </div>

                        <button class="btn btn-navy mt-16" type="submit" style="background:#0f172a;color:#fff">Simpan Perubahan</button>
                    </form>
                </div>
            </div>

            {{-- Keamanan --}}
            <div class="card" id="keamanan">
                <div class="card-head">
                    <h3>🔒 Keamanan &amp; Ganti Password</h3>
                </div>
                <div class="card-body">
                    <p class="tiny muted">Jaga keamanan akun Anda dengan memperbarui kata sandi secara berkala.</p>

                    <form method="POST" action="{{ route('profil.password') }}">
                        @csrf
                        @method('PUT')
                        <div class="form-grid">
                            <div class="field full">
                                <label>Password Saat Ini</label>
                                <input type="password" name="password_lama" required>
                            </div>
                            <div class="field">
                                <label>Password Baru</label>
                                <input type="password" name="password" placeholder="Minimal 8 karakter" required>
                            </div>
                            <div class="field">
                                <label>Konfirmasi Password Baru</label>
                                <input type="password" name="password_confirmation" required>
                            </div>
                        </div>

                        <div class="mt-16" style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:12px 14px">
                            <div class="tiny" style="color:#1d4ed8;font-weight:700">💡 Tips keamanan</div>
                            <div class="tiny" style="color:#1e40af;margin-top:3px">
                                Gunakan kombinasi huruf besar, huruf kecil, angka, dan simbol. Jangan gunakan kata sandi yang sama dengan akun lain.
                            </div>
                        </div>

                        <button class="btn mt-16" type="submit" style="background:#0f172a;color:#fff">Ubah Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
