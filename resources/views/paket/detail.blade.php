@extends('layouts.app')

@section('title', $paket->nama_paket)

@php
    $jadwalPertama = $paket->jadwalTersedia->first();
    // Parse itinerary: tiap baris "Hari N: A - B - C" jadi kartu hari
    $hari = [];
    foreach (preg_split('/\r\n|\r|\n/', (string) $paket->itinerary) as $baris) {
        $baris = trim($baris);
        if ($baris === '') continue;
        if (preg_match('/^Hari\s*(\d+)\s*[:.-]\s*(.+)$/i', $baris, $m)) {
            $hari[] = ['judul' => 'Hari '.$m[1].': '.$m[2], 'langkah' => []];
        } else {
            $hari[count($hari) - 1]['langkah'][] = $baris;
        }
    }
    foreach ($hari as $i => $h) {
        $pecah = array_filter(array_map('trim', preg_split('/\s+-\s+/', $h['judul'])));
        if (count($pecah) > 1) {
            $hari[$i]['judul'] = array_shift($pecah);
            $hari[$i]['langkah'] = array_merge($pecah, $h['langkah']);
        }
    }
@endphp

@section('content')
<div class="container" style="padding-top:20px">
    <div class="crumb">
        <span><a href="{{ route('beranda') }}">Beranda</a> / <a href="{{ route('beranda') }}#katalog">Paket Wisata</a> / <strong>{{ Str::limit($paket->nama_paket, 40) }}</strong></span>
    </div>

    {{-- Galeri --}}
    <div class="pdp-gallery">
        <div class="pdp-main">
            @if($paket->foto)<img src="{{ '/storage/'.$paket->foto }}" alt="{{ $paket->nama_paket }}">@endif
            <span class="pdp-gallery-btn">📷 Lihat semua foto</span>
        </div>
        <div class="pdp-side">
            @foreach($galeri as $i => $g)
                <div>
                    <img src="{{ '/storage/'.$g->foto }}" alt="{{ $g->nama_paket }}">
                    <span class="pdp-gallery-btn" style="font-size:10px">📷 {{ $i + 2 }}/12</span>
                </div>
            @endforeach
        </div>
    </div>

    <div class="grid" style="grid-template-columns: 1fr 372px; align-items:start">
        <div>
            <div class="pdp-badges">
                <span class="pdp-badge">Nanta Exclusive</span>
                @if($paket->is_bestseller)<span class="pdp-badge orange">Best Seller</span>@endif
            </div>

            <h1 class="pdp-title">{{ $paket->nama_paket }}</h1>
            <div class="pdp-loc">📍 {{ $paket->destinasi->lokasi }}</div>

            <div class="row" style="gap:16px;margin-top:10px">
                <div class="pdp-rating">★ 4.9 <span class="muted" style="font-weight:600">(1.2rb ulasan)</span></div>
                <div class="tiny muted">{{ $paket->tipe_trip }}</div>
            </div>

            <div class="pdp-meta">
                <div>🗓 {{ $paket->durasi_hari }} Hari {{ $paket->durasi_hari - 1 }} Malam</div>
                <div>👥 Min. 2 Peserta</div>
                <div>🏷 {{ $paket->tipe_trip }}</div>
                <div>✅ Kuota {{ $jadwalPertama->sisa_kuota ?? 0 }} kursi tersisa</div>
            </div>

            {{-- Tab --}}
            <div class="tabbar">
                <button type="button" class="active" data-tab="deskripsi">Deskripsi</button>
                <button type="button" data-tab="itinerary">Itinerary</button>
                <button type="button" data-tab="fasilitas">Fasilitas</button>
                <button type="button" data-tab="syarat">Syarat &amp; Ketentuan</button>
            </div>

            <div class="tab-panel active" id="tab-deskripsi">
                <div class="card card-body">
                    <p class="muted" style="font-size:13px">{{ $paket->deskripsi }}</p>
                    <div class="tiny muted">{{ $paket->highlight }}</div>
                </div>
            </div>

            <div class="tab-panel" id="tab-itinerary">
                <h3 style="font-size:15px;margin-bottom:12px">Rencana Perjalanan / Itinerary</h3>
                @forelse($hari as $h)
                    <div class="day-card">
                        <h4>{{ $h['judul'] }}</h4>
                        <ul class="day-steps">
                            @foreach($h['langkah'] as $langkah)
                                <li><span class="dot"></span><span>{{ $langkah }}</span></li>
                            @endforeach
                        </ul>
                    </div>
                @empty
                    <div class="card empty">Itinerary belum tersedia.</div>
                @endforelse
            </div>

            <div class="tab-panel" id="tab-fasilitas">
                <div class="grid grid-2">
                    @foreach($paket->fasilitasList() as $f)
                        <div class="card card-pad row" style="gap:10px">
                            <span style="color:var(--green);font-weight:800">✓</span>
                            <span style="font-size:12.5px">{{ $f }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="tab-panel" id="tab-syarat">
                <div class="card card-body">
                    <ul class="muted" style="font-size:12.5px;padding-left:18px;margin:0">
                        <li>Harga berlaku per peserta dan dapat berubah tanpa pemberitahuan.</li>
                        <li>Kuota ditahan sementara setelah pemesanan dibuat, batas pembayaran 1 jam.</li>
                        <li>Pesanan yang belum dibayar sampai batas waktu akan kedaluwarsa otomatis.</li>
                        <li>Dokumen identitas (KTP/Paspor) wajib sesuai dengan data peserta.</li>
                        <li>Pembatalan dapat dilakukan selama status belum terkonfirmasi.</li>
                    </ul>
                </div>
            </div>

            <h3 style="font-size:15px;margin:24px 0 10px">Peta Lokasi &amp; Meeting Point</h3>
            <div class="map-box">
                📍 Lokasi akan dikonfirmasi admin
                <span class="tag">Lihat Peta</span>
            </div>
        </div>

        {{-- Booking card --}}
        <aside>
            <div class="book-card">
                <div class="book-price">Rp {{ number_format($paket->tarif, 0, ',', '.') }} <span>/ orang</span></div>
                @if($paket->harga_asli)
                    <div class="tiny muted" style="text-decoration:line-through">Rp {{ number_format($paket->harga_asli, 0, ',', '.') }}</div>
                @endif
                <div class="tiny muted mb-8">Sudah termasuk pajak &amp; biaya layanan</div>

                <form method="GET" action="{{ $jadwalPertama ? route('pemesanan.form', $jadwalPertama) : '#' }}" id="formPilih">
                    <div class="field mt-16">
                        <label>Pilih Tanggal</label>
                        <select name="jadwal" id="pilihJadwal" required>
                            @forelse($paket->jadwalTersedia as $j)
                                <option value="{{ $j->id }}" data-sisa="{{ $j->sisa_kuota }}">
                                    {{ $j->tanggal_berangkat->translatedFormat('d M Y') }} — sisa {{ $j->sisa_kuota }} kursi
                                </option>
                            @empty
                                <option value="">Belum ada jadwal</option>
                            @endforelse
                        </select>
                    </div>

                    <div class="field mt-16">
                        <label>Pilih Paket</label>
                        <select name="paket_opsi">
                            <option>{{ $paket->tipe_trip }} — {{ $paket->durasi_hari }} Hari</option>
                            <option>Private Group</option>
                        </select>
                    </div>

                    <div class="book-row">
                        <span class="lbl">Dewasa</span>
                        <div class="stepper">
                            <button type="button" onclick="ubahPeserta(-1)">−</button>
                            <span class="n" id="nDewasa">2</span>
                            <button type="button" onclick="ubahPeserta(1)">+</button>
                        </div>
                    </div>
                    <div class="book-row" style="border-bottom:0">
                        <span class="lbl">Anak</span>
                        <div class="stepper">
                            <button type="button" onclick="ubahAnak(-1)">−</button>
                            <span class="n" id="nAnak">0</span>
                            <button type="button" onclick="ubahAnak(1)">+</button>
                        </div>
                    </div>

                    <div class="book-total">
                        <span class="lbl" style="font-size:12px;font-weight:700">Total</span>
                        <span class="v" id="totalHarga">Rp {{ number_format($paket->tarif * 2, 0, ',', '.') }}</span>
                    </div>

                    <input type="hidden" name="jumlah" id="inputJumlah" value="2">

                    @if($jadwalPertama)
                        <a href="{{ auth()->check() ? route('pemesanan.form', ['jadwal' => $jadwalPertama, 'jumlah' => 2]) : route('login') }}"
                           class="btn btn-primary btn-block" id="btnPesan">Pesan Sekarang</a>
                    @else
                        <button class="btn btn-primary btn-block" disabled>Belum ada jadwal</button>
                    @endif

                    <a href="https://wa.me/6281234567890" class="btn btn-ghost btn-block mt-8" style="font-size:12px">💬 Tanya via WhatsApp</a>
                    <div class="book-trust">🔒 Pembayaran aman · Konfirmasi cepat setelah verifikasi</div>
                </form>
            </div>
        </aside>
    </div>
</div>

<script>
const TARIF = {{ (int) $paket->tarif }};
let dewasa = 2, anak = 0;
const fmt = n => 'Rp ' + n.toLocaleString('id-ID');

function render() {
    document.getElementById('nDewasa').textContent = dewasa;
    document.getElementById('nAnak').textContent = anak;
    document.getElementById('totalHarga').textContent = fmt(TARIF * dewasa);
    document.getElementById('inputJumlah').value = dewasa;
    const sel = document.getElementById('pilihJadwal');
    const btn = document.getElementById('btnPesan');
    if (sel && btn && sel.value && btn.tagName === 'A') {
        const tpl = @json(auth()->check() ? route('pemesanan.form', ['jadwal' => 'JADWAL_ID']) : null);
        if (tpl) btn.href = tpl.replace('JADWAL_ID', sel.value) + '?jumlah=' + dewasa;
    }
}
function ubahPeserta(d) { dewasa = Math.max(1, Math.min(20, dewasa + d)); render(); }
function ubahAnak(d) { anak = Math.max(0, Math.min(10, anak + d)); render(); }
render();

document.querySelectorAll('.tabbar button').forEach(b => {
    b.addEventListener('click', () => {
        document.querySelectorAll('.tabbar button').forEach(x => x.classList.remove('active'));
        document.querySelectorAll('.tab-panel').forEach(x => x.classList.remove('active'));
        b.classList.add('active');
        document.getElementById('tab-' + b.dataset.tab).classList.add('active');
    });
});
</script>
@endsection
