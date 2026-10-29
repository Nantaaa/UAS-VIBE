<?php

namespace App\Services;

use App\Models\JadwalKeberangkatan;
use App\Models\Pemesanan;
use App\Models\Peserta;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class BookingService
{
    public const BATAS_PEMBAYARAN_MENIT = 60;

    /**
     * Buat pemesanan baru dengan penguncian baris jadwal agar dua pemesanan
     * bersamaan tidak bisa menembus sisa kuota.
     *
     * @param  array<int, array{nama_peserta:string, jenis_identitas:string, no_identitas:string, file_identitas:UploadedFile}>  $pesertaData
     */
    public function buat(User $user, int $idJadwal, array $pesertaData): Pemesanan
    {
        $jumlah = count($pesertaData);

        if ($jumlah < 1) {
            throw ValidationException::withMessages(['peserta' => 'Minimal satu peserta harus diisi.']);
        }

        return DB::transaction(function () use ($user, $idJadwal, $pesertaData, $jumlah) {
            /** @var JadwalKeberangkatan $jadwal */
            $jadwal = JadwalKeberangkatan::whereKey($idJadwal)->lockForUpdate()->firstOrFail();

            $terpakai = $jadwal->kuotaTerpakai();
            $sisa = $jadwal->kuota_peserta - $terpakai;

            if ($sisa < $jumlah) {
                throw ValidationException::withMessages([
                    'jumlah_peserta' => "Sisa kuota tidak mencukupi. Tersedia {$sisa} kursi.",
                ]);
            }

            $paket = $jadwal->paket;
            $total = $paket->tarif * $jumlah;

            $pemesanan = Pemesanan::create([
                'id_user' => $user->id,
                'id_jadwal' => $jadwal->id,
                'kode_pemesanan' => $this->generateKode(),
                'jumlah_peserta' => $jumlah,
                'total_bayar' => $total,
                'status_pemesanan' => 'menunggu_pembayaran',
                'catatan_status' => 'Selesaikan pembayaran dalam 1 jam dan unggah bukti transfer.',
                'tanggal_pemesanan' => now(),
                'batas_pembayaran' => now()->addMinutes(self::BATAS_PEMBAYARAN_MENIT),
            ]);

            foreach ($pesertaData as $p) {
                $path = $p['file_identitas']->store('identitas', 'local');

                Peserta::create([
                    'id_pemesanan' => $pemesanan->id,
                    'nama_peserta' => $p['nama_peserta'],
                    'jenis_identitas' => $p['jenis_identitas'],
                    'no_identitas' => $p['no_identitas'],
                    'file_identitas' => $path,
                    'status' => 'menunggu',
                ]);
            }

            $jadwal->sinkronSisaKuota();

            return $pemesanan;
        });
    }

    public function generateKode(): string
    {
        do {
            $kode = 'TRV-'.now()->format('ymd').'-'.Str::upper(Str::random(5));
        } while (Pemesanan::where('kode_pemesanan', $kode)->exists());

        return $kode;
    }

    public function batalkan(Pemesanan $pemesanan, ?string $alasan = null): void
    {
        if (! $pemesanan->bisaDibatalkan()) {
            throw ValidationException::withMessages([
                'status' => 'Pemesanan dengan status ini tidak dapat dibatalkan.',
            ]);
        }

        DB::transaction(function () use ($pemesanan, $alasan) {
            $pemesanan->update([
                'status_pemesanan' => 'dibatalkan',
                'catatan_status' => $alasan ?: 'Dibatalkan oleh wisatawan.',
            ]);

            $pemesanan->jadwal->sinkronSisaKuota();
        });
    }
}
