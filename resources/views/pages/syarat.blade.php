@extends('layouts.app')

@section('title', 'Syarat & Ketentuan Layanan')

@section('content')
<div class="container" style="padding-top:26px;max-width:920px">
    <div class="crumb mb-16">
        <span><a href="{{ route('beranda') }}">Beranda</a> / <strong>Syarat &amp; Ketentuan</strong></span>
    </div>

    <div class="card card-body" style="padding:32px 36px">
        <h1 style="font-size:24px;margin:0 0 6px">Syarat &amp; Ketentuan Layanan Nanta Travel</h1>
        <div class="tiny muted mb-24">Terakhir diperbarui: 01 November 2026 · Berlaku untuk seluruh pemesanan perjalanan</div>

        <section style="margin-bottom:24px">
            <h3 style="font-size:16px;color:var(--primary);margin-bottom:10px">1. Ketentuan Pemesanan &amp; Batas Pembayaran</h3>
            <p style="font-size:13px;line-height:1.7;color:var(--ink-light)">
                Pemesanan paket wisata dapat dilakukan secara mandiri melalui website resmi Nanta Travel. Setelah formulir pemesanan diserahkan:
            </p>
            <ul style="font-size:13px;line-height:1.8;padding-left:20px;color:var(--ink-light)">
                <li>Kuota peserta pada jadwal yang dipilih akan <strong>ditahan sementara</strong> oleh sistem demi menjamin ketersediaan kursi Anda.</li>
                <li>Wisatawan memiliki batas waktu pembayaran maksimal <strong>1 (satu) jam</strong> sejak pesanan dibuat untuk melakukan pembayaran dan mengunggah bukti transfer.</li>
                <li>Bila bukti pembayaran belum diunggah hingga batas waktu 1 jam berakhir, sistem secara otomatis mengubah status menjadi <strong>Kedaluwarsa</strong> dan melepas kuota kursi ke katalog publik.</li>
                <li>Setiap reservasi paket wisata wajib memenuhi kuota minimal <strong>2 (dua) peserta</strong> dengan sekurang-kurangnya 1 (satu) orang dewasa penanggung jawab.</li>
            </ul>
        </section>

        <section style="margin-bottom:24px">
            <h3 style="font-size:16px;color:var(--primary);margin-bottom:10px">2. Struktur Tarif Peserta Dewasa &amp; Anak</h3>
            <ul style="font-size:13px;line-height:1.8;padding-left:20px;color:var(--ink-light)">
                <li>Peserta dewasa dikenakan tarif normal 100% sesuai harga paket yang tertera.</li>
                <li>Peserta anak (usia di bawah 12 tahun) memperoleh potongan tarif resmi sebesar <strong>50%</strong> dari harga dasar paket dewasa.</li>
                <li>Biaya tambahan penjemputan (*pickup point*) di luar titik kumpul utama dihitung per pemesanan sesuai tarif rute yang dipilih.</li>
            </ul>
        </section>

        <section style="margin-bottom:24px">
            <h3 style="font-size:16px;color:var(--primary);margin-bottom:10px">3. Titik Kumpul, Penjemputan, &amp; Toleransi Keterlambatan</h3>
            <ul style="font-size:13px;line-height:1.8;padding-left:20px;color:var(--ink-light)">
                <li>Titik kumpul keberangkatan (*meeting point*) open trip berada di simpul transportasi kota destinasi (misalnya Bandara Komodo Labuan Bajo atau Bandara Ngurah Rai Bali).</li>
                <li>Transportasi dari kota asal wisatawan menuju kota destinasi diatur mandiri oleh peserta kecuali paket secara eksplisit menyatakan tiket pesawat termasuk.</li>
                <li>Peserta wajib tiba di titik jemput selambat-lambatnya <strong>15 menit</strong> sebelum jadwal keberangkatan armada shuttle.</li>
                <li>Toleransi keterlambatan maksimal adalah <strong>30 menit</strong>. Peserta yang terlambat melebihi batas waktu tersebut wajib menyusul secara mandiri menuju dermaga/tujuan berikutnya dengan biaya sendiri.</li>
            </ul>
        </section>

        <section style="margin-bottom:24px">
            <h3 style="font-size:16px;color:var(--primary);margin-bottom:10px">4. Verifikasi Dokumen Identitas &amp; Pembayaran</h3>
            <ul style="font-size:13px;line-height:1.8;padding-left:20px;color:var(--ink-light)">
                <li>Seluruh peserta wajib mengunggah foto identitas sah (KTP untuk WNI atau Paspor untuk WNA) demi keselamatan manifes pelayaran resmi.</li>
                <li>Admin Operasional berhak menolak atau mengembalikan pesanan dengan status <strong>Perlu Perbaikan</strong> bila foto KTP/paspor buram atau bukti transfer tidak valid.</li>
                <li>Wisatawan dapat mengunggah berkas revisi secara langsung melalui halaman detail pesanan tanpa biaya tambahan.</li>
            </ul>
        </section>

        <section style="margin-bottom:24px">
            <h3 style="font-size:16px;color:var(--primary);margin-bottom:10px">5. Pembatalan &amp; Pengembalian Kuota</h3>
            <ul style="font-size:13px;line-height:1.8;padding-left:20px;color:var(--ink-light)">
                <li>Wisatawan dapat membatalkan pesanan yang belum terkonfirmasi melalui tombol pembatalan di akun pengguna.</li>
                <li>Kuota yang telah dibatalkan akan langsung dikembalikan ke jadwal keberangkatan secara otomatis.</li>
            </ul>
        </section>

        <div class="row between wrap mt-32" style="border-top:1px solid var(--line);padding-top:20px;gap:12px">
            <div class="tiny muted">Ada pertanyaan mengenai ketentuan di atas?</div>
            <a href="https://wa.me/6281234567890" class="btn btn-teal btn-sm" target="_blank">Hubungi Layanan Concierge</a>
        </div>
    </div>
</div>
@endsection
