<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JadwalKeberangkatan;
use App\Models\Pemesanan;
use App\Models\Penugasan;
use App\Models\Petugas;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ManifesController extends Controller
{
    public function index()
    {
        $jadwal = JadwalKeberangkatan::with(['paket.destinasi', 'penugasan.petugas'])
            ->withCount(['pemesanan as peserta_terkonfirmasi' => fn ($q) => $q->where('status_pemesanan', 'terkonfirmasi')])
            ->orderByDesc('tanggal_berangkat')
            ->paginate(12);

        return view('admin.manifes.index', compact('jadwal'));
    }

    public function show(JadwalKeberangkatan $jadwal)
    {
        $jadwal->load(['paket.destinasi', 'penugasan.petugas']);

        $pemesanan = Pemesanan::with(['user', 'peserta'])
            ->where('id_jadwal', $jadwal->id)
            ->where('status_pemesanan', 'terkonfirmasi')
            ->orderBy('kode_pemesanan')
            ->get();

        $daftarPetugas = Petugas::orderBy('nama_petugas')->get();

        return view('admin.manifes.show', [
            'jadwal' => $jadwal,
            'pemesanan' => $pemesanan,
            'totalPeserta' => (int) $pemesanan->sum('jumlah_peserta'),
            'daftarPetugas' => $daftarPetugas,
        ]);
    }

    public function updatePetugas(Request $request, JadwalKeberangkatan $jadwal)
    {
        $ids = collect($request->input('petugas', []))->filter()->map(fn ($id) => (int) $id)->all();

        Penugasan::where('id_jadwal', $jadwal->id)->whereNotIn('id_petugas', $ids ?: [0])->delete();

        foreach ($ids as $idPetugas) {
            Penugasan::firstOrCreate(
                ['id_jadwal' => $jadwal->id, 'id_petugas' => $idPetugas],
                ['tanggal_tugas' => $jadwal->tanggal_berangkat]
            );
        }

        return back()->with('sukses', 'Penugasan kru / petugas pelayaran berhasil diperbarui.');
    }

    public function pdf(JadwalKeberangkatan $jadwal)
    {
        $jadwal->load(['paket.destinasi', 'penugasan.petugas']);

        $pemesanan = Pemesanan::with(['user', 'peserta'])
            ->where('id_jadwal', $jadwal->id)
            ->where('status_pemesanan', 'terkonfirmasi')
            ->orderBy('kode_pemesanan')
            ->get();

        $pdf = Pdf::loadView('pdf.manifes', [
            'jadwal' => $jadwal,
            'pemesanan' => $pemesanan,
            'totalPeserta' => (int) $pemesanan->sum('jumlah_peserta'),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('manifes-'.$jadwal->id.'-'.$jadwal->tanggal_berangkat->format('Ymd').'.pdf');
    }
}
