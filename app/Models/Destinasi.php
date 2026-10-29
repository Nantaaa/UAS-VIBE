<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Destinasi extends Model
{
    use HasFactory;

    protected $table = 'destinasi';

    protected $fillable = ['nama_destinasi', 'lokasi', 'deskripsi', 'foto'];

    public function paket()
    {
        return $this->hasMany(PaketWisata::class, 'id_destinasi');
    }
}
