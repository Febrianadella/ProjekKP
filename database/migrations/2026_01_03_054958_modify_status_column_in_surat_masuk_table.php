<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surat_masuk', function (Blueprint $table) {
            // sesuaikan dengan kebutuhan, misalnya VARCHAR(50)
            $table->string('status', 50)->change();
        });
    }

    public function down(): void
    {
        Schema::table('surat_masuk', function (Blueprint $table) {
            // kembalikan ke ukuran awal, misal 10 (cek skema lama)
            $table->string('status', 10)->change();
        });
    }
};
