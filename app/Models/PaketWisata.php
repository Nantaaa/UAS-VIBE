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
        'itinerary', 'durasi_hari', 'min_peserta', 'tipe_trip', 'include_penerbangan',
        'termasuk', 'tidak_termasuk', 'tarif', 'tarif_private', 'harga_asli',
        'foto', 'is_active', 'is_bestseller',
    ];

    protected $casts = [
        'min_peserta' => 'integer',
        'include_penerbangan' => 'boolean',
        'tarif' => 'decimal:2',
        'tarif_private' => 'decimal:2',
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
        return collect(preg_split('/
\n|
|\n/', (string) $this->fasilitas))
            ->map(fn ($f) => trim($f))
            ->filter()
            ->values()
            ->all();
    }

    public function minPeserta(): int
    {
        return max(1, (int) ($this->min_peserta ?? 1));
    }

    public function tarifPrivate(): float
    {
        if ($this->tarif_private && (float) $this->tarif_private > 0) {
            return (float) $this->tarif_private;
        }

        $tipe = strtolower(trim((string) $this->tipe_trip));
        if (str_contains($tipe, 'private') || str_contains($tipe, 'honeymoon')) {
            return (float) $this->tarif;
        }

        // Default Private Group: kenaikan 40% (carter armada & guide eksklusif)
        $raw = (float) $this->tarif * 1.40;
        return (float) (round($raw / 50000) * 50000);
    }

    public function isEksklusifPrivate(): bool
    {
        $tipe = strtolower(trim((string) $this->tipe_trip));
        return str_contains($tipe, 'private') || str_contains($tipe, 'honeymoon');
    }
}
