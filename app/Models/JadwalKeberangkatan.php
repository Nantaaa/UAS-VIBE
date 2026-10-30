<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalKeberangkatan extends Model
{
    use HasFactory;

    protected $table = 'jadwal_keberangkatan';

    protected $fillable = [
        'id_paket', 'tanggal_berangkat', 'tanggal_kembali', 'kuota_peserta', 'sisa_kuota',
        'kota_kumpul', 'titik_kumpul', 'jam_kumpul', 'catatan_keberangkatan',
    ];

    protected $casts = [
        'jam_kumpul' => 'datetime:H:i',
        'tanggal_berangkat' => 'date',
        'tanggal_kembali' => 'date',
    ];

    public function paket()
    {
        return $this->belongsTo(PaketWisata::class, 'id_paket');
    }

    public function pemesanan()
    {
        return $this->hasMany(Pemesanan::class, 'id_jadwal');
    }

    public function penugasan()
    {
        return $this->hasMany(Penugasan::class, 'id_jadwal');
    }

    /**
     * Kuota terpakai = pemesanan aktif (menunggu pembayaran, menunggu verifikasi,
     * perlu perbaikan) + terkonfirmasi. Dibatalkan/kedaluwarsa tidak dihitung.
     */
    public function kuotaTerpakai(): int
    {
        return (int) $this->pemesanan()
            ->whereIn('status_pemesanan', Pemesanan::STATUS_AKTIF)
            ->sum('jumlah_peserta');
    }

    public function sinkronSisaKuota(): void
    {
        $this->sisa_kuota = max(0, $this->kuota_peserta - $this->kuotaTerpakai());
        $this->save();
    }
}
