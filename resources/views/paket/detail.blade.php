@extends('layouts.app')

@section('title', $paket->nama_paket)

@section('content')
<div class="container" style="padding:28px 20px 0">
    <div class="tiny muted mb-16">
        <a href="{{ route('beranda') }}">Beranda</a> ›
        <a href="{{ route('beranda', ['destinasi' => $paket->id_destinasi]) }}">{{ $paket->destinasi->nama_destinasi }}</a> ›
        <span>{{ $paket->nama_paket }}</span>
    </div>

    <div class="grid" style="grid-template-columns: 1.6fr 1fr; align-items:start">
        <div class="stack">
            <div class="card" style="overflow:hidden">
                <div class="paket-thumb" style="aspect-ratio:16/8">
                    @if($paket->foto)
                        <img src="{{ Storage::disk('public')->url($paket->foto) }}" alt="{{ $paket->nama_paket }}">
                    @endif
                    <span class="tag">{{ $paket->destinasi->nama_destinasi }}</span>
                    <span class="durasi">{{ $paket->durasi_hari }} Hari {{ $paket->durasi_hari - 1 }} Malam</span>
                </div>
                <div class="card-body">
                    <h1 style="font-size:24px">{{ $paket->nama_paket }}</h1>
                    <div class="paket-loc mb-16">📍 {{ $paket->destinasi->lokasi }}</div>
                    <p class="muted">{{ $paket->deskripsi }}</p>
                </div>
            </div>

            <div class="card">
                <div class="card-head"><h3>Fasilitas termasuk</h3></div>
                <div class="card-body">
                    <div class="grid grid-2" style="gap:10px">
                        @foreach($paket->fasilitasList() as $f)
                            <div class="row" style="gap:9px;align-items:flex-start">
                                <span style="color:var(--green);font-weight:800">✓</span>
                                <span style="font-size:13.5px">{{ $f }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            @if($paket->itinerary)
                <div class="card">
                    <div class="card-head"><h3>Rencana perjalanan</h3></div>
                    <div class="card-body">
                        <ul class="timeline">
                            @foreach(preg_split('/\r\n|\r|\n/', $paket->itinerary) as $i => $baris)
                                @continue(trim($baris) === '')
                                <li class="{{ $loop->last ? 'current' : 'done' }}">
                                    <span class="dot"></span>
                                    <div>
                                        <div style="font-weight:700;font-size:13.5px">{{ $baris }}</div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>

        <div class="stack" style="position:sticky;top:88px">
            <div class="card card-pad">
                <div class="tiny muted">Harga per peserta</div>
                <div style="font-size:28px;font-weight:800;color:var(--primary-dark);letter-spacing:-.02em">
                    Rp {{ number_format($paket->tarif, 0, ',', '.') }}
                </div>

                <hr style="border:0;border-top:1px solid var(--line);margin:16px 0">

                <h3 style="font-size:14px">Pilih jadwal keberangkatan</h3>

                @forelse($paket->jadwalTersedia as $j)
                    <div class="card" style="padding:13px 14px;margin-bottom:10px;box-shadow:none">
                        <div class="row between">
                            <div>
                                <div style="font-weight:800;font-size:13.5px">
                                    {{ $j->tanggal_berangkat->translatedFormat('d M Y') }}
                                </div>
                                <div class="tiny muted">
                                    s/d {{ $j->tanggal_kembali?->translatedFormat('d M Y') ?? '-' }}
                                </div>
                            </div>
                            <div class="right">
                                @if($j->sisa_kuota > 0)
                                    <span class="badge badge-success">{{ $j->sisa_kuota }} kursi</span>
                                @else
                                    <span class="badge badge-danger">Penuh</span>
                                @endif
                            </div>
                        </div>

                        @if($j->sisa_kuota > 0)
                            <a href="{{ auth()->check() ? route('pemesanan.form', $j) : route('login') }}"
                               class="btn btn-primary btn-block btn-sm mt-8">Pesan jadwal ini</a>
                        @else
                            <button class="btn btn-ghost btn-block btn-sm mt-8" disabled>Kuota penuh</button>
                        @endif
                    </div>
                @empty
                    <div class="alert alert-info mb-0">Belum ada jadwal keberangkatan tersedia untuk paket ini.</div>
                @endforelse

                <div class="tiny muted mt-16">
                    Kuota ditahan sementara begitu pemesanan dibuat. Batas pembayaran 1 jam.
                </div>
            </div>

            <div class="card card-pad">
                <h3 style="font-size:14px">Butuh bantuan?</h3>
                <p class="tiny muted mb-8">Hubungi admin travel kami pada jam kerja 08.00–20.00 WIB.</p>
                <div class="tiny" style="font-weight:700">📞 0812-3456-7890</div>
            </div>
        </div>
    </div>
</div>
@endsection
