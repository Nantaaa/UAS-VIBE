{{-- Panel login/registrasi sesuai desain Figma --}}
@php($mode = $mode ?? 'masuk')
<div class="auth-toolbar">
    <a href="{{ route('beranda') }}">← Kembali ke Beranda</a>
    <span class="esc">Selesai (Esc)</span>
</div>

<div class="auth-card">
    <div class="auth-eyebrow">Akses Akun Travel</div>
    <h2>Selamat Datang di Nanta</h2>
    <p class="lead">Buat akun baru untuk mulai menjelajah atau masuk jika sudah memiliki akun.</p>

    <div class="auth-tabs">
        <a href="{{ route('login') }}" class="{{ $mode === 'masuk' ? 'active' : '' }}">⇥ Masuk</a>
        <a href="{{ route('register') }}" class="{{ $mode === 'daftar' ? 'active' : '' }}">✎ Daftar{{ $mode === 'daftar' ? ' (Aktif)' : '' }}</a>
    </div>

    <button type="button" class="btn-google" onclick="alert('Login Google belum aktif pada demo UAS. Gunakan formulir di bawah.')">
        <span style="font-weight:800;color:#4285F4">G</span> Masuk cepat dengan Google
    </button>

    <div class="divider-label"><span>atau dengan formulir</span></div>

    @if($mode === 'daftar')
        <form method="POST" action="{{ route('register.store') }}" class="stack" style="gap:10px">
            @csrf
            <div class="field">
                <label for="nama_lengkap">Nama Lengkap</label>
                <input id="nama_lengkap" type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}"
                       class="@error('nama_lengkap') invalid @enderror" placeholder="Budi Pratama" required>
                @error('nama_lengkap')<span class="err">{{ $message }}</span>@enderror
            </div>

            <div class="field">
                <label for="email">Alamat Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}"
                       class="@error('email') invalid @enderror" placeholder="budi.pratama@gmail.com" required>
                @error('email')<span class="err">{{ $message }}</span>@enderror
            </div>

            <div class="field">
                <label for="no_hp">Nomor Telepon (WhatsApp)</label>
                <input id="no_hp" type="tel" name="no_hp" value="{{ old('no_hp') }}"
                       class="@error('no_hp') invalid @enderror" placeholder="+62 8123-4567-890" required>
                <span class="hint">Diperlukan untuk konfirmasi kehadiran &amp; bimbingan trip ini</span>
                @error('no_hp')<span class="err">{{ $message }}</span>@enderror
            </div>

            <div class="form-grid" style="gap:10px">
                <div class="field">
                    <label for="password">Kata Sandi</label>
                    <input id="password" type="password" name="password" placeholder="••••••••" required>
                    <span class="hint">Min. 8 karakter, kombinasi huruf &amp; angka</span>
                </div>
                <div class="field">
                    <label for="password_confirmation">Konfirmasi Kata Sandi</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" placeholder="••••••••" required>
                    <span class="hint">Kata sandi cocok</span>
                </div>
            </div>

            <label class="row" style="gap:8px;align-items:flex-start;font-size:10.5px;color:var(--muted)">
                <input type="checkbox" required style="width:auto;margin-top:2px">
                <span>Saya menyetujui Syarat &amp; Ketentuan serta Kebijakan Privasi untuk pemrosesan data wisatawan.</span>
            </label>

            <button class="btn btn-primary btn-block" type="submit">Daftar Sekarang →</button>

            <p class="tiny center mb-0" style="color:var(--muted)">
                Sudah punya akun? <a href="{{ route('login') }}" style="color:var(--orange);font-weight:700">Masuk di sini</a>
            </p>
        </form>
    @else
        <form method="POST" action="{{ route('login.attempt') }}" class="stack" style="gap:10px">
            @csrf
            <div class="field">
                <label for="email">Alamat Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}"
                       class="@error('email') invalid @enderror" placeholder="nama@email.com" required autofocus>
                @error('email')<span class="err">{{ $message }}</span>@enderror
            </div>

            <div class="field">
                <label for="password">Kata Sandi</label>
                <input id="password" type="password" name="password" placeholder="••••••••" required>
            </div>

            <label class="row between" style="gap:8px;font-size:10.5px;color:var(--muted)">
                <span class="row" style="gap:6px">
                    <input type="checkbox" name="remember" style="width:auto"> Ingat saya
                </span>
                <a href="#" style="color:var(--orange);font-weight:700">Lupa kata sandi?</a>
            </label>

            <button class="btn btn-primary btn-block" type="submit">Masuk Sekarang →</button>

            <p class="tiny center mb-0" style="color:var(--muted)">
                Belum punya akun? <a href="{{ route('register') }}" style="color:var(--orange);font-weight:700">Daftar di sini</a>
            </p>
        </form>
    @endif

    <div class="auth-trust">
        <span>🔒 256-bit Enkripsi Data</span>
        <span>🛡 ISO &amp; AITB Certified</span>
        <span>☎ Layanan Concierge 24 Jam</span>
    </div>
</div>
