<?php

namespace App\Services;

use App\Models\JadwalKeberangkatan;
use App\Models\Pemesanan;
use App\Models\Peserta;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanExcelService
{
    public function download(int $bulan, int $tahun): StreamedResponse
    {
        $spreadsheet = $this->buildSpreadsheet($bulan, $tahun);
        $periodeNama = now()->setDate($tahun, $bulan, 1)->translatedFormat('F-Y');
        $filename = "Laporan-Perjalanan-Nanta-Travel-{$periodeNama}.xlsx";

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'max-age=0',
        ]);
    }

    public function buildSpreadsheet(int $bulan, int $tahun): Spreadsheet
    {
        $mulai = now()->setDate($tahun, $bulan, 1)->startOfDay();
        $selesai = (clone $mulai)->endOfMonth()->endOfDay();
        $periodeStr = $mulai->translatedFormat('F Y');

        // Query Data
        $jadwalList = JadwalKeberangkatan::with(['paket.destinasi', 'penugasan.petugas', 'pemesanan'])
            ->whereBetween('tanggal_berangkat', [$mulai->toDateString(), $selesai->toDateString()])
            ->orderBy('tanggal_berangkat')
            ->get();

        $pemesananList = Pemesanan::with(['user', 'jadwal.paket.destinasi', 'titikJemput', 'pembayaran'])
            ->whereHas('jadwal', fn ($q) => $q->whereBetween('tanggal_berangkat', [$mulai->toDateString(), $selesai->toDateString()]))
            ->orderBy('created_at', 'desc')
            ->get();

        $pesertaList = Peserta::with(['pemesanan.jadwal.paket', 'pemesanan.user'])
            ->whereHas('pemesanan.jadwal', fn ($q) => $q->whereBetween('tanggal_berangkat', [$mulai->toDateString(), $selesai->toDateString()]))
            ->orderBy('id', 'asc')
            ->get();

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator('Nanta Travel System')
            ->setLastModifiedBy('Administrator Nanta')
            ->setTitle("Laporan Perjalanan {$periodeStr}")
            ->setSubject("Rekapitulasi Operasional & Keuangan {$periodeStr}")
            ->setDescription("Laporan resmi bulanan Nanta Travel periode {$periodeStr}");

        // SHEET 1: REKAP KEBERANGKATAN
        $this->buildSheetJadwal($spreadsheet->getActiveSheet(), $jadwalList, $periodeStr);

        // SHEET 2: RINCIAN PEMESANAN
        $sheet2 = $spreadsheet->createSheet();
        $this->buildSheetPemesanan($sheet2, $pemesananList, $periodeStr);

        // SHEET 3: MANIFEST PENUMPANG
        $sheet3 = $spreadsheet->createSheet();
        $this->buildSheetManifest($sheet3, $pesertaList, $periodeStr);

        // Set active sheet ke sheet 1
        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;
    }

    private function buildSheetJadwal($sheet, $jadwalList, string $periodeStr): void
    {
        $sheet->setTitle('Rekap Keberangkatan');
        $sheet->setShowGridLines(true);

        // Header Title
        $sheet->setCellValue('A1', 'PT NANTA EKSPEDISI NUSANTARA — NANTA TRAVEL');
        $sheet->getStyle('A1')->getFont()->setName('Arial')->setSize(14)->setBold(true)->getColor()->setARGB('FF0F2C4A');

        $sheet->setCellValue('A2', 'LAPORAN REKAPITULASI OPERASIONAL & PENDAPATAN JADWAL KEBERANGKATAN');
        $sheet->getStyle('A2')->getFont()->setName('Arial')->setSize(11)->setBold(true)->getColor()->setARGB('FF334155');

        $sheet->setCellValue('A3', "Periode: {$periodeStr} | Tanggal Cetak: " . now()->translatedFormat('d F Y H:i') . ' WIB | Dibuat oleh Admin');
        $sheet->getStyle('A3')->getFont()->setName('Arial')->setSize(9)->setItalic(true)->getColor()->setARGB('FF64748B');

        // KPI Ringkasan Box (Baris 5)
        $totalJadwal = $jadwalList->count();
        $totalKuota = $jadwalList->sum('kuota_peserta');
        $totalPeserta = 0;
        $totalOmset = 0;

        foreach ($jadwalList as $j) {
            $konfirm = $j->pemesanan->where('status_pemesanan', 'terkonfirmasi');
            $totalPeserta += $konfirm->sum('jumlah_peserta');
            $totalOmset += $konfirm->sum('total_bayar');
        }
        $avgOkupansi = $totalKuota > 0 ? round(($totalPeserta / $totalKuota) * 100, 1) : 0;

        $sheet->setCellValue('A5', 'TOTAL JADWAL');
        $sheet->setCellValue('A6', $totalJadwal . ' Keberangkatan');
        $sheet->setCellValue('D5', 'TOTAL KAPASITAS KUOTA');
        $sheet->setCellValue('D6', $totalKuota . ' Kursi');
        $sheet->setCellValue('G5', 'PESERTA TERKONFIRMASI');
        $sheet->setCellValue('G6', $totalPeserta . ' Pax (' . $avgOkupansi . '%)');
        $sheet->setCellValue('K5', 'TOTAL OMSET PENDAPATAN');
        $sheet->setCellValue('K6', 'Rp ' . number_format($totalOmset, 0, ',', '.'));

        // Styling KPI Box
        foreach (['A', 'D', 'G', 'K'] as $col) {
            $nextCol = chr(ord($col) + 2);
            $sheet->mergeCells("{$col}5:{$nextCol}5");
            $sheet->mergeCells("{$col}6:{$nextCol}6");
            $sheet->getStyle("{$col}5:{$nextCol}6")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF1F5F9');
            $sheet->getStyle("{$col}5:{$nextCol}6")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setARGB('FFCBD5E1');
            $sheet->getStyle("{$col}5")->getFont()->setName('Arial')->setSize(8.5)->setBold(true)->getColor()->setARGB('FF64748B');
            $sheet->getStyle("{$col}6")->getFont()->setName('Arial')->setSize(12)->setBold(true)->getColor()->setARGB('FF0F2C4A');
            $sheet->getStyle("{$col}5:{$nextCol}6")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        }

        // Table Header (Baris 8)
        $headers = [
            'A8' => ['No', 6, Alignment::HORIZONTAL_CENTER],
            'B8' => ['ID Jadwal', 11, Alignment::HORIZONTAL_CENTER],
            'C8' => ['Tgl Berangkat', 14, Alignment::HORIZONTAL_CENTER],
            'D8' => ['Tgl Kembali', 14, Alignment::HORIZONTAL_CENTER],
            'E8' => ['Durasi', 10, Alignment::HORIZONTAL_CENTER],
            'F8' => ['Nama Paket Wisata', 34, Alignment::HORIZONTAL_LEFT],
            'G8' => ['Destinasi', 20, Alignment::HORIZONTAL_LEFT],
            'H8' => ['Pemandu / Kru Kapal', 24, Alignment::HORIZONTAL_LEFT],
            'I8' => ['Kuota', 10, Alignment::HORIZONTAL_RIGHT],
            'J8' => ['Terisi', 11, Alignment::HORIZONTAL_RIGHT],
            'K8' => ['Sisa', 10, Alignment::HORIZONTAL_RIGHT],
            'L8' => ['Okupansi', 12, Alignment::HORIZONTAL_RIGHT],
            'M8' => ['Status', 14, Alignment::HORIZONTAL_CENTER],
            'N8' => ['Pendapatan (Rp)', 18, Alignment::HORIZONTAL_RIGHT],
        ];

        foreach ($headers as $cell => [$label, $width, $align]) {
            $sheet->setCellValue($cell, $label);
            $colLetter = preg_replace('/[0-9]/', '', $cell);
            $sheet->getColumnDimension($colLetter)->setWidth($width);
        }

        $headerRange = 'A8:N8';
        $sheet->getStyle($headerRange)->getFont()->setName('Arial')->setSize(9.5)->setBold(true)->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle($headerRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF0F2C4A');
        $sheet->getStyle($headerRange)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle($headerRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setARGB('FF0F2C4A');
        $sheet->getRowDimension(8)->setRowHeight(26);

        // Data Rows
        $row = 9;
        foreach ($jadwalList as $idx => $j) {
            $konfirm = $j->pemesanan->where('status_pemesanan', 'terkonfirmasi');
            $paxTerisi = (int) $konfirm->sum('jumlah_peserta');
            $omset = (float) $konfirm->sum('total_bayar');
            $sisa = max(0, $j->kuota_peserta - $paxTerisi);
            $pct = $j->kuota_peserta > 0 ? ($paxTerisi / $j->kuota_peserta) : 0;
            $kru = $j->penugasan->map(fn ($p) => $p->petugas->nama_petugas)->join(', ') ?: '—';

            $statusText = 'Mendatang';
            if ($j->tanggal_berangkat->isPast()) {
                $statusText = 'Selesai';
            } elseif ($sisa == 0) {
                $statusText = 'Penuh';
            }

            $sheet->setCellValue("A{$row}", $idx + 1);
            $sheet->setCellValue("B{$row}", "#{$j->id}");
            $sheet->setCellValue("C{$row}", $j->tanggal_berangkat->format('d/m/Y'));
            $sheet->setCellValue("D{$row}", $j->tanggal_kembali?->format('d/m/Y') ?? '—');
            $sheet->setCellValue("E{$row}", "{$j->paket->durasi_hari} Hari");
            $sheet->setCellValue("F{$row}", $j->paket->nama_paket);
            $sheet->setCellValue("G{$row}", $j->paket->destinasi->nama_destinasi);
            $sheet->setCellValue("H{$row}", $kru);
            $sheet->setCellValue("I{$row}", $j->kuota_peserta);
            $sheet->setCellValue("J{$row}", $paxTerisi);
            $sheet->setCellValue("K{$row}", $sisa);
            $sheet->setCellValue("L{$row}", $pct);
            $sheet->setCellValue("M{$row}", $statusText);
            $sheet->setCellValue("N{$row}", $omset);

            // Number formats
            $sheet->getStyle("I{$row}:K{$row}")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle("L{$row}")->getNumberFormat()->setFormatCode('0.0%');
            $sheet->getStyle("N{$row}")->getNumberFormat()->setFormatCode('"Rp "#,##0');

            // Alignments
            $sheet->getStyle("A{$row}:E{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("F{$row}:H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle("I{$row}:L{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("M{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("N{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            // Zebra striping
            if ($idx % 2 == 1) {
                $sheet->getStyle("A{$row}:N{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF8FAFC');
            }

            $sheet->getStyle("A{$row}:N{$row}")->getFont()->setName('Arial')->setSize(9);
            $sheet->getStyle("A{$row}:N{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setARGB('FFE2E8F0');
            $sheet->getRowDimension($row)->setRowHeight(20);

            $row++;
        }

        // Summary Total Row
        $sheet->setCellValue("A{$row}", 'TOTAL KESELURUHAN');
        $sheet->mergeCells("A{$row}:H{$row}");
        $sheet->setCellValue("I{$row}", "=SUM(I9:I" . ($row - 1) . ")");
        $sheet->setCellValue("J{$row}", "=SUM(J9:J" . ($row - 1) . ")");
        $sheet->setCellValue("K{$row}", "=SUM(K9:K" . ($row - 1) . ")");
        $sheet->setCellValue("L{$row}", "=IF(I{$row}>0, J{$row}/I{$row}, 0)");
        $sheet->setCellValue("M{$row}", "—");
        $sheet->setCellValue("N{$row}", "=SUM(N9:N" . ($row - 1) . ")");

        $totalRange = "A{$row}:N{$row}";
        $sheet->getStyle($totalRange)->getFont()->setName('Arial')->setSize(9.5)->setBold(true)->getColor()->setARGB('FF0F2C4A');
        $sheet->getStyle($totalRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFE2E8F0');
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("M{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("I{$row}:K{$row}")->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle("L{$row}")->getNumberFormat()->setFormatCode('0.0%');
        $sheet->getStyle("N{$row}")->getNumberFormat()->setFormatCode('"Rp "#,##0');
        $sheet->getStyle($totalRange)->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN)->getColor()->setARGB('FF64748B');
        $sheet->getStyle($totalRange)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_DOUBLE)->getColor()->setARGB('FF0F2C4A');
        $sheet->getRowDimension($row)->setRowHeight(24);

        $sheet->freezePane('A9');
    }

    private function buildSheetPemesanan($sheet, $pemesananList, string $periodeStr): void
    {
        $sheet->setTitle('Rincian Transaksi');
        $sheet->setShowGridLines(true);

        $sheet->setCellValue('A1', 'PT NANTA EKSPEDISI NUSANTARA — BUKU TRANSAKSI PEMESANAN');
        $sheet->getStyle('A1')->getFont()->setName('Arial')->setSize(13)->setBold(true)->getColor()->setARGB('FF0F2C4A');

        $sheet->setCellValue('A2', "Periode: {$periodeStr} | Rincian Seluruh Transaksi Reservasi Wisatawan");
        $sheet->getStyle('A2')->getFont()->setName('Arial')->setSize(10)->getColor()->setARGB('FF64748B');

        // Header Row (Baris 4)
        $headers = [
            'A4' => ['No', 6],
            'B4' => ['Kode Booking', 18],
            'C4' => ['Tgl Pesan', 16],
            'D4' => ['Nama Pemesan', 24],
            'E4' => ['No. WhatsApp', 16],
            'F4' => ['Email Pemesan', 24],
            'G4' => ['Paket Wisata', 30],
            'H4' => ['Tipe Trip', 14],
            'I4' => ['Titik Jemput', 22],
            'J4' => ['Dewasa', 10],
            'K4' => ['Anak', 10],
            'L4' => ['Total Pax', 11],
            'M4' => ['Biaya Jemput (Rp)', 16],
            'N4' => ['Total Bayar (Rp)', 18],
            'O4' => ['Status Pemesanan', 18],
            'P4' => ['Waktu Bayar', 16],
        ];

        foreach ($headers as $cell => [$label, $width]) {
            $sheet->setCellValue($cell, $label);
            $sheet->getColumnDimension(preg_replace('/[0-9]/', '', $cell))->setWidth($width);
        }

        $headerRange = 'A4:P4';
        $sheet->getStyle($headerRange)->getFont()->setName('Arial')->setSize(9.5)->setBold(true)->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle($headerRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF0F2C4A');
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle($headerRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setARGB('FF0F2C4A');
        $sheet->getRowDimension(4)->setRowHeight(25);

        $row = 5;
        foreach ($pemesananList as $idx => $order) {
            $tipe = ($order->jumlah_peserta < 2 && $order->biaya_jemput == 0) ? 'Open Trip' : ($order->total_bayar > ($order->jadwal->paket->tarif * $order->jumlah_peserta) ? 'Private Group' : 'Open Trip');
            $statusLabel = ucfirst(str_replace('_', ' ', $order->status_pemesanan));
            $waktuBayar = $order->pembayaran->last()?->created_at?->format('d/m/Y H:i') ?? '—';

            $sheet->setCellValue("A{$row}", $idx + 1);
            $sheet->setCellValue("B{$row}", $order->kode_pemesanan);
            $sheet->setCellValue("C{$row}", $order->created_at->format('d/m/Y H:i'));
            $sheet->setCellValue("D{$row}", $order->user->nama_lengkap ?? '—');
            $sheet->setCellValue("E{$row}", $order->user->no_hp ?? '—');
            $sheet->setCellValue("F{$row}", $order->user->email ?? '—');
            $sheet->setCellValue("G{$row}", $order->jadwal->paket->nama_paket ?? '—');
            $sheet->setCellValue("H{$row}", $tipe);
            $sheet->setCellValue("I{$row}", $order->titikJemput?->lokasi_jemput ?? 'Titik Kumpul Utama');
            $sheet->setCellValue("J{$row}", $order->jumlah_dewasa ?? $order->jumlah_peserta);
            $sheet->setCellValue("K{$row}", $order->jumlah_anak ?? 0);
            $sheet->setCellValue("L{$row}", $order->jumlah_peserta);
            $sheet->setCellValue("M{$row}", (float) ($order->biaya_jemput ?? 0));
            $sheet->setCellValue("N{$row}", (float) $order->total_bayar);
            $sheet->setCellValue("O{$row}", $statusLabel);
            $sheet->setCellValue("P{$row}", $waktuBayar);

            // Alignments & Formats
            $sheet->getStyle("A{$row}:C{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("D{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle("E{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("F{$row}:G{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle("H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("I{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle("J{$row}:L{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("M{$row}:N{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("O{$row}:P{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->getStyle("J{$row}:L{$row}")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle("M{$row}:N{$row}")->getNumberFormat()->setFormatCode('"Rp "#,##0');

            if ($idx % 2 == 1) {
                $sheet->getStyle("A{$row}:P{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF8FAFC');
            }

            $sheet->getStyle("A{$row}:P{$row}")->getFont()->setName('Arial')->setSize(9);
            $sheet->getStyle("A{$row}:P{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setARGB('FFE2E8F0');
            $sheet->getRowDimension($row)->setRowHeight(19);

            $row++;
        }

        // Total Row
        if ($pemesananList->count() > 0) {
            $sheet->setCellValue("A{$row}", 'TOTAL');
            $sheet->mergeCells("A{$row}:I{$row}");
            $sheet->setCellValue("J{$row}", "=SUM(J5:J" . ($row - 1) . ")");
            $sheet->setCellValue("K{$row}", "=SUM(K5:K" . ($row - 1) . ")");
            $sheet->setCellValue("L{$row}", "=SUM(L5:L" . ($row - 1) . ")");
            $sheet->setCellValue("M{$row}", "=SUM(M5:M" . ($row - 1) . ")");
            $sheet->setCellValue("N{$row}", "=SUM(N5:N" . ($row - 1) . ")");
            $sheet->setCellValue("O{$row}", "—");
            $sheet->setCellValue("P{$row}", "—");

            $totRange = "A{$row}:P{$row}";
            $sheet->getStyle($totRange)->getFont()->setName('Arial')->setSize(9.5)->setBold(true)->getColor()->setARGB('FF0F2C4A');
            $sheet->getStyle($totRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFE2E8F0');
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("J{$row}:L{$row}")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle("M{$row}:N{$row}")->getNumberFormat()->setFormatCode('"Rp "#,##0');
            $sheet->getStyle($totRange)->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN)->getColor()->setARGB('FF64748B');
            $sheet->getStyle($totRange)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_DOUBLE)->getColor()->setARGB('FF0F2C4A');
            $sheet->getRowDimension($row)->setRowHeight(22);
        }

        $sheet->freezePane('A5');
    }

    private function buildSheetManifest($sheet, $pesertaList, string $periodeStr): void
    {
        $sheet->setTitle('Manifest Peserta');
        $sheet->setShowGridLines(true);

        $sheet->setCellValue('A1', 'PT NANTA EKSPEDISI NUSANTARA — DAFTAR MANIFEST PENUMPANG');
        $sheet->getStyle('A1')->getFont()->setName('Arial')->setSize(13)->setBold(true)->getColor()->setARGB('FF0F2C4A');

        $sheet->setCellValue('A2', "Periode: {$periodeStr} | Seluruh Penumpang Terdaftar pada Keberangkatan Terkonfirmasi");
        $sheet->getStyle('A2')->getFont()->setName('Arial')->setSize(10)->getColor()->setARGB('FF64748B');

        $headers = [
            'A4' => ['No', 6],
            'B4' => ['Kode Booking', 18],
            'C4' => ['Nama Lengkap Peserta', 26],
            'D4' => ['Kategori', 12],
            'E4' => ['Jenis ID', 10],
            'F4' => ['Nomor Identitas (NIK/Paspor)', 24],
            'G4' => ['No. HP Peserta', 16],
            'H4' => ['Paket Wisata', 30],
            'I4' => ['Tgl Berangkat', 16],
            'J4' => ['Nama Pemesan', 22],
            'K4' => ['Status Peserta', 16],
        ];

        foreach ($headers as $cell => [$label, $width]) {
            $sheet->setCellValue($cell, $label);
            $sheet->getColumnDimension(preg_replace('/[0-9]/', '', $cell))->setWidth($width);
        }

        $headerRange = 'A4:K4';
        $sheet->getStyle($headerRange)->getFont()->setName('Arial')->setSize(9.5)->setBold(true)->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle($headerRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF0F2C4A');
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle($headerRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setARGB('FF0F2C4A');
        $sheet->getRowDimension(4)->setRowHeight(25);

        $row = 5;
        foreach ($pesertaList as $idx => $ps) {
            $order = $ps->pemesanan;
            $jadwal = $order?->jadwal;
            $paket = $jadwal?->paket;

            $sheet->setCellValue("A{$row}", $idx + 1);
            $sheet->setCellValue("B{$row}", $order?->kode_pemesanan ?? '—');
            $sheet->setCellValue("C{$row}", $ps->nama_peserta);
            $sheet->setCellValue("D{$row}", ucfirst($ps->kategori ?? 'Dewasa'));
            $sheet->setCellValue("E{$row}", strtoupper($ps->jenis_identitas ?? 'KTP'));
            $sheet->setCellValue("F{$row}", " {$ps->no_identitas} ");
            $sheet->setCellValue("G{$row}", $ps->no_hp ?? $order?->user?->no_hp ?? '—');
            $sheet->setCellValue("H{$row}", $paket?->nama_paket ?? '—');
            $sheet->setCellValue("I{$row}", $jadwal?->tanggal_berangkat?->format('d/m/Y') ?? '—');
            $sheet->setCellValue("J{$row}", $order?->user?->nama_lengkap ?? '—');
            $sheet->setCellValue("K{$row}", ucfirst($ps->status ?? 'menunggu'));

            $sheet->getStyle("A{$row}:B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("C{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle("D{$row}:E{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("F{$row}:G{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle("I{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("J{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle("K{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            if ($idx % 2 == 1) {
                $sheet->getStyle("A{$row}:K{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF8FAFC');
            }

            $sheet->getStyle("A{$row}:K{$row}")->getFont()->setName('Arial')->setSize(9);
            $sheet->getStyle("A{$row}:K{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setARGB('FFE2E8F0');
            $sheet->getRowDimension($row)->setRowHeight(19);

            $row++;
        }

        $sheet->freezePane('A5');
    }
}
