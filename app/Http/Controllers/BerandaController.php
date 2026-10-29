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

        $perHalaman = (int) $request->input('per_halaman', 6);
        $perHalaman = in_array($perHalaman, [6, 9, 12], true) ? $perHalaman : 6;

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

        $galeri = \App\Models\PaketWisata::where('id', '!=', $paket->id)
            ->whereNotNull('foto')
            ->where('is_active', true)
            ->inRandomOrder()
            ->limit(2)
            ->get(['foto', 'nama_paket']);

        return view('paket.detail', compact('paket', 'galeri'));
    }
}
