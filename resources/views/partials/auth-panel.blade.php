{{-- Panel login/registrasi — struktur & copy mengikuti desain Figma --}}
@php($mode = $mode ?? 'masuk')
<div class="auth-toolbar auth-anim auth-d1">
    <a href="{{ route('beranda') }}">← Kembali ke Beranda</a>
    <span class="esc">Selesai (Esc)</span>
</div>

<div class="auth-card">
    <div class="auth-anim auth-d2">
        <a href="{{ route('beranda') }}" style="display:inline-block;margin-bottom:14px" title="Nanta Travel">
            <img src="{{ asset('images/logo-nanta.svg') }}" alt="Nanta Travel" height="38" style="height:38px;width:auto;display:block">
        </a>
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
                <a href="javascript:void(0)" onclick="document.getElementById('modalLupaSandi').style.display='flex'"
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
                <li>{!! view('partials.icon', ['name' => 'check-circle', 'size' => 13])->render() !!} Pelacakan status verifikasi berkas KTP &amp; bukti pembayaran secara real-time</li>
                <li>{!! view('partials.icon', ['name' => 'check-circle', 'size' => 13])->render() !!} Akses promo eksklusif dan kemudahan menyimpan paket ke Wishlist</li>
            </ul>
        </div>

        {{-- Modal Bantuan Lupa Sandi --}}
        <div id="modalLupaSandi" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:99999;align-items:center;justify-content:center;padding:20px" onclick="if(event.target===this)this.style.display='none'">
            <div style="background:#fff;border-radius:12px;max-width:440px;width:100%;padding:24px;box-shadow:0 10px 30px rgba(0,0,0,.3)">
                <div class="row between mb-12">
                    <h3 style="margin:0;font-size:15px;color:var(--primary)">🔑 Informasi Bantuan Kata Sandi</h3>
                    <button type="button" onclick="document.getElementById('modalLupaSandi').style.display='none'" style="background:none;border:0;font-size:20px;cursor:pointer">×</button>
                </div>
                <p class="tiny muted" style="line-height:1.7;margin-bottom:12px">
                    Untuk kemudahan evaluasi proyek UAS, kata sandi bawaan seluruh akun demo adalah:
                </p>
                <div class="card card-pad" style="background:#f8fafc;font-family:ui-monospace,monospace;font-size:12px;margin-bottom:14px;text-align:center">
                    <strong>password</strong>
                </div>
                <p class="tiny muted" style="line-height:1.7;margin-bottom:16px">
                    Bila Anda mendaftar dengan email baru dan lupa kata sandinya, silakan gunakan tombol <strong>"Akun Demo Instan"</strong> di bagian bawah halaman login atau hubungi administrator melalui <a href="mailto:admin@nanta.id" style="color:var(--primary);font-weight:700">admin@nanta.id</a>.
                </p>
                <button type="button" class="btn btn-primary btn-block btn-sm" onclick="document.getElementById('modalLupaSandi').style.display='none'">Mengerti</button>
            </div>
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
