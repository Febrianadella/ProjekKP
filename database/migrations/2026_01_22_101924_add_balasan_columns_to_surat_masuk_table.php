<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('surat_masuk', function (Blueprint $table) {
            if (!Schema::hasColumn('surat_masuk', 'no_surat_balasan')) {
                $table->string('no_surat_balasan')->nullable()->after('file_surat');
            }
            if (!Schema::hasColumn('surat_masuk', 'tanggal_balasan')) {
                $table->date('tanggal_balasan')->nullable()->after('no_surat_balasan');
            }
            if (!Schema::hasColumn('surat_masuk', 'tujuan_surat')) {
                $table->string('tujuan_surat')->nullable()->after('tanggal_balasan');
            }
            if (!Schema::hasColumn('surat_masuk', 'perihal_balasan')) {
                $table->text('perihal_balasan')->nullable()->after('tujuan_surat');
            }
            if (!Schema::hasColumn('surat_masuk', 'file_balasan')) {
                $table->string('file_balasan')->nullable()->after('perihal_balasan');
            }
            // Update status column if needed
            $table->enum('status', ['Belum Dibalas', 'Sudah Dibalas'])->default('Belum Dibalas')->change();
        });
    }

    public function down()
    {
        Schema::table('surat_masuk', function (Blueprint $table) {
            $table->dropColumn([
                'no_surat_balasan',
                'tanggal_balasan', 
                'tujuan_surat',
                'perihal_balasan',
                'file_balasan'
            ]);
            $table->string('status')->default('Belum Dibalas')->change();
        });
    }
};
