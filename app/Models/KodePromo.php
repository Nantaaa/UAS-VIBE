<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KodePromo extends Model
{
    protected $table = 'kode_promo';

    protected $fillable = [
        'kode', 'tipe', 'nilai', 'min_transaksi', 'maks_diskon',
        'kuota', 'terpakai', 'aktif', 'berlaku_hingga',
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
        'min_transaksi' => 'decimal:2',
        'maks_diskon' => 'decimal:2',
        'aktif' => 'boolean',
        'berlaku_hingga' => 'date',
    ];

    public function labelDiskon(): string
    {
        return $this->tipe === 'persen'
            ? rtrim(rtrim(number_format((float) $this->nilai, 2, ',', '.'), '0'), ',').'%'
            : 'Rp '.number_format((float) $this->nilai, 0, ',', '.');
    }
}
