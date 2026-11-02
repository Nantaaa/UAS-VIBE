@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="container" style="padding-top:24px">
    <div class="crumb">
        <span><a href="{{ route('beranda') }}">Beranda</a> / <strong>Profil Saya</strong></span>
    </div>

    <h1 style="font-size:23px;margin:0 0 4px">Profil Saya &amp; Pengaturan Akun</h1>
    <p class="muted" style="font-size:12.5px">Kelola informasi pribadi, metode pembayaran, dan keamanan akun Anda.</p>

    {{-- Hero profil --}}
    <div class="profil-hero">
        <div class="row" style="gap:16px">
            <div class="profil-ava">{{ strtoupper(substr($user->nama_lengkap, 0, 1)) }}</div>
            <div>
                <h2 style="margin:0;font-size:19px;color:#fff">{{ $user->nama_lengkap }}</h2>
                <div class="row" style="gap:7px;margin-top:6px">
                    <span class="badge badge-success">✓ Akun Aktif</span>
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
        {{-- Sidebar navigasi akun --}}
        <aside class="card" style="padding:12px">
            <div class="side-label" style="color:var(--muted)">Pengaturan Akun</div>
            <a href="#profil" class="profil-link active" data-tab="profil">
                {!! view('partials.icon', ['name' => 'user', 'size' => 15])->render() !!} Profil Saya
            </a>
            <a href="{{ route('pemesanan.index') }}" class="profil-link">
                {!! view('partials.icon', ['name' => 'ticket', 'size' => 15])->render() !!} Pesanan Saya
            </a>
            <a href="#keamanan" class="profil-link" data-tab="keamanan">
                {!! view('partials.icon', ['name' => 'lock', 'size' => 15])->render() !!} Keamanan Akun
            </a>
            <a href="#pembayaran" class="profil-link" data-tab="pembayaran">
                <span>💳 Metode Pembayaran</span>
                <span class="badge-dot" title="Daftar rekening resmi"></span>
            </a>
            <a href="#notifikasi" class="profil-link" data-tab="notifikasi">
                <span>🔔 Notifikasi</span>
            </a>

            <form method="POST" action="{{ route('logout') }}" class="mt-8">
                @csrf
                <button type="submit" class="profil-link danger" style="width:100%;background:none;border:0;text-align:left;font-family:inherit;cursor:pointer">
                    {!! view('partials.icon', ['name' => 'logout', 'size' => 15])->render() !!} Keluar
                </button>
            </form>

            <div class="card card-pad mt-16" style="background:#f8fafc">
                <div class="tiny" style="font-weight:800">Butuh bantuan?</div>
                <div class="tiny muted" style="margin:4px 0 8px">Hubungi customer service kami.</div>
                <a href="https://wa.me/6281234567890" class="btn btn-teal btn-sm btn-block">Chat CS</a>
            </div>
        </aside>

        {{-- Konten Tab --}}
        <div>
            @if(session('sukses'))
                <div class="alert alert-success mb-16">✓ {{ session('sukses') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-error mb-16">
                    <strong>Perhatian:</strong>
                    <ul style="margin:4px 0 0;padding-left:18px">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- 1. TAB PROFIL --}}
            <div class="profil-tab-content active" id="tab-profil">
                <div class="card">
                    <div class="card-head">
                        <h3>Informasi Pribadi &amp; Kontak</h3>
                        <span class="tiny" style="color:var(--teal-dark);font-weight:700">Data Utama Wisatawan</span>
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
                                    <label>Nomor Telepon (WhatsApp)</label>
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
                                    <label>Kota Asal / Domisili</label>
                                    <input type="text" name="kota" value="{{ old('kota', $user->kota) }}" placeholder="Contoh: Solo / Palembang">
                                </div>
                                <div class="field">
                                    <label>Kode Pos</label>
                                    <input type="text" name="kode_pos" value="{{ old('kode_pos', $user->kode_pos) }}">
                                </div>
                                <div class="field full">
                                    <label>Alamat Lengkap</label>
                                    <input type="text" name="alamat" value="{{ old('alamat', $user->alamat) }}" placeholder="Alamat rumah sesuai KTP">
                                </div>
                            </div>

                            <button class="btn btn-navy mt-16" type="submit" style="background:#0f172a;color:#fff">Simpan Perubahan Profil</button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- 3. TAB METODE PEMBAYARAN --}}
            <div class="profil-tab-content" id="tab-pembayaran">
                <div class="card">
                    <div class="card-head">
                        <div>
                            <h3 style="margin:0">💳 Metode Pembayaran &amp; Rekening Resmi</h3>
                            <p class="tiny muted" style="margin:2px 0 0">Seluruh transaksi pemesanan wisata ditransfer ke rekening resmi a.n. PT Nanta Ekspedisi Nusantara.</p>
                        </div>
                        <span class="badge badge-success">✓ Pembayaran Terverifikasi</span>
                    </div>
                    <div class="card-body">
                        <div class="bank-grid">
                            {{-- BCA --}}
                            <div class="bank-card">
                                <div class="bank-head">
                                    <strong style="color:#005baa;font-size:13.5px">BCA Virtual Account</strong>
                                    <span class="bank-badge">Verifikasi Cepat</span>
                                </div>
                                <div class="bank-norek mt-8">8808 0812 3456 7890</div>
                                <div class="tiny muted">a.n. PT Nanta Ekspedisi Nusantara</div>
                            </div>

                            {{-- Mandiri --}}
                            <div class="bank-card">
                                <div class="bank-head">
                                    <strong style="color:#003366;font-size:13.5px">Bank Mandiri</strong>
                                    <span class="bank-badge">Transfer Bank</span>
                                </div>
                                <div class="bank-norek mt-8">137-00-1928374-1</div>
                                <div class="tiny muted">a.n. PT Nanta Ekspedisi Nusantara</div>
                            </div>

                            {{-- BNI --}}
                            <div class="bank-card">
                                <div class="bank-head">
                                    <strong style="color:#f15a24;font-size:13.5px">Bank BNI</strong>
                                    <span class="bank-badge">Transfer Bank</span>
                                </div>
                                <div class="bank-norek mt-8">0293-8472-91</div>
                                <div class="tiny muted">a.n. PT Nanta Ekspedisi Nusantara</div>
                            </div>

                            {{-- BRI --}}
                            <div class="bank-card">
                                <div class="bank-head">
                                    <strong style="color:#00529c;font-size:13.5px">Bank BRI</strong>
                                    <span class="bank-badge">Transfer Bank</span>
                                </div>
                                <div class="bank-norek mt-8">0123-01-002938-50-4</div>
                                <div class="tiny muted">a.n. PT Nanta Ekspedisi Nusantara</div>
                            </div>
                        </div>

                        <div class="card card-pad mt-16" style="background:#f8fafc;border:1px solid #e2e8f0">
                            <h4 style="margin:0 0 6px;font-size:12.5px">Panduan Pembayaran Resmi Nanta Travel:</h4>
                            <ol style="margin:0;padding-left:18px;font-size:11.5px;color:var(--ink-soft);line-height:1.6">
                                <li>Pemesanan paket wisata akan menahan kuota selama <strong>1 jam</strong>.</li>
                                <li>Lakukan transfer sesuai nominal tagihan yang tertera di halaman pembayaran.</li>
                                <li>Unggah foto/struk bukti transfer pada halaman pesanan Anda.</li>
                                <li>Admin operasional akan memverifikasi pembayaran dan menerbitkan e-ticket serta voucher secara otomatis.</li>
                            </ol>
                        </div>
                    </div>
                </div>

                {{-- Riwayat pembayaran akun --}}
                <div class="card mt-16">
                    <div class="card-head">
                        <h3 style="font-size:14px">Riwayat Pembayaran Anda</h3>
                        <span class="tiny muted">{{ $riwayatPembayaran->count() }} transaksi tercatat</span>
                    </div>
                    <div class="table-wrap">
                        <table class="table" style="font-size:12px">
                            <thead>
                                <tr>
                                    <th>Kode Booking</th>
                                    <th>Paket Wisata</th>
                                    <th>Jumlah Bayar</th>
                                    <th>Waktu Unggah</th>
                                    <th>Status Verifikasi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($riwayatPembayaran as $bayar)
                                    <tr>
                                        <td><code>{{ $bayar->pemesanan->kode_pemesanan }}</code></td>
                                        <td>{{ Str::limit($bayar->pemesanan->jadwal->paket->nama_paket ?? '-', 30) }}</td>
                                        <td><strong>Rp {{ number_format($bayar->jumlah_bayar, 0, ',', '.') }}</strong></td>
                                        <td class="tiny muted">{{ $bayar->tanggal_upload?->translatedFormat('d M Y, H:i') ?? '-' }}</td>
                                        <td>
                                            @if($bayar->status_verifikasi === 'valid')
                                                <span class="badge badge-success">✓ Terverifikasi</span>
                                            @elseif($bayar->status_verifikasi === 'tidak_valid')
                                                <span class="badge badge-danger">Ditolak</span>
                                            @else
                                                <span class="badge badge-warning">Menunggu</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('pemesanan.show', $bayar->pemesanan) }}" class="tiny" style="color:var(--teal-dark);font-weight:700">Lihat Pesanan →</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="center muted py-16">Belum ada riwayat pembayaran pada akun ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- 4. TAB KEAMANAN --}}
            <div class="profil-tab-content" id="tab-keamanan">
                <div class="card">
                    <div class="card-head">
                        <h3>{!! view('partials.icon', ['name' => 'lock', 'size' => 15])->render() !!} Keamanan &amp; Ganti Password</h3>
                        <span class="tiny muted">Pembaruan Kata Sandi Akun</span>
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
                                <div class="tiny" style="color:#1d4ed8;font-weight:700">💡 Tips keamanan akun</div>
                                <div class="tiny" style="color:#1e40af;margin-top:3px">
                                    Gunakan kombinasi huruf besar, huruf kecil, angka, dan simbol. Jangan gunakan kata sandi yang sama dengan akun lain.
                                </div>
                            </div>

                            <button class="btn mt-16" type="submit" style="background:#0f172a;color:#fff">Ubah Password Sekarang</button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- 5. TAB NOTIFIKASI --}}
            <div class="profil-tab-content" id="tab-notifikasi">
                <div class="card">
                    <div class="card-head">
                        <h3>🔔 Preferensi Notifikasi &amp; Saluran Informasi</h3>
                        <span class="badge badge-success">Aktif</span>
                    </div>
                    <div class="card-body">
                        <p class="tiny muted">Pilih jenis informasi perjalanan yang ingin Anda terima melalui WhatsApp dan Email.</p>

                        <div style="display:grid;gap:12px;margin-top:14px">
                            <label class="row between card-pad" style="border:1px solid var(--line);border-radius:10px;background:#fbfcfe">
                                <div>
                                    <strong style="font-size:12.5px;color:var(--ink)">Notifikasi WhatsApp Keberangkatan</strong>
                                    <div class="tiny muted">Pengingat H-1 keberangkatan, info koordinasi titik jemput, dan kontak guide lapangan.</div>
                                </div>
                                <input type="checkbox" checked disabled style="width:auto;accent-color:var(--teal)">
                            </label>

                            <label class="row between card-pad" style="border:1px solid var(--line);border-radius:10px;background:#fbfcfe">
                                <div>
                                    <strong style="font-size:12.5px;color:var(--ink)">Email E-Ticket &amp; Invoice Resmi</strong>
                                    <div class="tiny muted">Pengiriman file PDF voucher, e-ticket ber-barcode, dan bukti konfirmasi verifikasi admin.</div>
                                </div>
                                <input type="checkbox" checked disabled style="width:auto;accent-color:var(--teal)">
                            </label>

                            <label class="row between card-pad" style="border:1px solid var(--line);border-radius:10px;background:#fbfcfe">
                                <div>
                                    <strong style="font-size:12.5px;color:var(--ink)">Informasi Trip &amp; Jadwal Baru</strong>
                                    <div class="tiny muted">Pemberitahuan rute dan jadwal keberangkatan terbaru Nanta Travel.</div>
                                </div>
                                <input type="checkbox" checked style="width:auto;accent-color:var(--teal)">
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Switcher Tab Pengaturan Akun
(() => {
    const links = document.querySelectorAll('.profil-link[data-tab]');
    const tabs = document.querySelectorAll('.profil-tab-content');

    function bukaTab(tabName) {
        if (!tabName) tabName = 'profil';
        tabName = tabName.replace(/^#/, '');

        const target = document.getElementById('tab-' + tabName);
        if (!target) return;

        tabs.forEach(t => t.classList.remove('active'));
        links.forEach(l => l.classList.remove('active'));

        target.classList.add('active');

        const activeLink = document.querySelector(`.profil-link[data-tab="${tabName}"]`);
        if (activeLink) activeLink.classList.add('active');
    }

    links.forEach(l => {
        l.addEventListener('click', (e) => {
            e.preventDefault();
            const t = l.dataset.tab;
            bukaTab(t);
            try { history.replaceState(null, null, '#' + t); } catch (err) {}
        });
    });

    // Cek hash URL awal (contoh: #dokumen, #pembayaran, #keamanan)
    const hash = window.location.hash;
    if (hash) {
        bukaTab(hash);
    }
})();
</script>
@endsection
