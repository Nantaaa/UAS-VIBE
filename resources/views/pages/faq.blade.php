@extends('layouts.app')

@section('title', 'Pusat Bantuan & FAQ')

@section('content')
<div class="container" style="padding-top:26px;max-width:920px">
    <div class="crumb mb-16">
        <span><a href="{{ route('beranda') }}">Beranda</a> / <strong>Pusat Bantuan &amp; FAQ</strong></span>
    </div>

    <div class="card card-body" style="padding:32px 36px">
        <h1 style="font-size:24px;margin:0 0 6px">Pusat Bantuan &amp; Pertanyaan Umum (FAQ)</h1>
        <p class="muted mb-24" style="font-size:13px">Jawaban atas pertanyaan yang paling sering diajukan seputar pemesanan paket wisata, titik jemput, pembayaran, dan tiket di Nanta Travel.</p>

        <div style="display:grid;gap:14px">
            <details class="card card-pad" style="border:1px solid var(--line);border-radius:10px;background:#fbfcfe">
                <summary style="font-weight:700;font-size:14px;color:var(--primary);cursor:pointer">
                    Bagaimana alur pemesanan hingga saya mendapatkan E-Tiket?
                </summary>
                <div class="tiny" style="line-height:1.8;color:var(--ink-light);margin-top:10px">
                    1. Pilih paket wisata dan tanggal keberangkatan yang diinginkan di Beranda.<br>
                    2. Masukkan data peserta (minimal 2 orang) dan unggah foto KTP/Paspor asli.<br>
                    3. Lakukan pembayaran via BCA VA, Mandiri, BNI, atau BRI dalam waktu 1 jam.<br>
                    4. Unggah foto bukti transfer bank pada halaman pembayaran.<br>
                    5. Admin memeriksa keabsahan dokumen dan pembayaran.<br>
                    6. Setelah disetujui, E-Tiket resmi ber-QR Code otomatis terbit dan siap diunduh dalam format PDF.
                </div>
            </details>

            <details class="card card-pad" style="border:1px solid var(--line);border-radius:10px;background:#fbfcfe">
                <summary style="font-weight:700;font-size:14px;color:var(--primary);cursor:pointer">
                    Bagaimana konsep titik kumpul (Meeting Point) untuk peserta dari luar kota?
                </summary>
                <div class="tiny" style="line-height:1.8;color:var(--ink-light);margin-top:10px">
                    Untuk paket open trip, titik kumpul resmi berada di kota tujuan wisata (contoh: Bandara Komodo Labuan Bajo atau Bandara Ngurah Rai Bali). Peserta dari kota asal (seperti Solo, Surabaya, Jakarta) mengatur penerbangan mandiri menuju titik kumpul tersebut. Nanta Travel menyediakan penjemputan shuttle resmi dari bandara/hotel menuju pelabuhan atau objek wisata.
                </div>
            </details>

            <details class="card card-pad" style="border:1px solid var(--line);border-radius:10px;background:#fbfcfe">
                <summary style="font-weight:700;font-size:14px;color:var(--primary);cursor:pointer">
                    Berapa tarif untuk peserta anak-anak?
                </summary>
                <div class="tiny" style="line-height:1.8;color:var(--ink-light);margin-top:10px">
                    Peserta anak berusia di bawah 12 tahun mendapatkan diskon resmi 50% dari harga dasar paket dewasa. Ketentuan ini otomatis terkalkulasi di kalkulator halaman pemesanan. Setiap pemesanan wajib menyertakan minimal 1 orang dewasa sebagai pendamping.
                </div>
            </details>

            <details class="card card-pad" style="border:1px solid var(--line);border-radius:10px;background:#fbfcfe">
                <summary style="font-weight:700;font-size:14px;color:var(--primary);cursor:pointer">
                    Apa yang terjadi jika saya telat membayar dalam 1 jam?
                </summary>
                <div class="tiny" style="line-height:1.8;color:var(--ink-light);margin-top:10px">
                    Sistem scheduler otomatis menandai pemesanan sebagai <strong>Kedaluwarsa</strong> jika bukti bayar belum diunggah setelah 1 jam. Kuota kursi yang sempat ditahan akan otomatis dilepaskan kembali ke publik. Anda dapat membuat pesanan baru kapan saja melalui katalog.
                </div>
            </details>

            <details class="card card-pad" style="border:1px solid var(--line);border-radius:10px;background:#fbfcfe">
                <summary style="font-weight:700;font-size:14px;color:var(--primary);cursor:pointer">
                    Apa yang harus dilakukan jika admin meminta perbaikan dokumen?
                </summary>
                <div class="tiny" style="line-height:1.8;color:var(--ink-light);margin-top:10px">
                    Jika foto KTP buram atau slip transfer tidak jelas, status pesanan akan menjadi <strong>Perlu Perbaikan</strong> dengan catatan alasan dari admin. Anda cukup membuka menu Pesanan Saya › Detail Pesanan, lalu klik tombol <em>"Ganti"</em> pada peserta terkait untuk mengunggah berkas baru. Admin akan meninjau ulang secara otomatis.
                </div>
            </details>

            <details class="card card-pad" style="border:1px solid var(--line);border-radius:10px;background:#fbfcfe">
                <summary style="font-weight:700;font-size:14px;color:var(--primary);cursor:pointer">
                    Apakah saya bisa memesan Custom Itinerary atau Private Group?
                </summary>
                <div class="tiny" style="line-height:1.8;color:var(--ink-light);margin-top:10px">
                    Bisa. Nanta Travel menyediakan layanan itinerary privat khusus keluarga, rombongan perusahaan, atau komunitas. Silakan hubungi tim concierge kami melalui tombol WhatsApp di bawah.
                </div>
            </details>
        </div>

        <div class="row between wrap mt-32" style="border-top:1px solid var(--line);padding-top:20px;gap:12px">
            <div>
                <strong>Masih butuh bantuan langsung?</strong>
                <div class="tiny muted">Tim Concierge kami aktif 24 jam siap membantu Anda.</div>
            </div>
            <a href="https://wa.me/6281234567890?text=Halo%20Concierge%20Nanta%20Travel,%20saya%20butuh%20bantuan" class="btn btn-primary" target="_blank">Chat Concierge WhatsApp</a>
        </div>
    </div>
</div>
@endsection
