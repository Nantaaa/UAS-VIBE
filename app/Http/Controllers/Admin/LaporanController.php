<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\LaporanService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanController extends Controller
{
    public function __construct(private LaporanService $laporan) {}

    public function index(Request $request)
    {
        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);

        return view('admin.laporan.index', [
            'rekap' => $this->laporan->rekap($bulan, $tahun),
            'bulan' => $bulan,
            'tahun' => $tahun,
            'daftarTahun' => range(now()->year - 3, now()->year + 1),
        ]);
    }

    public function pdf(Request $request)
    {
        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);

        $pdf = Pdf::loadView('pdf.laporan', [
            'rekap' => $this->laporan->rekap($bulan, $tahun),
        ])->setPaper('a4', 'landscape');

        return $pdf->download("laporan-perjalanan-{$tahun}-{$bulan}.pdf");
    }

    public function excel(Request $request): StreamedResponse
    {
        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);

        $rekap = $this->laporan->rekap($bulan, $tahun);
        $rows = $this->laporan->barisExcel($bulan, $tahun);

        return response()->streamDownload(function () use ($rekap, $rows) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, ['Laporan Perjalanan', $rekap['periode']]);
            fputcsv($out, []);
            fputcsv($out, ['Total Perjalanan', $rekap['jumlah_perjalanan']]);
            fputcsv($out, ['Total Peserta', $rekap['jumlah_peserta']]);
            fputcsv($out, ['Total Pendapatan', $rekap['pendapatan']]);
            fputcsv($out, []);
            fputcsv($out, ['Tanggal', 'Paket', 'Destinasi', 'Kuota', 'Peserta', 'Pendapatan']);

            foreach ($rows as $row) {
                fputcsv($out, $row);
            }

            fclose($out);
        }, "laporan-perjalanan-{$tahun}-{$bulan}.csv", [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
