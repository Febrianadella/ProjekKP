<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Kolom yang boleh di-mass assign (create/update).
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',  // tambahkan role
    ];

    /**
     * Kolom yang disembunyikan saat serialisasi.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casting kolom.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    /**
     * Check if user is Admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is Pimpinan (read-only)
     */
    public function isPimpinan(): bool
    {
        return $this->role === 'pimpinan';
    }

    /**
     * Check if user is regular User
     */
    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    /**
     * Check if user can modify data (not pimpinan)
     */
    public function canModify(): bool
    {
        return !$this->isPimpinan();
    }
}
