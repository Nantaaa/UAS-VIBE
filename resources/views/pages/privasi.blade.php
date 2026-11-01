@extends('layouts.app')

@section('title', 'Kebijakan Privasi')

@section('content')
<div class="container" style="padding-top:26px;max-width:920px">
    <div class="crumb mb-16">
        <span><a href="{{ route('beranda') }}">Beranda</a> / <strong>Kebijakan Privasi</strong></span>
    </div>

    <div class="card card-body" style="padding:32px 36px">
        <h1 style="font-size:24px;margin:0 0 6px">Kebijakan Privasi &amp; Perlindungan Data</h1>
        <div class="tiny muted mb-24">Standar keamanan data dokumen identitas dan transaksi wisatawan Nanta Travel</div>

        <section style="margin-bottom:24px">
            <h3 style="font-size:16px;color:var(--primary);margin-bottom:10px">1. Pengumpulan Data Identitas</h3>
            <p style="font-size:13px;line-height:1.7;color:var(--ink-light)">
                Dalam memproses reservasi perjalanan wisata dan pembuatan manifes keselamatan pelayaran resmi, Nanta Travel mengumpulkan data berupa nama lengkap, nomor identitas (NIK KTP / Paspor), nomor telepon, email, serta foto dokumen identitas.
            </p>
        </section>

        <section style="margin-bottom:24px">
            <h3 style="font-size:16px;color:var(--primary);margin-bottom:10px">2. Penyimpanan Privat &amp; Akses Terbatas</h3>
            <p style="font-size:13px;line-height:1.7;color:var(--ink-light)">
                Sesuai dengan ketentuan perancangan sistem dan mitigasi keamanan:
            </p>
            <ul style="font-size:13px;line-height:1.8;padding-left:20px;color:var(--ink-light)">
                <li>Seluruh berkas foto KTP/Paspor dan bukti transfer disimpan di <strong>penyimpanan privat lokal server</strong> (`storage/app/identitas/` dan `storage/app/pembayaran/`).</li>
                <li>Berkas tidak dapat diakses secara publik melalui URL langsung tanpa otentikasi. Setiap akses berkas dilindungi oleh otorisasi controller (`PemesananController@berkas` dan `ProfilController@lihatBerkas`) yang hanya mengizinkan pemilik akun terdaftar atau Admin Travel.</li>
                <li>Kata sandi seluruh akun wisatawan dienkripsi menggunakan hashing kriptografis standar industri (`Bcrypt`).</li>
            </ul>
        </section>

        <section style="margin-bottom:24px">
            <h3 style="font-size:16px;color:var(--primary);margin-bottom:10px">3. Penggunaan Data untuk Manifes Resmi</h3>
            <p style="font-size:13px;line-height:1.7;color:var(--ink-light)">
                Data nama dan NIK peserta hanya digunakan untuk keperluan penyusunan Manifes Penumpang Pelayaran (KSOP/Armada), asuransi perjalanan, dan validasi e-ticket di pos keberangkatan. Data Anda tidak akan pernah diperjualbelikan atau dibagikan kepada pihak ketiga di luar kepentingan operasional perjalanan.
            </p>
        </section>

        <section style="margin-bottom:24px">
            <h3 style="font-size:16px;color:var(--primary);margin-bottom:10px">4. Hak Pengguna atas Data</h3>
            <p style="font-size:13px;line-height:1.7;color:var(--ink-light)">
                Wisatawan memiliki hak penuh untuk memperbarui biodata, mengubah dokumen identitas, atau mengganti kata sandi akun melalui menu Profil Saya setiap saat.
            </p>
        </section>

        <div class="row between wrap mt-32" style="border-top:1px solid var(--line);padding-top:20px;gap:12px">
            <div class="tiny muted">Punya pertanyaan seputar perlindungan data?</div>
            <a href="mailto:privacy@nanta.id" class="btn btn-ghost btn-sm">Kontak Tim Keamanan Data</a>
        </div>
    </div>
</div>
@endsection
