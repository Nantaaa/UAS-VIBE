<?php

namespace App\Http\Controllers;

use App\Models\Destinasi;
use App\Models\PaketWisata;
use Illuminate\Http\Request;

class BerandaController extends Controller
{
    public function index(Request $request)
    {
        $query = PaketWisata::with(['destinasi', 'jadwalTersedia'])
            ->withSum(['pemesanan as peserta_terkonfirmasi' => fn ($q) => $q->where('status_pemesanan', 'terkonfirmasi')], 'jumlah_peserta')
            ->where('is_active', true);

        if ($cari = $request->string('q')->trim()->toString()) {
            $query->where(function ($q) use ($cari) {
                $q->where('nama_paket', 'like', "%{$cari}%")
                    ->orWhere('highlight', 'like', "%{$cari}%")
                    ->orWhereHas('destinasi', fn ($d) => $d->where('nama_destinasi', 'like', "%{$cari}%")
                        ->orWhere('lokasi', 'like', "%{$cari}%"));
            });
        }

        $destinasiDipilih = array_filter((array) $request->input('destinasi', []));
        if (! empty($destinasiDipilih)) {
            $query->whereIn('id_destinasi', $destinasiDipilih);
        }

        $tipeDipilih = array_filter((array) $request->input('tipe', []));
        if (! empty($tipeDipilih)) {
            $query->whereIn('tipe_trip', $tipeDipilih);
        }

        if ($budget = $request->input('budget')) {
            match ($budget) {
                '1jt' => $query->where('tarif', '<=', 1000000),
                '1-2.5jt' => $query->whereBetween('tarif', [1000000, 2500000]),
                '2.5-5jt' => $query->whereBetween('tarif', [2500000, 5000000]),
                '5jt' => $query->where('tarif', '>=', 5000000),
                default => null,
            };
        }

        if ($request->filled('harga_min')) {
            $query->where('tarif', '>=', (float) $request->input('harga_min'));
        }

        if ($request->filled('harga_max')) {
            $query->where('tarif', '<=', (float) $request->input('harga_max'));
        }

        if ($durasi = array_filter((array) $request->input('durasi', []))) {
            $query->whereIn('durasi_hari', array_map('intval', $durasi));
        }

        if ($tanggal = $request->input('tanggal')) {
            $query->whereHas('jadwal', fn ($j) => $j->whereDate('tanggal_berangkat', $tanggal));
        }

        $query = match ($request->input('urutkan')) {
            'termurah' => $query->orderBy('tarif'),
            'termahal' => $query->orderByDesc('tarif'),
            'terbaru' => $query->latest(),
            default => $query->orderByDesc('is_bestseller')->orderBy('nama_paket'),
        };

        $perHalaman = (int) $request->input('per_halaman', 12);
        $perHalaman = in_array($perHalaman, [6, 9, 12, 18, 24], true) ? $perHalaman : 12;

        $wishlistIds = auth()->check()
            ? \App\Models\Wishlist::where('id_user', auth()->id())->pluck('id_paket')->all()
            : [];

        return view('beranda', [
            'paket' => $query->paginate($perHalaman)->withQueryString(),
            'wishlistIds' => $wishlistIds,
            'destinasi' => Destinasi::withCount('paket')->orderBy('nama_destinasi')->get(),
            'tipeTrip' => PaketWisata::query()->distinct()->orderBy('tipe_trip')->pluck('tipe_trip'),
            'totalPaket' => PaketWisata::where('is_active', true)->count(),
        ]);
    }

    public function detail(PaketWisata $paket)
    {
        abort_unless($paket->is_active, 404);

        $paket->load(['destinasi', 'jadwalTersedia']);
        $paket->loadSum(['pemesanan as peserta_terkonfirmasi' => fn ($q) => $q->where('status_pemesanan', 'terkonfirmasi')], 'jumlah_peserta');

        $galeri = \App\Models\PaketWisata::where('id', '!=', $paket->id)
            ->where('id_destinasi', $paket->id_destinasi)
            ->whereNotNull('foto')
            ->where('is_active', true)
            ->limit(2)
            ->get(['foto', 'nama_paket']);

        if ($galeri->count() < 2) {
            $sisa = 2 - $galeri->count();
            $tambahan = \App\Models\PaketWisata::where('id', '!=', $paket->id)
                ->whereNotIn('id', $galeri->pluck('id')->all())
                ->where('tipe_trip', $paket->tipe_trip)
                ->whereNotNull('foto')
                ->where('is_active', true)
                ->limit($sisa)
                ->get(['foto', 'nama_paket']);
            $galeri = $galeri->concat($tambahan);
        }

        return view('paket.detail', compact('paket', 'galeri'));
    }

    public function syarat()
    {
        return view('pages.syarat');
    }

    public function privasi()
    {
        return view('pages.privasi');
    }

    public function faq()
    {
        return view('pages.faq');
    }
}
