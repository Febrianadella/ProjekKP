<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $now      = Carbon::now();
        $tahunIni = $now->year;
        $bulanIni = $now->month;

        /*
        |--------------------------------------------------------------------------
        | STAT KARTU ATAS / GLOBAL
        |--------------------------------------------------------------------------
        */

        // Total surat masuk (all time)
        $totalMasuk = DB::table('surat_masuk')->count();

        // Total surat keluar (balasan)
        $totalKeluar = DB::table('surat_masuk')
            ->whereNotNull('file_balasan')
            ->count();

        // Surat yang belum dibalas:
        // anggap "sudah dibalas" hanya ketika status = 'Sudah Dibalas'
        $belumDibalas = DB::table('surat_masuk')
            ->where(function ($q) {
                $q->whereNull('status')
                  ->orWhere('status', '!=', 'Sudah Dibalas');
            })
            ->count();

        // Surat masuk bulan ini (berdasarkan created_at)
        $suratBulanIni = DB::table('surat_masuk')
            ->whereYear('created_at', $tahunIni)
            ->whereMonth('created_at', $bulanIni)
            ->count();

        // --- Persentase dibanding bulan lalu ---

        $bulanLalu      = $bulanIni == 1 ? 12 : $bulanIni - 1;
        $tahunBulanLalu = $bulanIni == 1 ? $tahunIni - 1 : $tahunIni;

        $masukBulanLalu = DB::table('surat_masuk')
            ->whereYear('created_at', $tahunBulanLalu)
            ->whereMonth('created_at', $bulanLalu)
            ->count();

        $keluarBulanIni = DB::table('surat_masuk')
            ->whereYear('created_at', $tahunIni)
            ->whereMonth('created_at', $bulanIni)
            ->whereNotNull('file_balasan')
            ->count();

        $keluarBulanLalu = DB::table('surat_masuk')
            ->whereYear('created_at', $tahunBulanLalu)
            ->whereMonth('created_at', $bulanLalu)
            ->whereNotNull('file_balasan')
            ->count();

        $persenMasuk = $masukBulanLalu > 0
            ? round((($suratBulanIni - $masukBulanLalu) / $masukBulanLalu) * 100)
            : 0;

        $persenKeluar = $keluarBulanLalu > 0
            ? round((($keluarBulanIni - $keluarBulanLalu) / $keluarBulanLalu) * 100)
            : 0;

        /*
        |--------------------------------------------------------------------------
        | STAT KATEGORI PERIHAL (PKL, Kunjungan, Lainnya)
        |--------------------------------------------------------------------------
        */

        // surat dengan perihal persis "PKL"
        $totalPKL = DB::table('surat_masuk')
            ->where('perihal', 'PKL')
            ->count();

        // surat dengan perihal persis "Kunjungan"
        $totalKunjungan = DB::table('surat_masuk')
            ->where('perihal', 'Kunjungan')
            ->count();

        // sisanya dianggap kategori "Lainnya"
        $totalLainnya = DB::table('surat_masuk')
            ->whereNotIn('perihal', ['PKL', 'Kunjungan'])
            ->count(); // [web:89][web:103]

        /*
        |--------------------------------------------------------------------------
        | DATA CHART PER BULAN
        |--------------------------------------------------------------------------
        */

        // Surat masuk per bulan (tahun ini)
        $suratMasukPerBulan = DB::table('surat_masuk')
            ->selectRaw('MONTH(created_at) as bulan, COUNT(*) as total')
            ->whereYear('created_at', $tahunIni)
            ->groupBy('bulan')
            ->pluck('total', 'bulan');   // key = bulan, value = total

        // Surat keluar (balasan) per bulan (tahun ini)
        $suratKeluarPerBulan = DB::table('surat_masuk')
            ->selectRaw('MONTH(created_at) as bulan, COUNT(*) as total')
            ->whereYear('created_at', $tahunIni)
            ->whereNotNull('file_balasan')
            ->groupBy('bulan')
            ->pluck('total', 'bulan');

        // Array 12 bulan, default 0 kalau tidak ada data
        $labels     = [];
        $masukData  = [];
        $keluarData = [];

        for ($i = 1; $i <= 12; $i++) {
            $labels[]     = Carbon::create($tahunIni, $i)->format('M');
            $masukData[]  = $suratMasukPerBulan[$i]  ?? 0;
            $keluarData[] = $suratKeluarPerBulan[$i] ?? 0;
        } // [web:107][web:110]

        /*
        |--------------------------------------------------------------------------
        | SURAT MASUK TERBARU
        |--------------------------------------------------------------------------
        */

        $suratTerbaru = DB::table('surat_masuk')
            ->select('id', 'asal_surat', 'perihal', 'tanggal_surat', 'file_balasan', 'status', 'created_at')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | KIRIM DATA KE VIEW
        |--------------------------------------------------------------------------
        */

        return view('dashboard', compact(
            'totalMasuk',
            'totalKeluar',
            'belumDibalas',
            'suratBulanIni',
            'persenMasuk',
            'persenKeluar',
            'labels',
            'masukData',
            'keluarData',
            'suratTerbaru',
            'totalPKL',
            'totalKunjungan',
            'totalLainnya',
        )); // [web:108][web:111]
    }
}
