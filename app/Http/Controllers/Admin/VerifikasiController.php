<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Pemesanan;
use App\Services\VoucherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VerifikasiController extends Controller
{
    public function __construct(private VoucherService $voucher) {}

    public function index(Request $request)
    {
        $status = $request->input('status', 'menunggu_verifikasi');

        $query = Pemesanan::with(['user', 'jadwal.paket.destinasi', 'peserta', 'pembayaranTerakhir']);

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($w) use ($q) {
                $w->where('kode_pemesanan', 'like', "%{$q}%")
                  ->orWhereHas('user', fn ($u) => $u->where('nama_lengkap', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%")->orWhere('no_hp', 'like', "%{$q}%"))
                  ->orWhereHas('jadwal.paket', fn ($p) => $p->where('nama_paket', 'like', "%{$q}%"));
            });
        }

        if ($status === 'perlu_perbaikan') {
            $query->where('status_pemesanan', 'perlu_perbaikan');
        } elseif ($status === 'terkonfirmasi') {
            $query->where('status_pemesanan', 'terkonfirmasi');
        } elseif ($status === 'semua') {
            // semua status
        } else {
            $query->where('status_pemesanan', 'menunggu_verifikasi');
        }

        $daftar = $query->orderBy('tanggal_pemesanan', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.verifikasi.index', [
            'daftar' => $daftar,
            'status' => $status,
            'jumlahMenunggu' => Pemesanan::where('status_pemesanan', 'menunggu_verifikasi')->count(),
            'jumlahPerbaikan' => Pemesanan::where('status_pemesanan', 'perlu_perbaikan')->count(),
            'jumlahTerkonfirmasi' => Pemesanan::where('status_pemesanan', 'terkonfirmasi')->count(),
            'jumlahSemua' => Pemesanan::count(),
        ]);
    }

    public function exportExcel(Request $request): StreamedResponse
    {
        $status = $request->input('status', 'semua');
        $query = Pemesanan::with(['user', 'jadwal.paket.destinasi', 'peserta', 'pembayaran']);

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($w) use ($q) {
                $w->where('kode_pemesanan', 'like', "%{$q}%")
                  ->orWhereHas('user', fn ($u) => $u->where('nama_lengkap', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%")->orWhere('no_hp', 'like', "%{$q}%"))
                  ->orWhereHas('jadwal.paket', fn ($p) => $p->where('nama_paket', 'like', "%{$q}%"));
            });
        }

        if ($status === 'perlu_perbaikan') {
            $query->where('status_pemesanan', 'perlu_perbaikan');
        } elseif ($status === 'terkonfirmasi') {
            $query->where('status_pemesanan', 'terkonfirmasi');
        } elseif ($status === 'menunggu_verifikasi') {
            $query->where('status_pemesanan', 'menunggu_verifikasi');
        }

        $daftar = $query->orderBy('created_at', 'desc')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Verifikasi Pemesanan');
        $sheet->setShowGridLines(true);

        // Header Title
        $sheet->setCellValue('A1', 'PT NANTA EKSPEDISI NUSANTARA — NANTA TRAVEL');
        $sheet->getStyle('A1')->getFont()->setName('Arial')->setSize(14)->setBold(true)->getColor()->setARGB('FF0F2C4A');

        $statusStr = ucfirst(str_replace('_', ' ', $status));
        $sheet->setCellValue('A2', "LOG ANTREAN & VERIFIKASI PEMESANAN (Filter: {$statusStr})");
        $sheet->getStyle('A2')->getFont()->setName('Arial')->setSize(11)->setBold(true)->getColor()->setARGB('FF334155');

        $sheet->setCellValue('A3', 'Total Data: ' . $daftar->count() . ' reservasi | Dicetak: ' . now()->translatedFormat('d F Y H:i') . ' WIB');
        $sheet->getStyle('A3')->getFont()->setName('Arial')->setSize(9)->setItalic(true)->getColor()->setARGB('FF64748B');

        $headers = [
            'A5' => ['No', 6],
            'B5' => ['Kode Booking', 18],
            'C5' => ['Waktu Pesan', 16],
            'D5' => ['Nama Wisatawan', 24],
            'E5' => ['No. WhatsApp', 16],
            'F5' => ['Email', 24],
            'G5' => ['Paket Wisata', 30],
            'H5' => ['Tgl Berangkat', 16],
            'I5' => ['Pax', 10],
            'J5' => ['Total Tagihan (Rp)', 18],
            'K5' => ['Status Verifikasi', 20],
            'L5' => ['KTP Diunggah?', 14],
            'M5' => ['Slip Transfer?', 14],
            'N5' => ['Catatan / Alasan Perbaikan', 32],
            'O5' => ['Waktu Bayar / Konfirmasi', 18],
        ];

        foreach ($headers as $cell => [$label, $width]) {
            $sheet->setCellValue($cell, $label);
            $sheet->getColumnDimension(preg_replace('/[0-9]/', '', $cell))->setWidth($width);
        }

        $headerRange = 'A5:O5';
        $sheet->getStyle($headerRange)->getFont()->setName('Arial')->setSize(9.5)->setBold(true)->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle($headerRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF0F2C4A');
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle($headerRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setARGB('FF0F2C4A');
        $sheet->getRowDimension(5)->setRowHeight(25);

        $row = 6;
        foreach ($daftar as $idx => $p) {
            $ktpUploaded = $p->peserta->filter(fn ($ps) => !empty($ps->file_identitas))->count() . '/' . $p->peserta->count();
            $slipUploaded = $p->pembayaran->filter(fn ($pb) => !empty($pb->bukti_pembayaran))->count() > 0 ? 'Sudah Diunggah' : 'Belum Diunggah';
            $waktuBayar = $p->pembayaran->last()?->created_at?->format('d/m/Y H:i') ?? '—';

            $sheet->setCellValue("A{$row}", $idx + 1);
            $sheet->setCellValue("B{$row}", $p->kode_pemesanan);
            $sheet->setCellValue("C{$row}", $p->created_at->format('d/m/Y H:i'));
            $sheet->setCellValue("D{$row}", $p->user->nama_lengkap ?? '—');
            $sheet->setCellValue("E{$row}", $p->user->no_hp ?? '—');
            $sheet->setCellValue("F{$row}", $p->user->email ?? '—');
            $sheet->setCellValue("G{$row}", $p->jadwal->paket->nama_paket ?? '—');
            $sheet->setCellValue("H{$row}", $p->jadwal->tanggal_berangkat?->format('d/m/Y') ?? '—');
            $sheet->setCellValue("I{$row}", $p->jumlah_peserta);
            $sheet->setCellValue("J{$row}", (float) $p->total_bayar);
            $sheet->setCellValue("K{$row}", ucfirst(str_replace('_', ' ', $p->status_pemesanan)));
            $sheet->setCellValue("L{$row}", $ktpUploaded);
            $sheet->setCellValue("M{$row}", $slipUploaded);
            $sheet->setCellValue("N{$row}", $p->catatan_status ?: '—');
            $sheet->setCellValue("O{$row}", $waktuBayar);

            $sheet->getStyle("A{$row}:C{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("D{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle("E{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("F{$row}:G{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle("H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("I{$row}:J{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("K{$row}:M{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("N{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle("O{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->getStyle("I{$row}")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle("J{$row}")->getNumberFormat()->setFormatCode('"Rp "#,##0');

            if ($idx % 2 == 1) {
                $sheet->getStyle("A{$row}:O{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF8FAFC');
            }

            $sheet->getStyle("A{$row}:O{$row}")->getFont()->setName('Arial')->setSize(9);
            $sheet->getStyle("A{$row}:O{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setARGB('FFE2E8F0');
            $sheet->getRowDimension($row)->setRowHeight(19);

            $row++;
        }

        $sheet->freezePane('A6');

        $filename = "Log-Verifikasi-Pemesanan-Nanta-Travel-" . now()->format('Y-m-d') . ".xlsx";

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'max-age=0',
        ]);
    }

    public function show(Pemesanan $pemesanan)
    {
        $pemesanan->load(['user', 'jadwal.paket.destinasi', 'peserta', 'pembayaran']);

        return view('admin.verifikasi.show', compact('pemesanan'));
    }

    public function setujui(Request $request, Pemesanan $pemesanan)
    {
        if ($pemesanan->status_pemesanan !== 'menunggu_verifikasi') {
            return back()->withErrors(['status' => 'Hanya pemesanan berstatus Menunggu Verifikasi yang dapat dikonfirmasi.']);
        }

        DB::transaction(function () use ($request, $pemesanan) {
            $pembayaran = $pemesanan->pembayaran()->latest('tanggal_upload')->first();

            $pembayaran?->update([
                'status_verifikasi' => 'valid',
                'id_admin_verifikator' => $request->user()->id,
            ]);

            $pemesanan->peserta()->update(['status' => 'valid']);

            $pemesanan->update([
                'status_pemesanan' => 'terkonfirmasi',
                'catatan_status' => 'Dokumen dan pembayaran sesuai. Pemesanan terkonfirmasi, voucher telah diterbitkan.',
            ]);

            $this->voucher->terbitkan($pemesanan);
        });

        return redirect()->route('admin.verifikasi.index')
            ->with('sukses', 'Pemesanan dikonfirmasi dan voucher diterbitkan.');
    }

    public function tolak(Request $request, Pemesanan $pemesanan)
    {
        $data = $request->validate([
            'alasan' => ['required', 'string', 'min:5', 'max:500'],
        ], [
            'alasan.required' => 'Alasan perbaikan wajib diisi agar wisatawan tahu apa yang harus diperbaiki.',
            'alasan.min' => 'Alasan perbaikan terlalu singkat.',
        ]);

        if (! in_array($pemesanan->status_pemesanan, ['menunggu_verifikasi', 'perlu_perbaikan'], true)) {
            return back()->withErrors(['status' => 'Status pemesanan tidak dapat dikembalikan untuk perbaikan.']);
        }

        DB::transaction(function () use ($request, $pemesanan, $data) {
            $pembayaran = $pemesanan->pembayaran()->latest('tanggal_upload')->first();

            $pembayaran?->update([
                'status_verifikasi' => 'tidak_valid',
                'id_admin_verifikator' => $request->user()->id,
            ]);

            $pemesanan->peserta()->update(['status' => 'perlu_perbaikan']);

            $pemesanan->update([
                'status_pemesanan' => 'perlu_perbaikan',
                'catatan_status' => $data['alasan'],
            ]);
        });

        return redirect()->route('admin.verifikasi.index')
            ->with('sukses', 'Pemesanan dikembalikan ke wisatawan untuk perbaikan.');
    }
}
