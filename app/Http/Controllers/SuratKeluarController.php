<?php

namespace App\Http\Controllers;

use App\Models\SuratMasuk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class SuratKeluarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return redirect()->route('surat');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('surat-create-keluar');
    }

    /**
     * Store a newly created resource in storage.
     * POST /surat-keluar → route('surat-keluar.store')
     */
    public function store(Request $request)
    {
        // Delegate ke SuratController::storeKeluar
        return app(SuratController::class)->storeKeluar($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(SuratMasuk $surat_keluar)
    {
        return view('surat-keluar.show', ['surat' => $surat_keluar]);
    }

    /**
     * Show the form for editing the specified resource.
     * GET /surat-keluar/{id} → route('surat-keluar.edit')
     */
    public function edit(SuratMasuk $surat_keluar)
    {
        return view('surat-edit-keluar', ['surat' => $surat_keluar]);
    }

    /**
     * Update the specified resource in storage.
     * PUT /surat-keluar/{surat_keluar} → route('surat-keluar.update')
     */
    public function update(Request $request, SuratMasuk $surat_keluar)
    {
        Log::info('Update balasan request:', $request->all());
        Log::info('Surat Keluar ID:', ['id' => $surat_keluar->id]);

        $data = $request->validate([
            'no_surat'        => ['nullable', 'string', 'max:255'],
            'tanggal_surat'   => ['required', 'date'],
            'tujuan_surat'    => ['required', 'string', 'max:255'],
            'perihal'         => ['required', 'string'],
            'perihal_lainnya' => ['nullable', 'string'],
            'file_balasan'    => ['nullable', 'file', 'mimes:pdf,docx', 'max:2048'],
        ]);

        // Jika perihal adalah 'lainnya', gunakan perihal_lainnya
        $perihalFinal = ($data['perihal'] === 'lainnya' && !empty($data['perihal_lainnya']))
            ? $data['perihal_lainnya']
            : $data['perihal'];

        $filePath = $surat_keluar->file_balasan;

        if ($request->hasFile('file_balasan')) {
            // Hapus file lama
            if ($filePath) {
                Storage::disk('public')->delete($filePath);
            }
            $filePath = $request->file('file_balasan')->store('surat-keluar', 'public');
        }

        // Handle no_surat - jika kosong atau null, gunakan nilai lama atau auto-generate
        $noSurat = !empty($data['no_surat']) 
            ? $data['no_surat'] 
            : ($surat_keluar->no_surat_balasan ?? 'SK/' . str_pad($surat_keluar->id, 3, '0', STR_PAD_LEFT) . '/' . date('Y'));

        $surat_keluar->update([
            'no_surat_balasan' => $noSurat,
            'tanggal_balasan'  => $data['tanggal_surat'],
            'tujuan_surat'     => $data['tujuan_surat'],
            'perihal_balasan'  => $perihalFinal,
            'file_balasan'     => $filePath,
            'status'           => 'Sudah Dibalas',
        ]);

        return redirect()->route('surat')
            ->with('success', 'Balasan surat keluar berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SuratMasuk $surat_keluar)
    {
        // Hapus file kalau ada
        if ($surat_keluar->file_balasan) {
            Storage::disk('public')->delete($surat_keluar->file_balasan);
        }

        $surat_keluar->update([
            'no_surat_balasan' => null,
            'tanggal_balasan'  => null,
            'tujuan_surat'     => null,
            'perihal_balasan'  => null,
            'file_balasan'     => null,
            'status'           => 'Belum Dibalas',
        ]);

        return redirect()->route('surat')
            ->with('success', 'Balasan surat keluar berhasil dihapus.');
    }

    /**
     * Download file balasan.
     */
    public function download(SuratMasuk $surat_keluar)
    {
        if (!$surat_keluar->file_balasan) {
            abort(404, 'File tidak ditemukan');
        }

        return Storage::disk('public')->download($surat_keluar->file_balasan);
    }

    /**
     * Preview file balasan (inline).
     */
    public function preview(SuratMasuk $surat_keluar)
    {
        if (!$surat_keluar->file_balasan) {
            abort(404, 'File tidak ditemukan');
        }

        if (!Storage::disk('public')->exists($surat_keluar->file_balasan)) {
            abort(404, 'File tidak ditemukan');
        }

        return Storage::disk('public')->response(
            $surat_keluar->file_balasan,
            basename($surat_keluar->file_balasan),
            ['Content-Disposition' => 'inline; filename="' . basename($surat_keluar->file_balasan) . '"']
        );
    }
}