<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Destinasi;
use App\Models\JadwalKeberangkatan;
use App\Models\Pemesanan;
use App\Services\LaporanService;

class DashboardController extends Controller
{
    public function index(LaporanService $laporan)
    {
        $bulanIni = $laporan->rekap(now()->month, now()->year);

        return view('admin.dashboard', [
            'menungguVerifikasi' => Pemesanan::where('status_pemesanan', 'menunggu_verifikasi')->count(),
            'perluPerbaikan' => Pemesanan::where('status_pemesanan', 'perlu_perbaikan')->count(),
            'terkonfirmasiBulanIni' => $bulanIni['pemesanan_terkonfirmasi'],
            'pendapatanBulanIni' => $bulanIni['pendapatan'],
            'totalDestinasi' => Destinasi::count(),
            'keberangkatanTerdekat' => JadwalKeberangkatan::with(['paket.destinasi'])
                ->whereDate('tanggal_berangkat', '>=', now()->toDateString())
                ->orderBy('tanggal_berangkat')
                ->limit(5)
                ->get(),
            'pemesananTerbaru' => Pemesanan::with(['user', 'jadwal.paket'])
                ->latest('tanggal_pemesanan')
                ->limit(6)
                ->get(),
            'grafik' => $this->grafikPendapatan(),
        ]);
    }

    private function grafikPendapatan(): array
    {
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $waktu = now()->subMonths($i);
            $total = Pemesanan::where('status_pemesanan', 'terkonfirmasi')
                ->whereYear('tanggal_pemesanan', $waktu->year)
                ->whereMonth('tanggal_pemesanan', $waktu->month)
                ->sum('total_bayar');

            $data[] = [
                'label' => $waktu->translatedFormat('M'),
                'nilai' => (float) $total,
            ];
        }

        return $data;
    }
}
