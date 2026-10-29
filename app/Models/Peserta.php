<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peserta extends Model
{
    use HasFactory;

    protected $table = 'peserta';

    protected $fillable = [
        'id_pemesanan', 'nama_peserta', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin',
        'jenis_identitas', 'no_identitas', 'no_hp', 'email', 'alamat', 'kota', 'provinsi',
        'file_identitas', 'status',
    ];

    protected $casts = ['tanggal_lahir' => 'date'];

    public function pemesanan()
    {
        return $this->belongsTo(Pemesanan::class, 'id_pemesanan');
    }
}
