<?php

namespace App\Services;

use App\Models\JadwalKeberangkatan;
use App\Models\Pemesanan;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LaporanService
{
    public function rekap(int $bulan, int $tahun): array
    {
        $mulai = now()->setDate($tahun, $bulan, 1)->startOfDay();
        $selesai = (clone $mulai)->endOfMonth()->endOfDay();

        $jadwal = JadwalKeberangkatan::with(['paket.destinasi'])
            ->whereBetween('tanggal_berangkat', [$mulai->toDateString(), $selesai->toDateString()])
            ->orderBy('tanggal_berangkat')
            ->get();

        $pemesanan = Pemesanan::with(['user', 'jadwal.paket.destinasi'])
            ->whereHas('jadwal', fn ($q) => $q->whereBetween('tanggal_berangkat', [$mulai->toDateString(), $selesai->toDateString()]))
            ->get();

        $terkonfirmasi = $pemesanan->where('status_pemesanan', 'terkonfirmasi');

        return [
            'periode' => $mulai->translatedFormat('F Y'),
            'bulan' => $bulan,
            'tahun' => $tahun,
            'jumlah_perjalanan' => $jadwal->count(),
            'jumlah_peserta' => (int) $terkonfirmasi->sum('jumlah_peserta'),
            'pendapatan' => (float) $terkonfirmasi->sum('total_bayar'),
            'jumlah_pemesanan' => $pemesanan->count(),
            'pemesanan_terkonfirmasi' => $terkonfirmasi->count(),
            'jadwal' => $jadwal,
            'detail' => $this->detailPerJadwal($jadwal),
        ];
    }

    private function detailPerJadwal(Collection $jadwal): Collection
    {
        return $jadwal->map(function (JadwalKeberangkatan $j) {
            $p = Pemesanan::where('id_jadwal', $j->id)->get();
            $konfirm = $p->where('status_pemesanan', 'terkonfirmasi');

            return [
                'tanggal' => $j->tanggal_berangkat,
                'paket' => $j->paket->nama_paket,
                'destinasi' => $j->paket->destinasi->nama_destinasi,
                'kuota' => $j->kuota_peserta,
                'peserta' => (int) $konfirm->sum('jumlah_peserta'),
                'pendapatan' => (float) $konfirm->sum('total_bayar'),
                'status_pemesanan' => $p->groupBy('status_pemesanan')->map->count()->all(),
            ];
        });
    }

    public function barisExcel(int $bulan, int $tahun): array
    {
        $rekap = $this->rekap($bulan, $tahun);

        $rows = [];
        foreach ($rekap['detail'] as $d) {
            $rows[] = [
                $d['tanggal']->format('d/m/Y'),
                $d['paket'],
                $d['destinasi'],
                $d['kuota'],
                $d['peserta'],
                $d['pendapatan'],
            ];
        }

        return $rows;
    }
}
