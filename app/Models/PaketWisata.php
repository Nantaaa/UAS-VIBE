<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaketWisata extends Model
{
    use HasFactory;

    protected $table = 'paket_wisata';

    protected $fillable = [
        'id_destinasi', 'nama_paket', 'deskripsi', 'highlight', 'fasilitas',
        'itinerary', 'durasi_hari', 'tipe_trip', 'tarif', 'harga_asli',
        'foto', 'is_active', 'is_bestseller',
    ];

    protected $casts = [
        'tarif' => 'decimal:2',
        'harga_asli' => 'decimal:2',
        'is_active' => 'boolean',
        'is_bestseller' => 'boolean',
    ];

    public function diskonPersen(): ?int
    {
        if (! $this->harga_asli || $this->harga_asli <= $this->tarif) {
            return null;
        }

        return (int) round((1 - ($this->tarif / $this->harga_asli)) * 100);
    }

    public function destinasi()
    {
        return $this->belongsTo(Destinasi::class, 'id_destinasi');
    }

    public function jadwal()
    {
        return $this->hasMany(JadwalKeberangkatan::class, 'id_paket');
    }

    public function pemesanan()
    {
        return $this->hasManyThrough(
            Pemesanan::class,
            JadwalKeberangkatan::class,
            'id_paket',
            'id_jadwal'
        );
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
