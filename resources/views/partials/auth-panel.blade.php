{{-- Panel login/registrasi — struktur & copy mengikuti desain Figma --}}
@php($mode = $mode ?? 'masuk')
<div class="auth-toolbar auth-anim auth-d1">
    <a href="{{ route('beranda') }}">← Kembali ke Beranda</a>
    <span class="esc">Selesai (Esc)</span>
</div>

<div class="auth-card">
    <div class="auth-anim auth-d2">
        <div class="auth-eyebrow">Akses Akun Travel</div>
        <h2>Selamat Datang di Nanta Travel</h2>
        <p class="lead">Buat akun baru untuk mulai menjelajah atau masuk jika sudah memiliki akun.</p>
    </div>

    <div class="auth-tabs auth-anim auth-d3">
        <a href="{{ route('login') }}" class="{{ $mode === 'masuk' ? 'active' : '' }}">→ Masuk</a>
        <a href="{{ route('register') }}" class="{{ $mode === 'daftar' ? 'active' : '' }}">↗ Daftar</a>
    </div>

    <button type="button" class="btn-google auth-anim auth-d3" onclick="alert('Login Google belum aktif pada demo UAS. Gunakan formulir di bawah.')">
        <svg viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path fill="#FFC107" d="M43.611 20.083H42V20H24v8h11.303c-1.649 4.657-6.08 8-11.303 8-6.627 0-12-5.373-12-12s5.373-12 12-12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 12.955 4 4 12.955 4 24s8.955 20 20 20 20-8.955 20-20c0-2.641-.21-5.236-.611-7.743z"/>
            <path fill="#FF3D00" d="M6.306 14.691l6.571 4.819C14.655 15.108 18.961 12 24 12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 16.318 4 9.656 8.337 6.306 14.691z"/>
            <path fill="#4CAF50" d="M24 44c5.166 0 9.86-1.977 13.409-5.192l-6.19-5.238C29.211 35.091 26.715 36 24 36c-5.202 0-9.619-3.317-11.283-7.946l-6.522 5.025C9.505 39.556 16.227 44 24 44z"/>
            <path fill="#1976D2" d="M43.611 20.083H42V20H24v8h11.303c-.792 2.237-2.231 4.166-4.087 5.571l6.19 5.238C42.022 35.026 44 30.038 44 24c0-2.641-.21-5.236-.611-7.743z"/>
        </svg>
        Masuk cepat dengan Google
    </button>

    <div class="divider-label auth-anim auth-d4"><span>atau dengan formulir</span></div>

    @if($mode === 'daftar')
        <form method="POST" action="{{ route('register.store') }}" class="stack" style="gap:10px">
            @csrf
            <div class="field auth-anim auth-d4">
                <label for="nama_lengkap">Nama Lengkap</label>
                <input id="nama_lengkap" type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}"
                       class="@error('nama_lengkap') invalid @enderror" placeholder="Budi Pratama" required>
                @error('nama_lengkap')<span class="err">{{ $message }}</span>@enderror
            </div>

            <div class="field auth-anim auth-d4">
                <label for="email">Alamat Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}"
                       class="@error('email') invalid @enderror" placeholder="budi.pratama@gmail.com" required>
                @error('email')<span class="err">{{ $message }}</span>@enderror
            </div>

            <div class="field auth-anim auth-d5">
                <label for="no_hp">Nomor Telepon (WhatsApp)</label>
                <input id="no_hp" type="tel" name="no_hp" value="{{ old('no_hp') }}"
                       class="@error('no_hp') invalid @enderror" placeholder="+62 8123-4567-890" required>
                <span class="hint">Diperlukan untuk konfirmasi kehadiran &amp; bimbingan trip ini</span>
                @error('no_hp')<span class="err">{{ $message }}</span>@enderror
            </div>

            <div class="form-grid auth-anim auth-d5" style="gap:10px">
                <div class="field">
                    <label for="password">Kata Sandi</label>
                    <div class="pw-wrap">
                        <input id="password" type="password" name="password" placeholder="••••••••" required>
                        <button type="button" class="eye-btn" data-target="password" aria-label="Tampilkan kata sandi">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                    <span class="hint">Min. 8 karakter, kombinasi huruf &amp; angka</span>
                </div>
                <div class="field">
                    <label for="password_confirmation">Konfirmasi Kata Sandi</label>
                    <div class="pw-wrap">
                        <input id="password_confirmation" type="password" name="password_confirmation" placeholder="••••••••" required>
                        <button type="button" class="eye-btn" data-target="password_confirmation" aria-label="Tampilkan kata sandi">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                    <span class="hint">Kata sandi cocok</span>
                </div>
            </div>

            <label class="row auth-anim auth-d6" style="gap:8px;align-items:flex-start;font-size:10.5px;color:var(--muted)">
                <input type="checkbox" required style="width:auto;margin-top:2px">
                <span>Saya menyetujui Syarat &amp; Ketentuan serta Kebijakan Privasi untuk pemrosesan data wisatawan.</span>
            </label>

            <button class="btn btn-primary btn-block auth-anim auth-d6" type="submit">Daftar Sekarang →</button>

            <p class="tiny center mb-0 auth-anim auth-d7" style="color:var(--muted)">
                Sudah punya akun? <a href="{{ route('login') }}" style="color:var(--orange);font-weight:700">Masuk di sini</a>
            </p>
        </form>
    @else
        <form method="POST" action="{{ route('login.attempt') }}" class="stack" style="gap:10px">
            @csrf
            <div class="field auth-anim auth-d4">
                <label for="email">Alamat Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}"
                       class="@error('email') invalid @enderror" placeholder="nama@email.com" required autofocus>
                @error('email')<span class="err">{{ $message }}</span>@enderror
            </div>

            <div class="field auth-anim auth-d5">
                <label for="password">Kata Sandi</label>
                <div class="pw-wrap">
                    <input id="password" type="password" name="password" placeholder="••••••••" required>
                    <button type="button" class="eye-btn" data-target="password" aria-label="Tampilkan kata sandi">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
            </div>

            <label class="row between auth-anim auth-d5" style="gap:8px;font-size:10.5px;color:var(--muted)">
                <span class="row" style="gap:6px">
                    <input type="checkbox" name="remember" style="width:auto"> Ingat saya
                </span>
                <a href="#" onclick="alert('Fitur reset kata sandi belum aktif pada demo UAS.'); return false;"
                   style="color:var(--orange);font-weight:700">Lupa kata sandi?</a>
            </label>

            <button class="btn btn-primary btn-block auth-anim auth-d6" type="submit">Masuk Sekarang →</button>

            <p class="tiny center mb-0 auth-anim auth-d7" style="color:var(--muted)">
                Belum punya akun? <a href="{{ route('register') }}" style="color:var(--orange);font-weight:700">Daftar di sini</a>
            </p>
        </form>
    @endif

    <div class="auth-trust auth-anim auth-d7">
        <span>{!! view('partials.icon', ['name' => 'lock', 'size' => 15])->render() !!} 256-bit Enkripsi Data</span>
        <span>{!! view('partials.icon', ['name' => 'shield', 'size' => 15])->render() !!} ISO &amp; AITB Certified</span>
        <span>{!! view('partials.icon', ['name' => 'phone', 'size' => 15])->render() !!} Layanan Concierge 24 Jam</span>
    </div>
</div>

<script>
document.querySelectorAll('.eye-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const input = document.getElementById(btn.dataset.target);
        if (!input) return;
        const show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        btn.style.color = show ? 'var(--teal-dark)' : '';
    });
});
</script>
