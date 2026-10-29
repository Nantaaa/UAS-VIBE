<?php

namespace App\Services;

use App\Models\KodePromo;
use Illuminate\Validation\ValidationException;

class PromoService
{
    /**
     * Hitung diskon untuk subtotal tertentu.
     * Mengembalikan array ['promo' => KodePromo, 'diskon' => float, 'total' => float].
     *
     * @throws ValidationException bila kode tidak sah
     */
    public function hitung(?string $kode, float $subtotal): array
    {
        $kode = trim((string) $kode);

        if ($kode === '') {
            return ['promo' => null, 'diskon' => 0.0, 'total' => $subtotal];
        }

        $promo = KodePromo::whereRaw('UPPER(kode) = ?', [mb_strtoupper($kode)])->first();

        if (! $promo) {
            throw ValidationException::withMessages(['kode_promo' => 'Kode promo tidak ditemukan.']);
        }

        if (! $promo->aktif) {
            throw ValidationException::withMessages(['kode_promo' => 'Kode promo sudah tidak aktif.']);
        }

        if ($promo->berlaku_hingga && $promo->berlaku_hingga->isPast()) {
            throw ValidationException::withMessages(['kode_promo' => 'Kode promo sudah kedaluwarsa.']);
        }

        if ($promo->kuota !== null && $promo->terpakai >= $promo->kuota) {
            throw ValidationException::withMessages(['kode_promo' => 'Kuota kode promo sudah habis.']);
        }

        if ($subtotal < (float) $promo->min_transaksi) {
            throw ValidationException::withMessages([
                'kode_promo' => 'Minimal transaksi Rp '.number_format((float) $promo->min_transaksi, 0, ',', '.').' untuk kode ini.',
            ]);
        }

        $diskon = $promo->tipe === 'persen'
            ? $subtotal * ((float) $promo->nilai / 100)
            : (float) $promo->nilai;

        if ($promo->maks_diskon) {
            $diskon = min($diskon, (float) $promo->maks_diskon);
        }

        $diskon = min($diskon, $subtotal);

        return [
            'promo' => $promo,
            'diskon' => round($diskon, 2),
            'total' => round($subtotal - $diskon, 2),
        ];
    }

    public function tandaiTerpakai(?KodePromo $promo): void
    {
        $promo?->increment('terpakai');
    }
}
