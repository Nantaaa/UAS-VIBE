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
            <span class="pdp-gallery-btn">{!! view('partials.icon', ['name' => 'camera', 'size' => 15])->render() !!} Lihat semua foto</span>
        </div>
        <div class="pdp-side">
            @foreach($galeri as $i => $g)
                <div>
                    <img src="{{ '/storage/'.$g->foto }}" alt="{{ $g->nama_paket }}">
                    <span class="pdp-gallery-btn" style="font-size:10px">{!! view('partials.icon', ['name' => 'camera', 'size' => 15])->render() !!} {{ $i + 2 }}/12</span>
                </div>
            @endforeach
        </div>
    </div>

    <div class="grid" style="grid-template-columns: 1fr 372px; align-items:start">
        <div>
            <div class="row between" style="gap:12px;align-items:flex-start">
                <div class="pdp-badges">
                    <span class="pdp-badge">Nanta Exclusive</span>
                    @if($paket->is_bestseller)<span class="pdp-badge orange">Best Seller</span>@endif
                </div>
                @auth
                    <form method="POST" action="{{ route('wishlist.toggle', $paket) }}" style="margin:0">
                        @csrf
                        <button type="submit" class="btn btn-ghost btn-sm">
                            {!! view('partials.icon', ['name' => 'heart', 'size' => 14])->render() !!}
                            {{ in_array($paket->id, \App\Models\Wishlist::where('id_user', auth()->id())->pluck('id_paket')->all()) ? 'Tersimpan' : 'Simpan' }}
                        </button>
                    </form>
                @endauth
            </div>

            <h1 class="pdp-title">{{ $paket->nama_paket }}</h1>
            <div class="pdp-loc">{!! view('partials.icon', ['name' => 'map-pin', 'size' => 15])->render() !!} {{ $paket->destinasi->lokasi }}</div>

            <div class="row" style="gap:16px;margin-top:10px">
                <div class="pdp-rating" style="color:var(--teal-dark)">
                    {!! view('partials.icon', ['name' => 'users', 'size' => 15])->render() !!}
                    @if(($paket->peserta_terkonfirmasi ?? 0) > 0)
                        {{ (int) $paket->peserta_terkonfirmasi }} peserta sudah berangkat
                    @else
                        Jadwal baru dibuka
                    @endif
                </div>
                <div class="tiny muted">{{ $paket->tipe_trip }}</div>
            </div>

            <div class="pdp-meta">
                <div>{!! view('partials.icon', ['name' => 'calendar', 'size' => 15])->render() !!} {{ $paket->durasi_hari }} Hari {{ $paket->durasi_hari - 1 }} Malam</div>
                <div>{!! view('partials.icon', ['name' => 'users', 'size' => 15])->render() !!} Min. 2 Peserta</div>
                <div>{!! view('partials.icon', ['name' => 'check-circle', 'size' => 15])->render() !!} Kuota {{ $jadwalPertama->sisa_kuota ?? 0 }} kursi tersisa</div>
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
                <div class="grid grid-2" style="gap:14px;align-items:start">
                    <div class="card card-body">
                        <h4 style="margin:0 0 10px;font-size:13px;color:#15803d">Sudah Termasuk</h4>
                        <ul class="include-list">
                            @forelse(preg_split('/\r\n|\r|\n/', (string) $paket->termasuk) as $t)
                                @continue(trim($t) === '')
                                <li><span class="ico-ok">✓</span><span>{{ trim($t) }}</span></li>
                            @empty
                                <li class="muted">Belum diisi admin.</li>
                            @endforelse
                        </ul>
                    </div>
                    <div class="card card-body">
                        <h4 style="margin:0 0 10px;font-size:13px;color:#b91c1c">Belum Termasuk</h4>
                        <ul class="include-list">
                            @forelse(preg_split('/\r\n|\r|\n/', (string) $paket->tidak_termasuk) as $t)
                                @continue(trim($t) === '')
                                <li><span class="ico-no">×</span><span>{{ trim($t) }}</span></li>
                            @empty
                                <li class="muted">Belum diisi admin.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                @if($paket->fasilitasList())
                    <h4 style="margin:18px 0 10px;font-size:13px">Fasilitas Tambahan</h4>
                    <div class="grid grid-2">
                        @foreach($paket->fasilitasList() as $f)
                            <div class="card card-pad row" style="gap:10px">
                                <span style="color:var(--green);font-weight:800">✓</span>
                                <span style="font-size:12.5px">{{ $f }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
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

            <h3 style="font-size:15px;margin:24px 0 10px">Titik Kumpul &amp; Meeting Point</h3>
            @php($jadwalKumpul = $paket->jadwalTersedia->first())
            <div class="meet-card">
                <div class="meet-head">
                    <span class="meet-ico">{!! view('partials.icon', ['name' => 'map-pin', 'size' => 16])->render() !!}</span>
                    <div>
                        <div class="meet-kota">{{ $jadwalKumpul->kota_kumpul ?? $paket->destinasi->lokasi }}</div>
                        <div class="meet-titik">{{ $jadwalKumpul->titik_kumpul ?? 'Titik kumpul dikonfirmasi admin menjelang keberangkatan.' }}</div>
                    </div>
                </div>

                <div class="meet-grid">
                    <div>
                        <div class="meet-lbl">Jam Kumpul</div>
                        <div class="meet-val">{{ $jadwalKumpul?->jam_kumpul?->format('H:i') ?? '—' }} waktu setempat</div>
                    </div>
                    <div>
                        <div class="meet-lbl">Tiket Pesawat</div>
                        <div class="meet-val">
                            @if($paket->include_penerbangan)
                                <span class="pill-ok">Termasuk</span>
                            @else
                                <span class="pill-no">Tidak termasuk</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <div class="meet-lbl">Titik Awal Paket</div>
                        <div class="meet-val">{{ $jadwalKumpul->kota_kumpul ?? $paket->destinasi->lokasi }}</div>
                    </div>
                </div>

                @if($jadwalKumpul?->catatan_keberangkatan)
                    <div class="meet-note">
                        {!! view('partials.icon', ['name' => 'alert', 'size' => 13])->render() !!}
                        {{ $jadwalKumpul->catatan_keberangkatan }}
                    </div>
                @endif
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
                                <option value="{{ $j->id }}" data-sisa="{{ $j->sisa_kuota }}"
                                        data-kumpul="{{ $j->kota_kumpul }}"
                                        data-titik="{{ $j->titik_kumpul }}"
                                        data-jam="{{ $j->jam_kumpul?->format('H:i') }}">
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

                    <div class="book-meet" id="bookMeet">
                        {!! view('partials.icon', ['name' => 'map-pin', 'size' => 12])->render() !!}
                        <span id="txtMeet">Kumpul di {{ $jadwalKumpul->kota_kumpul ?? $paket->destinasi->lokasi }}{{ $jadwalKumpul?->jam_kumpul ? ', '.$jadwalKumpul->jam_kumpul->format('H:i') : '' }}</span>
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
                        <span class="lbl">Anak <small style="color:var(--muted);font-weight:600">({{ \App\Models\Pemesanan::persenTarifAnak() }}%)</small></span>
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
                        <a href="{{ auth()->check() ? route('pemesanan.form', ['jadwal' => $jadwalPertama, 'dewasa' => 2, 'anak' => 0]) : route('login') }}"
                           class="btn btn-primary btn-block" id="btnPesan">Pesan Sekarang</a>
                    @else
                        <button class="btn btn-primary btn-block" disabled>Belum ada jadwal</button>
                    @endif

                    <a href="https://wa.me/6281234567890" class="btn btn-ghost btn-block mt-8" style="font-size:12px">{!! view('partials.icon', ['name' => 'chat', 'size' => 15])->render() !!} Tanya via WhatsApp</a>
                    <div class="book-trust">{!! view('partials.icon', ['name' => 'lock', 'size' => 15])->render() !!} Pembayaran aman · Konfirmasi cepat setelah verifikasi</div>
                </form>
            </div>
        </aside>
    </div>
</div>

<script>
const TARIF = {{ (int) $paket->tarif }};
const TARIF_ANAK = {{ (int) \App\Models\Pemesanan::tarifKategori((float) $paket->tarif, 'anak') }};
let dewasa = 2, anak = 0;
const fmt = n => 'Rp ' + n.toLocaleString('id-ID');

function render() {
    document.getElementById('nDewasa').textContent = dewasa;
    document.getElementById('nAnak').textContent = anak;
    document.getElementById('totalHarga').textContent = fmt(TARIF * dewasa + TARIF_ANAK * anak);
    document.getElementById('inputJumlah').value = dewasa + anak;
    segarkanTombol();
    const sel = document.getElementById('pilihJadwal');
    const infoMeet = document.getElementById('txtMeet');
    if (sel && infoMeet && sel.selectedOptions.length) {
        const opt = sel.selectedOptions[0];
        const kota = opt.dataset.kumpul || '';
        const jam = opt.dataset.jam || '';
        infoMeet.textContent = 'Kumpul di ' + kota + (jam ? ', ' + jam : '');
        infoMeet.title = opt.dataset.titik || '';
    }
    const btn = document.getElementById('btnPesan');
    if (sel && btn && sel.value && btn.tagName === 'A') {
        const tpl = @json(auth()->check() ? route('pemesanan.form', ['jadwal' => 'JADWAL_ID']) : null);
        if (tpl) btn.href = tpl.replace('JADWAL_ID', sel.value) + '?dewasa=' + dewasa + '&anak=' + anak;
    }
}
function ubahPeserta(d) { dewasa = Math.max(1, Math.min(20, dewasa + d)); render(); }
function ubahAnak(d) { anak = Math.max(0, Math.min(10, anak + d)); render(); }

// Nonaktifkan tombol minus saat sudah di batas bawah
function segarkanTombol() {
    const baris = document.querySelectorAll('.book-row .stepper');
    if (baris[0]) baris[0].querySelector('button').disabled = dewasa <= 1;
    if (baris[1]) baris[1].querySelector('button').disabled = anak <= 0;
}
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
