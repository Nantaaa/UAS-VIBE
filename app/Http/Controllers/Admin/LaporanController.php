<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\LaporanExcelService;
use App\Services\LaporanService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanController extends Controller
{
    public function __construct(
        private LaporanService $laporan,
        private LaporanExcelService $excelService
    ) {}

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

        return $this->excelService->download($bulan, $tahun);
    }
}
