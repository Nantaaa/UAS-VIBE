<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayaran';

    protected $fillable = [
        'id_pemesanan', 'id_admin_verifikator', 'jumlah_bayar',
        'bukti_pembayaran', 'tanggal_upload', 'status_verifikasi',
    ];

    protected $casts = [
        'jumlah_bayar' => 'decimal:2',
        'tanggal_upload' => 'datetime',
    ];

    public function pemesanan()
    {
        return $this->belongsTo(Pemesanan::class, 'id_pemesanan');
    }

    public function verifikator()
    {
        return $this->belongsTo(User::class, 'id_admin_verifikator');
    }
}
