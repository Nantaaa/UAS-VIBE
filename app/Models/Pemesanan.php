<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemesanan extends Model
{
    use HasFactory;

    protected $table = 'pemesanan';

    public const STATUS_AKTIF = [
        'menunggu_pembayaran',
        'menunggu_verifikasi',
        'perlu_perbaikan',
        'terkonfirmasi',
    ];

    public const STATUS_LABEL = [
        'menunggu_pembayaran' => 'Menunggu Pembayaran',
        'menunggu_verifikasi' => 'Menunggu Verifikasi',
        'perlu_perbaikan' => 'Perlu Perbaikan',
        'terkonfirmasi' => 'Terkonfirmasi',
        'kedaluwarsa' => 'Kedaluwarsa',
        'dibatalkan' => 'Dibatalkan',
    ];

    public const STATUS_BADGE = [
        'menunggu_pembayaran' => 'warning',
        'menunggu_verifikasi' => 'info',
        'perlu_perbaikan' => 'danger',
        'terkonfirmasi' => 'success',
        'kedaluwarsa' => 'secondary',
        'dibatalkan' => 'secondary',
    ];

    protected $fillable = [
        'id_user', 'id_jadwal', 'kode_pemesanan', 'jumlah_peserta', 'total_bayar',
        'status_pemesanan', 'catatan_status', 'tanggal_pemesanan', 'batas_pembayaran',
        'kode_promo', 'diskon',
    ];

    protected $casts = [
        'total_bayar' => 'decimal:2',
        'diskon' => 'decimal:2',
        'tanggal_pemesanan' => 'datetime',
        'batas_pembayaran' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function jadwal()
    {
        return $this->belongsTo(JadwalKeberangkatan::class, 'id_jadwal');
    }

    public function peserta()
    {
        return $this->hasMany(Peserta::class, 'id_pemesanan');
    }

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class, 'id_pemesanan')->latest('tanggal_upload');
    }

    public function pembayaranTerakhir()
    {
        return $this->hasOne(Pembayaran::class, 'id_pemesanan')->latestOfMany('tanggal_upload');
    }

    public function tiket()
    {
        return $this->hasOne(ETicket::class, 'id_pemesanan');
    }

    public function labelStatus(): string
    {
        return self::STATUS_LABEL[$this->status_pemesanan] ?? $this->status_pemesanan;
    }

    public function badgeStatus(): string
    {
        return self::STATUS_BADGE[$this->status_pemesanan] ?? 'secondary';
    }

    public function bisaDibatalkan(): bool
    {
        return in_array($this->status_pemesanan, [
            'menunggu_pembayaran', 'menunggu_verifikasi', 'perlu_perbaikan',
        ], true);
    }

    public function sisaDetikPembayaran(): int
    {
        return max(0, now()->diffInSeconds($this->batas_pembayaran, false));
    }
}
