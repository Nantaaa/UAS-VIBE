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
    public function buat(User $user, int $idJadwal, array $pesertaData, array $promo = [], ?int $dewasa = null, ?int $anak = null): Pemesanan
    {
        $jumlah = count($pesertaData);
        $dewasa = $dewasa ?? $jumlah;
        $anak = $anak ?? 0;

        if ($dewasa + $anak !== $jumlah) {
            throw ValidationException::withMessages([
                'jumlah_peserta' => 'Jumlah dewasa dan anak harus sama dengan jumlah data peserta.',
            ]);
        }

        if ($jumlah < 1) {
            throw ValidationException::withMessages(['peserta' => 'Minimal satu peserta harus diisi.']);
        }

        return DB::transaction(function () use ($user, $idJadwal, $pesertaData, $jumlah, $promo, $dewasa, $anak) {
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
            $tarifDewasa = (float) $paket->tarif;
            $tarifAnak = Pemesanan::tarifKategori($tarifDewasa, 'anak');
            $subtotal = ($dewasa * $tarifDewasa) + ($anak * $tarifAnak);
            $diskon = (float) ($promo['diskon'] ?? 0);
            $total = max(0, $subtotal - $diskon);

            $pemesanan = Pemesanan::create([
                'id_user' => $user->id,
                'id_jadwal' => $jadwal->id,
                'kode_pemesanan' => $this->generateKode(),
                'jumlah_peserta' => $jumlah,
                'jumlah_dewasa' => $dewasa,
                'jumlah_anak' => $anak,
                'total_bayar' => $total,
                'kode_promo' => $promo['promo']->kode ?? null,
                'diskon' => $diskon,
                'status_pemesanan' => 'menunggu_pembayaran',
                'catatan_status' => 'Selesaikan pembayaran dalam 1 jam dan unggah bukti transfer.',
                'tanggal_pemesanan' => now(),
                'batas_pembayaran' => now()->addMinutes(self::BATAS_PEMBAYARAN_MENIT),
            ]);

            foreach (array_values($pesertaData) as $idx => $p) {
                $path = $p['file_identitas']->store('identitas', 'local');

                Peserta::create([
                    'id_pemesanan' => $pemesanan->id,
                    'nama_peserta' => $p['nama_peserta'],
                    'kategori' => $p['kategori'] ?? ($idx < $dewasa ? 'dewasa' : 'anak'),
                    'tempat_lahir' => $p['tempat_lahir'] ?? null,
                    'tanggal_lahir' => $p['tanggal_lahir'] ?? null,
                    'jenis_kelamin' => $p['jenis_kelamin'] ?? null,
                    'jenis_identitas' => $p['jenis_identitas'],
                    'no_identitas' => $p['no_identitas'],
                    'no_hp' => $p['no_hp'] ?? null,
                    'email' => $p['email'] ?? null,
                    'alamat' => $p['alamat'] ?? null,
                    'kota' => $p['kota'] ?? null,
                    'provinsi' => $p['provinsi'] ?? null,
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
