<?php

namespace App\Console\Commands;

use App\Models\Pemesanan;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class KedaluwarsakanPemesanan extends Command
{
    protected $signature = 'pemesanan:kedaluwarsakan';

    protected $description = 'Ubah pemesanan yang melewati batas pembayaran menjadi kedaluwarsa dan lepas kuotanya';

    public function handle(): int
    {
        $daftar = Pemesanan::where('status_pemesanan', 'menunggu_pembayaran')
            ->where('batas_pembayaran', '<', now())
            ->get();

        $jumlah = 0;

        foreach ($daftar as $pemesanan) {
            DB::transaction(function () use ($pemesanan) {
                $pemesanan->update([
                    'status_pemesanan' => 'kedaluwarsa',
                    'catatan_status' => 'Batas waktu pembayaran 1 jam terlewati. Kuota dilepas otomatis.',
                ]);

                $pemesanan->jadwal->sinkronSisaKuota();
            });

            $jumlah++;
        }

        $this->info("{$jumlah} pemesanan ditandai kedaluwarsa.");

        return self::SUCCESS;
    }
}
