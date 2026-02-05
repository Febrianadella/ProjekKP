<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanSuratExport;

class LaporanController extends Controller
{
    /**
     * Display laporan with filters
     */
    public function index(Request $request)
    {
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalAkhir = $request->input('tanggal_akhir');
        $perihal = $request->input('perihal');
        $status = $request->input('status', 'semua');

        $query = DB::table('surat_masuk as sm');

        if ($tanggalMulai) {
            $query->whereDate('sm.tanggal_surat', '>=', $tanggalMulai);
        }
        if ($tanggalAkhir) {
            $query->whereDate('sm.tanggal_surat', '<=', $tanggalAkhir);
        }
        if ($perihal) {
            $query->where('sm.perihal', 'like', "%{$perihal}%");
        }
        if ($status !== 'semua') {
            if ($status === 'Sudah Dibalas') {
                $query->whereNotNull('sm.file_balasan');
            } else {
                $query->whereNull('sm.file_balasan');
            }
        }

        $surat = $query->orderByDesc('sm.tanggal_surat')->get();

        // Statistik
        $totalMasuk = DB::table('surat_masuk')->count();
        $totalKeluar = DB::table('surat_masuk')->whereNotNull('file_balasan')->count();
        $totalBelumDibalas = DB::table('surat_masuk')->whereNull('file_balasan')->count();

        return view('laporan', compact(
            'surat', 'totalMasuk', 'totalKeluar', 'totalBelumDibalas',
            'tanggalMulai', 'tanggalAkhir', 'perihal', 'status'
        ));
    }

    /**
     * Export filtered data to Excel
     */
    public function exportExcel(Request $request)
    {
        return Excel::download(
            new LaporanSuratExport($request->all()), 
            'laporan-surat-' . now()->format('Y-m-d-H-i-s') . '.xlsx'
        );
    }

    /**
     * Export filtered data to PDF
     */
    public function exportPdf(Request $request)
    {
        // Get all filters
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalAkhir = $request->input('tanggal_akhir');
        $perihal = $request->input('perihal');
        $status = $request->input('status', 'semua');

        // Same query logic as index method
        $query = DB::table('surat_masuk as sm');
        
        if ($tanggalMulai) {
            $query->whereDate('sm.tanggal_surat', '>=', $tanggalMulai);
        }
        if ($tanggalAkhir) {
            $query->whereDate('sm.tanggal_surat', '<=', $tanggalAkhir);
        }
        if ($perihal) {
            $query->where('sm.perihal', 'like', "%{$perihal}%");
        }
        if ($status !== 'semua') {
            if ($status === 'Sudah Dibalas') {
                $query->whereNotNull('sm.file_balasan');
            } else {
                $query->whereNull('sm.file_balasan');
            }
        }

        $surat = $query->orderByDesc('sm.tanggal_surat')->get();

        // Pass data to PDF view
        $pdf = Pdf::loadView('laporan.pdf', compact(
            'surat', 
            'tanggalMulai', 
            'tanggalAkhir', 
            'perihal', 
            'status'
        ))
        ->setPaper('a4', 'landscape')
        ->setOptions([
            'defaultFont' => 'Arial',
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true
        ]);

        return $pdf->download('laporan-surat-' . now()->format('Y-m-d-H-i-s') . '.pdf');
    }

    /**
     * Preview filtered data to PDF (inline).
     */
    public function previewPdf(Request $request)
    {
        // Get all filters
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalAkhir = $request->input('tanggal_akhir');
        $perihal = $request->input('perihal');
        $status = $request->input('status', 'semua');

        // Same query logic as index method
        $query = DB::table('surat_masuk as sm');

        if ($tanggalMulai) {
            $query->whereDate('sm.tanggal_surat', '>=', $tanggalMulai);
        }
        if ($tanggalAkhir) {
            $query->whereDate('sm.tanggal_surat', '<=', $tanggalAkhir);
        }
        if ($perihal) {
            $query->where('sm.perihal', 'like', "%{$perihal}%");
        }
        if ($status !== 'semua') {
            if ($status === 'Sudah Dibalas') {
                $query->whereNotNull('sm.file_balasan');
            } else {
                $query->whereNull('sm.file_balasan');
            }
        }

        $surat = $query->orderByDesc('sm.tanggal_surat')->get();

        $pdf = Pdf::loadView('laporan.pdf', compact(
            'surat',
            'tanggalMulai',
            'tanggalAkhir',
            'perihal',
            'status'
        ))
        ->setPaper('a4', 'landscape')
        ->setOptions([
            'defaultFont' => 'Arial',
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true
        ]);

        return $pdf->stream('laporan-surat-' . now()->format('Y-m-d-H-i-s') . '.pdf');
    }
}
