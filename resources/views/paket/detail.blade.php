@extends('layouts.app')

@section('title', $paket->nama_paket)

@php
    $jadwalPertama = $paket->jadwalTersedia->first();
    // Parse itinerary: tiap baris "Hari N: A - B - C" jadi kartu hari
    $hari = [];
    foreach (preg_split('/
\n|
|\n/', (string) $paket->itinerary) as $baris) {
        $baris = trim($baris);
        if ($baris === '') continue;
        if (preg_match('/^Hari\s*(\d+)\s*[:.-]\s*(.+)$/i', $baris, $m)) {
            $hari[] = ['judul' => 'Hari '.$m[1].': '.$m[2], 'langkah' => []];
        } else {
            if (empty($hari)) {
                $hari[] = ['judul' => 'Jadwal Kegiatan', 'langkah' => []];
            }
            $hari[count($hari) - 1]['langkah'][] = $baris;
        }
    }
    foreach ($hari as $i => $h) {
        if (!isset($h['judul'])) continue;
        $pecah = array_filter(array_map('trim', preg_split('/\s+-\s+/', $h['judul'])));
        if (count($pecah) > 1) {
            $hari[$i]['judul'] = array_shift($pecah);
            $hari[$i]['langkah'] = array_merge($pecah, $h['langkah'] ?? []);
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
            @if($paket->foto)<img src="{{ '/storage/'.$paket->foto }}" alt="{{ $paket->nama_paket }}" id="mainPhoto">@endif
            <span class="pdp-gallery-btn" onclick="bukaModalFoto()" style="cursor:pointer">{!! view('partials.icon', ['name' => 'camera', 'size' => 15])->render() !!} Lihat semua foto</span>
        </div>
        <div class="pdp-side">
            @foreach($galeri as $i => $g)
                <div onclick="gantiFotoUtama('{{ '/storage/'.$g->foto }}')" style="cursor:pointer" title="Klik untuk melihat">
                    <img src="{{ '/storage/'.$g->foto }}" alt="{{ $g->nama_paket }}">
                    <span class="pdp-gallery-btn" style="font-size:10px">{!! view('partials.icon', ['name' => 'camera', 'size' => 15])->render() !!} {{ $i + 2 }}/{{ count($galeri) + 1 }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Modal Galeri Lengkap --}}
    <div id="modalGaleri" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.85);z-index:99999;align-items:center;justify-content:center;padding:20px" onclick="if(event.target===this)tutupModalFoto()">
        <div style="background:#fff;border-radius:12px;max-width:880px;width:100%;max-height:90vh;overflow-y:auto;padding:24px;position:relative">
            <div class="row between mb-16" style="border-bottom:1px solid var(--line);padding-bottom:12px">
                <h3 style="margin:0;font-size:16px">Galeri Destinasi: {{ $paket->nama_paket }}</h3>
                <button type="button" onclick="tutupModalFoto()" style="background:none;border:0;font-size:24px;line-height:1;cursor:pointer">×</button>
            </div>
            <div class="grid grid-2" style="gap:14px">
                @if($paket->foto)
                    <div>
                        <img src="{{ '/storage/'.$paket->foto }}" style="width:100%;height:220px;object-fit:cover;border-radius:8px">
                        <div class="tiny muted mt-4">Foto Utama Paket</div>
                    </div>
                @endif
                @foreach($galeri as $g)
                    <div>
                        <img src="{{ '/storage/'.$g->foto }}" style="width:100%;height:220px;object-fit:cover;border-radius:8px">
                        <div class="tiny muted mt-4">{{ $g->nama_paket }}</div>
                    </div>
                @endforeach
            </div>
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
                <div>{!! view('partials.icon', ['name' => 'users', 'size' => 15])->render() !!} Min. {{ $paket->minPeserta() }} Peserta</div>
                <div>{!! view('partials.icon', ['name' => 'check-circle', 'size' => 15])->render() !!} Kuota {{ $jadwalPertama->sisa_kuota ?? 0 }} kursi tersisa</div>
            </div>

            {{-- Tab --}}
            <div class="tabbar">
                <button type="button" class="active" data-tab="deskripsi">Deskripsi</button>
                <button type="button" data-tab="itinerary">Rencana Perjalanan</button>
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
                <h3 style="font-size:15px;margin-bottom:12px">Rencana Perjalanan (Jadwal Kegiatan Harian)</h3>
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

                @php($titikAktif = $jadwalKumpul?->titikJemput->where('urutan', '<', 99) ?? collect())
                @if($titikAktif->isNotEmpty())
                    <div class="meet-list">
                        <div class="meet-lbl" style="margin-bottom:7px">Pilihan Titik Jemput</div>
                        @foreach($titikAktif as $t)
                            <div class="meet-item">
                                <span class="meet-no">{{ $loop->iteration }}</span>
                                <span class="meet-item-nama">{{ $t->nama }}</span>
                                <span class="meet-item-meta">
                                    @if($t->jam){{ $t->jam->format('H:i') }}@endif
                                    @if((float) $t->biaya_tambahan > 0)
                                        · +Rp {{ number_format($t->biaya_tambahan, 0, ',', '.') }}
                                    @else
                                        · gratis jemput
                                    @endif
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="policy-card">
                <h4>Kebijakan Titik Jemput &amp; Keterlambatan</h4>
                <ul>
                    <li>Penjemputan hanya dilakukan di titik jemput yang tertera. Di luar titik tersebut, peserta menuju sendiri ke titik terdekat dengan biaya sendiri.</li>
                    <li>Peserta wajib berada di titik jemput <strong>15 menit</strong> sebelum jam penjemputan.</li>
                    <li>Keterlambatan melebihi <strong>30 menit</strong> dari jam jemput dianggap menyusul sendiri, tanpa pengembalian biaya.</li>
                    <li>Perubahan titik jemput mendadak oleh operator tidak akan jauh dari titik awal dan akan diinformasikan lewat WhatsApp.</li>
                </ul>
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
                        <label>Pilihan Tipe Perjalanan</label>
                        <select name="opsi" id="pilihOpsiPaket" onchange="gantiOpsiPaket(this.value)">
                            <option value="open">Open Trip — Rp {{ number_format($paket->tarif, 0, ',', '.') }} / org</option>
                            <option value="private">Private Group — Rp {{ number_format($paket->tarifPrivate(), 0, ',', '.') }} / org</option>
                        </select>
                        <div id="descOpsiPaket" class="tiny" style="color:var(--ink-soft);background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:8px 10px;margin-top:6px;line-height:1.45">
                            <strong>Open Trip:</strong> Gabung dengan peserta lain, minimal kuota terpenuhi.
                        </div>
                    </div>

                    <div class="book-meet" id="bookMeet">
                        {!! view('partials.icon', ['name' => 'map-pin', 'size' => 12])->render() !!}
                        <span id="txtMeet">Kumpul di {{ $jadwalKumpul->kota_kumpul ?? $paket->destinasi->lokasi }}{{ $jadwalKumpul?->jam_kumpul ? ', '.$jadwalKumpul->jam_kumpul->format('H:i') : '' }}</span>
                    </div>

                    <div class="book-row">
                        <span class="lbl">Dewasa</span>
                        <div class="stepper">
                            <button type="button" onclick="ubahPeserta(-1)" disabled>−</button>
                            <span class="n" id="nDewasa">{{ $paket->minPeserta() }}</span>
                            <button type="button" onclick="ubahPeserta(1)">+</button>
                        </div>
                    </div>
                    <div class="book-row" style="border-bottom:0">
                        <span class="lbl">Anak <small style="color:var(--muted);font-weight:600">({{ \App\Models\Pemesanan::persenTarifAnak() }}%)</small></span>
                        <div class="stepper">
                            <button type="button" onclick="ubahAnak(-1)" disabled>−</button>
                            <span class="n" id="nAnak">0</span>
                            <button type="button" onclick="ubahAnak(1)">+</button>
                        </div>
                    </div>

                    <div class="book-total">
                        <span class="lbl" style="font-size:12px;font-weight:700">Total</span>
                        <span class="v" id="totalHarga">Rp {{ number_format($paket->tarif * $paket->minPeserta(), 0, ',', '.') }}</span>
                    </div>

                    <input type="hidden" name="jumlah" id="inputJumlah" value="{{ $paket->minPeserta() }}">

                    @if($jadwalPertama)
                        <a href="{{ auth()->check() ? route('pemesanan.form', ['jadwal' => $jadwalPertama, 'dewasa' => $paket->minPeserta(), 'anak' => 0]) : route('login') }}"
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
const TARIF_OPEN = {{ (int) $paket->tarif }};
const TARIF_PRIVATE = {{ (int) $paket->tarifPrivate() }};
const MIN_OPEN = {{ (int) $paket->minPeserta() }};
const MIN_PRIVATE = 2;

let opsiPaket = 'open';
let TARIF = TARIF_OPEN;
let TARIF_ANAK = {{ (int) \App\Models\Pemesanan::tarifKategori((float) $paket->tarif, 'anak') }};
let MIN_TOTAL = MIN_OPEN;
let dewasa = MIN_OPEN, anak = 0;
const fmt = n => 'Rp ' + n.toLocaleString('id-ID');

function gantiOpsiPaket(val) {
    opsiPaket = val;
    const descBox = document.getElementById('descOpsiPaket');
    const priceDisplay = document.querySelector('.book-price');

    if (val === 'private') {
        TARIF = TARIF_PRIVATE;
        MIN_TOTAL = MIN_PRIVATE;
        if (dewasa < MIN_PRIVATE) {
            dewasa = MIN_PRIVATE;
        }
        if (descBox) {
            descBox.innerHTML = '<strong>Private Group:</strong> Eksklusif untuk rombongan Anda sendiri tanpa gabung orang lain, bebas tentukan tanggal fleksibel.';
            descBox.style.background = '#faf5ff';
            descBox.style.borderColor = '#e9d5ff';
            descBox.style.color = '#6b21a8';
        }
        if (priceDisplay) {
            priceDisplay.innerHTML = 'Rp ' + TARIF.toLocaleString('id-ID') + ' <span>/ orang (Private)</span>';
        }
    } else {
        TARIF = TARIF_OPEN;
        MIN_TOTAL = MIN_OPEN;
        if (descBox) {
            descBox.innerHTML = '<strong>Open Trip:</strong> Gabung dengan peserta lain, minimal kuota terpenuhi.';
            descBox.style.background = '#f8fafc';
            descBox.style.borderColor = '#e2e8f0';
            descBox.style.color = 'var(--ink-soft)';
        }
        if (priceDisplay) {
            priceDisplay.innerHTML = 'Rp ' + TARIF.toLocaleString('id-ID') + ' <span>/ orang</span>';
        }
    }
    TARIF_ANAK = Math.round(TARIF * {{ (int) \App\Models\Pemesanan::persenTarifAnak() }} / 100);
    render();
}

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
        if (tpl) btn.href = tpl.replace('JADWAL_ID', sel.value) + '?dewasa=' + dewasa + '&anak=' + anak + '&opsi=' + opsiPaket;
    }
}
function ubahPeserta(d) {
    const minDewasa = Math.max(1, MIN_TOTAL - anak);
    dewasa = Math.max(minDewasa, Math.min(20, dewasa + d));
    render();
}
function ubahAnak(d) {
    const minAnak = Math.max(0, MIN_TOTAL - dewasa);
    anak = Math.max(minAnak, Math.min(10, anak + d));
    render();
}

// Nonaktifkan tombol minus saat sudah di batas bawah (minimal total peserta 2)
function segarkanTombol() {
    const baris = document.querySelectorAll('.book-row .stepper');
    const minDewasa = Math.max(1, MIN_TOTAL - anak);
    const minAnak = Math.max(0, MIN_TOTAL - dewasa);
    if (baris[0]) baris[0].querySelector('button').disabled = (dewasa <= minDewasa);
    if (baris[1]) baris[1].querySelector('button').disabled = (anak <= minAnak);
}
render();

document.getElementById('pilihJadwal')?.addEventListener('change', render);

function gantiFotoUtama(src) {
    const img = document.getElementById('mainPhoto');
    if (img) img.src = src;
}
function bukaModalFoto() {
    const m = document.getElementById('modalGaleri');
    if (m) m.style.display = 'flex';
}
function tutupModalFoto() {
    const m = document.getElementById('modalGaleri');
    if (m) m.style.display = 'none';
}

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
