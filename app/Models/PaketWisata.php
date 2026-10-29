<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaketWisata extends Model
{
    use HasFactory;

    protected $table = 'paket_wisata';

    protected $fillable = [
        'id_destinasi', 'nama_paket', 'deskripsi', 'fasilitas',
        'itinerary', 'durasi_hari', 'tarif', 'foto', 'is_active',
    ];

    protected $casts = [
        'tarif' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function destinasi()
    {
        return $this->belongsTo(Destinasi::class, 'id_destinasi');
    }

    public function jadwal()
    {
        return $this->hasMany(JadwalKeberangkatan::class, 'id_paket');
    }

    public function jadwalTersedia()
    {
        return $this->jadwal()
            ->where('tanggal_berangkat', '>=', now()->toDateString())
            ->orderBy('tanggal_berangkat');
    }

    public function fasilitasList(): array
    {
        return collect(preg_split('/\r\n|\r|\n/', (string) $this->fasilitas))
            ->map(fn ($f) => trim($f))
            ->filter()
            ->values()
            ->all();
    }
}
