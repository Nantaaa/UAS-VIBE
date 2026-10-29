<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    protected $table = 'wishlist';

    protected $fillable = ['id_user', 'id_paket'];

    public function paket()
    {
        return $this->belongsTo(PaketWisata::class, 'id_paket');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
