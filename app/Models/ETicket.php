<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ETicket extends Model
{
    use HasFactory;

    protected $table = 'e_ticket';

    protected $fillable = ['id_pemesanan', 'kode_qr', 'file_pdf', 'tanggal_terbit'];

    protected $casts = ['tanggal_terbit' => 'datetime'];

    public function pemesanan()
    {
        return $this->belongsTo(Pemesanan::class, 'id_pemesanan');
    }
}
