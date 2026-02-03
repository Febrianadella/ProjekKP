<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratKeluar extends Model
{
    use HasFactory;

    protected $table = 'surat_keluar';

    protected $fillable = [
        'surat_masuk_id',
        'no_surat',
        'tanggal_surat',
        'tujuan_surat',
        'perihal',
        'file_balasan',
    ];

    protected $casts = [
        'tanggal_surat' => 'date',
    ];

    public function suratMasuk()
    {
        return $this->belongsTo(SuratMasuk::class, 'surat_masuk_id');
    }
}
