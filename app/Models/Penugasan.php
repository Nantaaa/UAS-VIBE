<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penugasan extends Model
{
    use HasFactory;

    protected $table = 'penugasan';

    protected $fillable = ['id_jadwal', 'id_petugas', 'tanggal_tugas'];

    protected $casts = ['tanggal_tugas' => 'date'];

    public function jadwal()
    {
        return $this->belongsTo(JadwalKeberangkatan::class, 'id_jadwal');
    }

    public function petugas()
    {
        return $this->belongsTo(Petugas::class, 'id_petugas');
    }
}
