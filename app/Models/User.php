<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'nama_lengkap', 'email', 'password_hash', 'role', 'no_hp',
        'tanggal_lahir', 'jenis_kelamin', 'kewarganegaraan', 'no_identitas',
        'alamat', 'kota', 'kode_pos',
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'tanggal_lahir' => 'date',
            'password_hash' => 'hashed',
        ];
    }

    public function getAuthPasswordName(): string
    {
        return 'password_hash';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function pemesanan()
    {
        return $this->hasMany(Pemesanan::class, 'id_user');
    }
}
