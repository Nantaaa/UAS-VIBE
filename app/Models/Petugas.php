<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Petugas extends Model
{
    use HasFactory;

    protected $table = 'petugas';

    protected $fillable = ['nama_petugas', 'kontak', 'peran'];

    public function penugasan()
    {
        return $this->hasMany(Penugasan::class, 'id_petugas');
    }
}
