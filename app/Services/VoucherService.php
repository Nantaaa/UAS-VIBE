<?php

namespace App\Services;

use App\Models\ETicket;
use App\Models\Pemesanan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class VoucherService
{
    public function terbitkan(Pemesanan $pemesanan): ETicket
    {
        $existing = $pemesanan->tiket;

        if ($existing) {
            return $existing;
        }

        $kodeQr = 'QR-'.$pemesanan->kode_pemesanan.'-'.Str::upper(Str::random(6));

        $tiket = ETicket::create([
            'id_pemesanan' => $pemesanan->id,
            'kode_qr' => $kodeQr,
            'tanggal_terbit' => now(),
        ]);

        $this->buatPdf($pemesanan, $tiket);

        return $tiket->fresh();
    }

    public function buatPdf(Pemesanan $pemesanan, ETicket $tiket): string
    {
        $pemesanan->loadMissing(['user', 'jadwal.paket.destinasi', 'peserta', 'pembayaranTerakhir']);

        $qrSvg = (string) QrCode::format('svg')->size(180)->margin(1)
            ->errorCorrection('H')
            ->generate($tiket->kode_qr);

        $qrBase64 = 'data:image/svg+xml;base64,'.base64_encode($qrSvg);

        $pdf = Pdf::loadView('pdf.voucher', [
            'pemesanan' => $pemesanan,
            'tiket' => $tiket,
            'qr' => $qrBase64,
        ])->setPaper('a4', 'portrait');

        $path = 'voucher/voucher-'.$pemesanan->kode_pemesanan.'.pdf';
        Storage::disk('local')->put($path, $pdf->output());

        $tiket->update(['file_pdf' => $path]);

        return $path;
    }

    public function validasi(string $kode): array
    {
        $kode = trim($kode);

        $pemesanan = Pemesanan::with(['user', 'jadwal.paket.destinasi', 'peserta', 'tiket'])
            ->where('kode_pemesanan', $kode)
            ->first();

        $tiket = $pemesanan?->tiket;

        if (! $pemesanan && $tiket === null) {
            $tiket = ETicket::with(['pemesanan.user', 'pemesanan.jadwal.paket.destinasi', 'pemesanan.peserta'])
                ->where('kode_qr', $kode)
                ->first();

            $pemesanan = $tiket?->pemesanan;
        }

        if (! $pemesanan) {
            return ['valid' => false, 'alasan' => 'Kode pemesanan tidak ditemukan pada sistem.', 'pemesanan' => null];
        }

        if ($pemesanan->status_pemesanan !== 'terkonfirmasi') {
            return [
                'valid' => false,
                'alasan' => 'Pemesanan belum terkonfirmasi (status: '.$pemesanan->labelStatus().').',
                'pemesanan' => $pemesanan,
            ];
        }

        return ['valid' => true, 'alasan' => 'Voucher asli dan pemesanan terkonfirmasi.', 'pemesanan' => $pemesanan];
    }
}
