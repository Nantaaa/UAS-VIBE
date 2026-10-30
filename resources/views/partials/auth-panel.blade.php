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
        <p class="lead">
            @if($mode === 'daftar')
                Buat akun baru untuk mulai menjelajah — prosesnya kurang dari satu menit.
            @else
                Masuk untuk melihat e-ticket, riwayat perjalanan, dan kelola pemesanan Anda.
            @endif
        </p>
    </div>

    <div class="auth-tabs {{ $mode === 'daftar' ? 'mode-daftar' : 'mode-masuk' }} auth-anim auth-d3" role="tablist">
        <span class="ind" aria-hidden="true"></span>
        <a href="{{ route('login') }}" role="tab" aria-selected="{{ $mode === 'masuk' ? 'true' : 'false' }}"
           class="{{ $mode === 'masuk' ? 'active' : '' }}">Masuk</a>
        <a href="{{ route('register') }}" role="tab" aria-selected="{{ $mode === 'daftar' ? 'true' : 'false' }}"
           class="{{ $mode === 'daftar' ? 'active' : '' }}">Daftar</a>
    </div>

    <button type="button" class="btn-google auth-anim auth-d3"
            onclick="alert('Login Google belum aktif pada demo UAS. Gunakan formulir di bawah atau tombol akses demo.')">
        <svg viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path fill="#FFC107" d="M43.611 20.083H42V20H24v8h11.303c-1.649 4.657-6.08 8-11.303 8-6.627 0-12-5.373-12-12s5.373-12 12-12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 12.955 4 4 12.955 4 24s8.955 20 20 20 20-8.955 20-20c0-2.641-.21-5.236-.611-7.743z"/>
            <path fill="#FF3D00" d="M6.306 14.691l6.571 4.819C14.655 15.108 18.961 12 24 12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 16.318 4 9.656 8.337 6.306 14.691z"/>
            <path fill="#4CAF50" d="M24 44c5.166 0 9.86-1.977 13.409-5.192l-6.19-5.238C29.211 35.091 26.715 36 24 36c-5.202 0-9.619-3.317-11.283-7.946l-6.522 5.025C9.505 39.556 16.227 44 24 44z"/>
            <path fill="#1976D2" d="M43.611 20.083H42V20H24v8h11.303c-.792 2.237-2.231 4.166-4.087 5.571l6.19 5.238C42.022 35.026 44 30.038 44 24c0-2.641-.21-5.236-.611-7.743z"/>
        </svg>
        <span>Lanjutkan dengan Google</span>
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
                <span class="hint">Digunakan untuk pengiriman e-ticket &amp; koordinasi trip via WhatsApp</span>
                @error('no_hp')<span class="err">{{ $message }}</span>@enderror
            </div>

            <div class="form-grid auth-anim auth-d5" style="gap:10px">
                <div class="field">
                    <label for="password">Kata Sandi</label>
                    <div class="pw-wrap">
                        <input id="password" type="password" name="password" placeholder="••••••••" required
                               autocomplete="new-password" data-strength>
                        <button type="button" class="eye-btn" data-target="password" aria-label="Tampilkan kata sandi">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                    <div class="pw-meter" data-meter-for="password" aria-hidden="true"><i></i><i></i><i></i></div>
                    <span class="pw-label" data-label-for="password">Min. 8 karakter, kombinasi huruf &amp; angka</span>
                </div>
                <div class="field">
                    <label for="password_confirmation">Konfirmasi Kata Sandi</label>
                    <div class="pw-wrap">
                        <input id="password_confirmation" type="password" name="password_confirmation" placeholder="••••••••" required
                               autocomplete="new-password" data-match>
                        <button type="button" class="eye-btn" data-target="password_confirmation" aria-label="Tampilkan kata sandi">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                    <div class="pw-meter ghost" aria-hidden="true"><i></i><i></i><i></i></div>
                    <span class="pw-label" data-label-for="password_confirmation">Ulangi kata sandi yang sama</span>
                </div>
            </div>

            <label class="row auth-anim auth-d6" style="gap:8px;align-items:flex-start;font-size:10.5px;color:var(--muted)">
                <input type="checkbox" required style="width:auto;margin-top:2px">
                <span>Saya menyetujui Syarat &amp; Ketentuan serta Kebijakan Privasi untuk pemrosesan data wisatawan.</span>
            </label>

            <button class="btn btn-primary btn-block auth-anim auth-d6" type="submit">
                <span>Daftar Sekarang</span><span class="arw" aria-hidden="true">→</span>
            </button>

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
                    <input id="password" type="password" name="password" placeholder="••••••••" required autocomplete="current-password">
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

            <button class="btn btn-primary btn-block auth-anim auth-d6" type="submit">
                <span>Masuk Sekarang</span><span class="arw" aria-hidden="true">→</span>
            </button>

            <p class="tiny center mb-0 auth-anim auth-d7" style="color:var(--muted)">
                Belum punya akun? <a href="{{ route('register') }}" style="color:var(--orange);font-weight:700">Daftar di sini</a>
            </p>
        </form>

        <div class="auth-perks auth-anim auth-d7">
            <div class="perks-title">Keuntungan akun Nanta</div>
            <ul>
                <li>{!! view('partials.icon', ['name' => 'check-circle', 'size' => 13])->render() !!} E-ticket &amp; voucher digital terbit otomatis setelah pembayaran diverifikasi</li>
                <li>{!! view('partials.icon', ['name' => 'calendar', 'size' => 13])->render() !!} Kelola jadwal, peserta, dan riwayat perjalanan dalam satu akun</li>
                <li>{!! view('partials.icon', ['name' => 'phone', 'size' => 13])->render() !!} Concierge siap membantu koordinasi trip 24 jam via WhatsApp</li>
            </ul>
        </div>
    @endif

    <div class="auth-trust auth-anim auth-d7">
        <span>{!! view('partials.icon', ['name' => 'lock', 'size' => 14])->render() !!} 256-bit Enkripsi Data</span>
        <span>{!! view('partials.icon', ['name' => 'shield', 'size' => 14])->render() !!} ISO 27001 &amp; ASITA</span>
        <span>{!! view('partials.icon', ['name' => 'phone', 'size' => 14])->render() !!} Layanan Concierge 24 Jam</span>
    </div>
