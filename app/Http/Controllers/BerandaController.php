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
                    ->orWhereHas('destinasi', fn ($d) => $d->where('nama_destinasi', 'like', "%{$cari}%")
                        ->orWhere('lokasi', 'like', "%{$cari}%"));
            });
        }

        if ($idDestinasi = $request->integer('destinasi')) {
            $query->where('id_destinasi', $idDestinasi);
        }

        if ($request->filled('harga_min')) {
            $query->where('tarif', '>=', (float) $request->input('harga_min'));
        }

        if ($request->filled('harga_max')) {
            $query->where('tarif', '<=', (float) $request->input('harga_max'));
        }

        if ($tanggal = $request->input('tanggal')) {
            $query->whereHas('jadwal', fn ($j) => $j->whereDate('tanggal_berangkat', $tanggal));
        }

        $paket = $query->orderBy('nama_paket')->paginate(9)->withQueryString();

        return view('beranda', [
            'paket' => $paket,
            'destinasi' => Destinasi::orderBy('nama_destinasi')->get(),
        ]);
    }

    public function detail(PaketWisata $paket)
    {
        abort_unless($paket->is_active, 404);

        $paket->load(['destinasi', 'jadwalTersedia']);

        return view('paket.detail', compact('paket'));
    }
}
