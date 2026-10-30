{{-- Akses demo sekali klik — kredensial tidak ditampilkan mentah --}}
<div class="demo-akun">
    <div class="demo-head">
        <span class="demo-dot"></span>
        <span>Akses cepat demo UAS</span>
    </div>
    <div class="demo-row">
        <form method="POST" action="{{ route('demo.login', 'wisatawan') }}">
            @csrf
            <button type="submit" class="demo-btn">
                <strong>Masuk sebagai Wisatawan</strong>
                <span>Katalog, pesan trip, e-ticket</span>
            </button>
        </form>
        <form method="POST" action="{{ route('demo.login', 'admin') }}">
            @csrf
            <button type="submit" class="demo-btn">
                <strong>Masuk sebagai Admin</strong>
                <span>Verifikasi, manifes, laporan</span>
            </button>
        </form>
    </div>
</div>
