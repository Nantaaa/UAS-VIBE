<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TitikJemput extends Model
{
    protected $table = 'titik_jemput';

    protected $fillable = ['id_jadwal', 'nama', 'jam', 'urutan', 'biaya_tambahan'];

    protected $casts = [
        'jam' => 'datetime:H:i',
        'biaya_tambahan' => 'decimal:2',
    ];

    public function jadwal()
    {
        return $this->belongsTo(JadwalKeberangkatan::class, 'id_jadwal');
    }

    public function labelLengkap(): string
    {
        $jam = $this->jam ? $this->jam->format('H:i') : null;
        $biaya = (float) $this->biaya_tambahan;

        return $this->nama
            .($jam ? ' · '.$jam : '')
            .($biaya > 0 ? ' · +Rp '.number_format($biaya, 0, ',', '.') : ' · gratis jemput');
    }
}
