<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surat_masuk', function (Blueprint $table) {
            $table->id();

            // Data utama surat masuk
            $table->string('asal_surat');                 // instansi pengirim
            $table->string('perihal');                    // judul/perihal surat
            $table->date('tanggal_surat');                // tanggal pada surat

            // File surat masuk & file balasan (PDF)
            $table->string('file_surat')->nullable();     // file PDF surat masuk yang di-upload
            $table->string('file_balasan')->nullable();   // file PDF balasan surat keluar

            // Status surat masuk
            // Contoh nilai: "Belum Dibalas" atau "Sudah Dibalas"
            $table->string('status', 50)->default('Belum Dibalas');

            $table->timestamps();                         // created_at & updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_masuk');
    }
};
