<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SuratMasuk extends Model
{
    use HasFactory;

    protected $table = 'surat_masuk';

    /**
     * Fields yang boleh di-mass-assign (update/create)
     */
    protected $fillable = [
        // Surat masuk
        'no_surat',
        'asal_surat',
        'perihal',
        'perihal_lainnya',
        'tanggal_surat',
        'file_surat',
        
        // Balasan surat keluar ← INI YANG PENTING!
        'no_surat_balasan',
        'tanggal_balasan',
        'tujuan_surat',
        'perihal_balasan',
        'perihal_lainnya_keluar',  // dari form JS
        'file_balasan',
        
        // Status
        'status',
    ];

    /**
     * Cast dates ke Carbon instance
     */
    protected $casts = [
        'tanggal_surat'    => 'date:Y-m-d',
        'tanggal_balasan'  => 'date:Y-m-d',
        'created_at'       => 'datetime',
        'updated_at'       => 'datetime',
    ];

    /**
     * Relationship ke SuratKeluar (kalau ada tabel terpisah)
     */
    public function balasan(): HasOne
    {
        return $this->hasOne(SuratKeluar::class, 'surat_masuk_id');
    }

    /**
     * Scope untuk search
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('asal_surat', 'like', "%{$search}%")
              ->orWhere('perihal', 'like', "%{$search}%")
              ->orWhere('no_surat', 'like', "%{$search}%")
              ->orWhere('no_surat_balasan', 'like', "%{$search}%");
        });
    }
}