</div>

<script>
// Toggle lihat kata sandi
document.querySelectorAll('.eye-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const input = document.getElementById(btn.dataset.target);
        if (!input) return;
        const show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        btn.style.color = show ? 'var(--teal-dark)' : '';
    });
});

// Indikator kekuatan kata sandi
(() => {
    const pw = document.querySelector('input[data-strength]');
    if (!pw) return;
    const meter = document.querySelector('[data-meter-for="password"]');
    const label = document.querySelector('[data-label-for="password"]');
    const konfirmasi = document.querySelector('input[data-match]');
    const labelKonfirmasi = document.querySelector('[data-label-for="password_confirmation"]');

    const nilai = (v) => {
        let skor = 0;
        if (v.length >= 8) skor++;
        if (/[A-Za-z]/.test(v) && /[0-9]/.test(v)) skor++;
        if (v.length >= 12 || /[^A-Za-z0-9]/.test(v)) skor++;
        return v.length === 0 ? 0 : Math.max(1, skor);
    };

    pw.addEventListener('input', () => {
        const skor = nilai(pw.value);
        meter.className = 'pw-meter' + (skor ? ' s' + skor : '');
        const teks = ['Min. 8 karakter, kombinasi huruf & angka', 'Lemah — tambah panjang & variasi', 'Cukup — tambahkan simbol agar kuat', 'Kuat — kata sandi siap dipakai'];
        label.textContent = teks[skor];
        label.style.color = ['', '#dc2626', '#b45309', '#15803d'][skor] || 'var(--muted)';
    });

    if (konfirmasi) {
        const cek = () => {
            const cocok = konfirmasi.value !== '' && konfirmasi.value === pw.value;
            labelKonfirmasi.textContent = konfirmasi.value === '' ? 'Ulangi kata sandi yang sama'
                : (cocok ? 'Kata sandi cocok' : 'Kata sandi belum cocok');
            labelKonfirmasi.style.color = konfirmasi.value === '' ? 'var(--muted)' : (cocok ? '#15803d' : '#dc2626');
        };
        konfirmasi.addEventListener('input', cek);
        pw.addEventListener('input', cek);
    }
})();
</script>
